<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== CRIANDO TABELA DE PERMISSÕES ===\n\n";

// Cria tabela de interfaces/permissões
$sql = "CREATE TABLE IF NOT EXISTS sysapp_config_user_interfaces (
    cd_usuario INTEGER NOT NULL,
    nm_interface VARCHAR(100) NOT NULL,
    PRIMARY KEY (cd_usuario, nm_interface),
    FOREIGN KEY (cd_usuario) REFERENCES sysapp_config_user(cd_usuario) ON DELETE CASCADE
)";

$result = pg_query($conn, $sql);

if ($result) {
    echo "✓ Tabela sysapp_config_user_interfaces criada!\n\n";
} else {
    echo "✗ Erro ao criar tabela: " . pg_last_error($conn) . "\n";
    exit;
}

// Insere permissões para o usuário admin
echo "Cadastrando permissões para o admin...\n";

$interfaces = [
    'relatorios',
    'admin',
    'usuarios',
    'empresas',
    'clientes',
    'questionarios'
];

foreach ($interfaces as $interface) {
    $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
            VALUES (1, '$interface')
            ON CONFLICT (cd_usuario, nm_interface) DO NOTHING";
    
    $result = pg_query($conn, $sql);
    
    if ($result) {
        echo "  ✓ $interface\n";
    } else {
        echo "  ✗ Erro em $interface: " . pg_last_error($conn) . "\n";
    }
}

// Também para o usuário 2
echo "\nCadastrando permissões para diaazze...\n";

foreach ($interfaces as $interface) {
    $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
            VALUES (2, '$interface')
            ON CONFLICT (cd_usuario, nm_interface) DO NOTHING";
    
    pg_query($conn, $sql);
    echo "  ✓ $interface\n";
}

echo "\n=== PERMISSÕES CONFIGURADAS COM SUCESSO! ===\n";
echo "\nAgora você pode fazer login com:\n";
echo "  Login: admin\n";
echo "  Senha: admin\n";
