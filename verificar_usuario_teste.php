<?php
require 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Verificando usuário 'teste' no banco:\n\n";

$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha, fg_ativo 
        FROM sysapp_config_user 
        WHERE ds_login = 'teste'";

$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);

if ($row) {
    print_r($row);
    
    echo "\n\nTestando findByLogin():\n";
    require_once 'models/Usuario.php';
    $Usuario = new Usuario();
    $user = $Usuario->findByLogin('teste');
    print_r($user);
    
    if ($user) {
        echo "\n\nTestando findForAuth():\n";
        $userAuth = $Usuario->findForAuth($user['cd_usuario']);
        print_r($userAuth);
    }
} else {
    echo "❌ Usuário não encontrado!\n";
}
