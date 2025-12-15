<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== ESTRUTURA DA TABELA ===\n\n";

$sql = "SELECT column_name FROM information_schema.columns 
        WHERE table_name = 'sysapp_config_user' 
        ORDER BY ordinal_position";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "- " . $row['column_name'] . "\n";
}

echo "\n=== USUÁRIOS ATUAIS ===\n\n";

$sql = "SELECT * FROM sysapp_config_user ORDER BY cd_usuario";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "ID: {$row['cd_usuario']}\n";
    echo "Nome: {$row['nm_usuario']}\n";
    echo "Email: {$row['ds_email']}\n";
    echo "Login: " . ($row['ds_login'] ?? 'NULL') . "\n";
    echo "Senha: {$row['ds_senha']}\n";
    echo "Ativo: {$row['fg_ativo']}\n";
    echo "---\n";
}

echo "\n=== ATUALIZANDO USUÁRIO ADMIN ===\n\n";

// Atualiza o usuário admin para ter ds_login = 'admin'
$sql = "UPDATE sysapp_config_user SET ds_login = 'admin' WHERE cd_usuario = 1";
$result = pg_query($conn, $sql);

if ($result) {
    echo "✓ Usuário atualizado! Agora você pode logar com:\n";
    echo "  Login: admin\n";
    echo "  Senha: admin\n";
} else {
    echo "✗ Erro ao atualizar: " . pg_last_error($conn) . "\n";
}

// Verifica se ds_login existe
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email FROM sysapp_config_user WHERE cd_usuario = 1";
$result = pg_query($conn, $sql);
$user = pg_fetch_assoc($result);

echo "\nDados finais:\n";
print_r($user);
