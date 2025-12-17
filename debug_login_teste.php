<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== USUÁRIO ID 4 (teste) - DADOS COMPLETOS ===\n\n";

$result = pg_query($conn, "SELECT * FROM sysapp_config_user WHERE cd_usuario = 4");
$user = pg_fetch_assoc($result);

print_r($user);

echo "\n\n=== TESTANDO findByLogin('teste') ===\n\n";

// Query que o modelo usa
$login = strtolower('teste');
$sql = "SELECT cd_usuario 
        FROM sysapp_config_user 
        WHERE LOWER(ds_email) = '$login' 
           OR LOWER(ds_login) = '$login' 
           OR LOWER(nm_usuario) = '$login'";

echo "SQL: $sql\n\n";

$result = pg_query($conn, $sql);
$found = pg_fetch_assoc($result);

if ($found) {
    echo "✅ Encontrou: ID " . $found['cd_usuario'] . "\n";
} else {
    echo "❌ Não encontrou\n";
}

// Testa com email completo
echo "\n\n=== TESTANDO findByLogin('teste@sys.io') ===\n\n";

$login = strtolower('teste@sys.io');
$sql = "SELECT cd_usuario 
        FROM sysapp_config_user 
        WHERE LOWER(ds_email) = '$login' 
           OR LOWER(ds_login) = '$login' 
           OR LOWER(nm_usuario) = '$login'";

echo "SQL: $sql\n\n";

$result = pg_query($conn, $sql);
$found = pg_fetch_assoc($result);

if ($found) {
    echo "✅ Encontrou: ID " . $found['cd_usuario'] . "\n";
} else {
    echo "❌ Não encontrou\n";
}
