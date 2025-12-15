<?php
require 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Teste direto do findForAuth:\n\n";

$cd_usuario = 2;

$sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_senha as senha_usuario 
        FROM sysapp_config_user 
        WHERE cd_usuario = $cd_usuario AND fg_ativo = 'S'";

echo "SQL: $sql\n\n";

$result = pg_query($conn, $sql);
$usuario = pg_fetch_assoc($result);

echo "Resultado:\n";
print_r($usuario);

echo "\n\nTestando model Usuario:\n";
require_once 'models/Usuario.php';
$Usuario = new Usuario();
$user2 = $Usuario->findForAuth(2);
print_r($user2);
