<?php
require_once 'config/config.php';
require_once 'config/database.php';
$db = Database::getInstance();
$u = $db->fetchOne('SELECT ds_senha FROM sysapp_config_user WHERE cd_usuario = 4');
echo "Senha no banco: " . $u['ds_senha'] . "\n";
