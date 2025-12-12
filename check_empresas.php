<?php
require_once 'config/database.php';

$db = Database::getInstance();

echo "Banco conectado: " . $db->getDatabase() . "\n";

$empresas = $db->fetchAll("SELECT * FROM sysapp_config_empresas");

echo "Total de empresas: " . count($empresas) . "\n";

if (!empty($empresas)) {
    foreach ($empresas as $emp) {
        echo "- {$emp['cd_empresa']}: {$emp['nome_empresa']} ({$emp['nome_banco']})\n";
    }
}
?>
