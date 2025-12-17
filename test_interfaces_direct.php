<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "Banco conectado: " . $db->getDatabase() . "\n\n";

$sql = "SELECT cd_interface, nm_interface FROM sysapp_interfaces ORDER BY cd_interface";
$result = $db->fetchAll($sql);

echo "Total de interfaces: " . count($result) . "\n";
print_r($result);

// Testa a query exata que o model usa
echo "\n=== Testando query do model ===\n";
$sql2 = "SELECT cd_interface, nm_interface as nome_interface 
                FROM sysapp_interfaces 
                ORDER BY nm_interface";
$result2 = $db->fetchAll($sql2);
echo "Total com alias: " . count($result2) . "\n";
print_r($result2);
