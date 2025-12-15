<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== CONFIGURANDO USUÁRIO DIAAZZE ===\n\n";

// Vincula o usuário diaazze (ID 2) à Empresa Padrão (ID 1)
$sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
        VALUES (2, 1)
        ON CONFLICT (cd_usuario, cd_empresa) DO NOTHING";

$result = pg_query($conn, $sql);

if ($result) {
    echo "✓ Usuário diaazze vinculado à Empresa Padrão (ID 1)\n\n";
} else {
    echo "✗ Erro: " . pg_last_error($conn) . "\n";
    exit;
}

// Verifica as empresas do usuário
$sql = "SELECT u.nm_usuario, e.cd_empresa, e.nm_empresa
        FROM sysapp_config_user u
        INNER JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        INNER JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
        WHERE u.cd_usuario = 2";

$result = pg_query($conn, $sql);

echo "Empresas vinculadas ao usuário diaazze:\n";
while ($row = pg_fetch_assoc($result)) {
    echo "  ✓ {$row['nm_empresa']} (ID: {$row['cd_empresa']})\n";
}

echo "\n=== CONFIGURAÇÃO CONCLUÍDA! ===\n";
echo "\nAgora:\n";
echo "1. Feche TODAS as abas do navegador (para limpar a sessão)\n";
echo "2. Abra novamente: http://localhost:8000\n";
echo "3. Faça login com:\n";
echo "   Login: diaazze\n";
echo "   Senha: c6WUh^xH3H5gH64r2iOIPtHXHVAvRA\n";
