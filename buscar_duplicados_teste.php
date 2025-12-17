<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== PROCURANDO USUÁRIOS COM LOGIN/EMAIL/NOME = 'teste' ===\n\n";

$result = pg_query($conn, "
    SELECT cd_usuario, nm_usuario, ds_login, ds_email  
    FROM sysapp_config_user 
    WHERE LOWER(ds_email) = 'teste' 
       OR LOWER(ds_login) = 'teste' 
       OR LOWER(nm_usuario) = 'teste'
    ORDER BY cd_usuario
");

if (pg_num_rows($result) > 0) {
    while ($user = pg_fetch_assoc($result)) {
        echo "ID: " . $user['cd_usuario'] . "\n";
        echo "Nome: " . $user['nm_usuario'] . "\n";
        echo "Login: " . $user['ds_login'] . "\n";
        echo "Email: " . $user['ds_email'] . "\n";
        echo "---\n";
    }
    
    echo "\n\nTotal: " . pg_num_rows($result) . " usuários encontrados\n";
    
    if (pg_num_rows($result) > 1) {
        echo "\n❌ PROBLEMA: Há mais de um usuário com 'teste'!\n";
    }
} else {
    echo "❌ Nenhum usuário encontrado!\n";
}
