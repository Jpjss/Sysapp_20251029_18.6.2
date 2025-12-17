<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Usuario.php';

$db = Database::getInstance();

echo "=== Verificando Usuário ID 4 ===\n";
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email FROM sysapp_config_user WHERE cd_usuario = 4";
$user = $db->fetchOne($sql);
print_r($user);

echo "\n=== Testando Usuario::findByLogin('testeusuario') ===\n";
$usuarioModel = new Usuario();
$found = $usuarioModel->findByLogin('testeusuario');
if ($found) {
    echo "Encontrado via Model: " . $found['cd_usuario'] . "\n";
} else {
    echo "NÃO encontrado via Model.\n";
}
