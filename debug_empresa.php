<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

$empresas = $db->fetchAll('SELECT * FROM sysapp_config_empresas ORDER BY cd_empresa');

echo 'Total de empresas: ' . count($empresas) . "\n";

foreach ($empresas as $emp) {
    echo $emp['cd_empresa'] . ' - ' . $emp['nm_empresa'] . ' (' . $emp['ds_banco'] . ")\n";
}

// Verificar especificamente a empresa ID 2
$result = $db->fetchAll('SELECT * FROM sysapp_config_empresas WHERE cd_empresa = 2');
if (empty($result)) {
    echo "\nEmpresa ID 2 (Agape) NÃO encontrada no banco!\n";
} else {
    echo "\nEmpresa ID 2 encontrada: " . $result[0]['nm_empresa'] . "\n";
}
