<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Adicionando permissões para usuário diaazze (CD=6) ===\n\n";

$cd_usuario = 6;

// Remover permissões antigas
$db->query("DELETE FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario");

// Adicionar novas permissões
$permissoes = ['admin', 'clientes', 'questionarios', 'usuarios', 'relatorios'];

foreach ($permissoes as $nm_interface) {
    $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) VALUES ($cd_usuario, '$nm_interface')";
    $result = $db->query($sql);
    echo ($result ? "✓" : "✗") . " $nm_interface\n";
}

echo "\n=== Verificação ===\n";
$verifica = $db->fetchAll("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario");
echo "Total de permissões: " . count($verifica) . "\n";
print_r($verifica);
