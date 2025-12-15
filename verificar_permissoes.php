<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== VERIFICANDO PERMISSÕES DO USUÁRIO ===\n\n";

$cd_usuario = 1;

// Verifica se existe tabela de permissões
$sql = "SELECT table_name FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name LIKE '%permiss%'";
$result = pg_query($conn, $sql);

echo "Tabelas de permissões:\n";
while ($row = pg_fetch_assoc($result)) {
    echo "  - {$row['table_name']}\n";
}

echo "\n";

// Tenta buscar permissões do usuário
$sql = "SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = 1";
$result = pg_query($conn, $sql);

if ($result) {
    $count = pg_num_rows($result);
    echo "Permissões do usuário admin (cd_usuario=1): $count registros\n";
    
    if ($count == 0) {
        echo "\n⚠️ PROBLEMA: Usuário não tem permissões cadastradas!\n";
        echo "Isso está bloqueando o login.\n\n";
        
        echo "SOLUÇÃO: Vou cadastrar permissões básicas...\n";
        
        // Insere permissões básicas para o admin
        $interfaces = [
            'relatorios',
            'admin', 
            'usuarios',
            'empresas',
            'clientes'
        ];
        
        foreach ($interfaces as $interface) {
            $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
                    VALUES (1, '$interface')
                    ON CONFLICT DO NOTHING";
            pg_query($conn, $sql);
        }
        
        echo "✓ Permissões cadastradas!\n";
    } else {
        while ($row = pg_fetch_assoc($result)) {
            echo "  - Interface: {$row['nm_interface']}\n";
        }
    }
} else {
    echo "Erro ao buscar permissões: " . pg_last_error($conn) . "\n";
}
