<?php
/**
 * Verifica estrutura da tabela de empresas
 */

require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'systec', 5432);

echo "=== ESTRUTURA DA TABELA ===\n";
$sql = "SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_name = 'sysapp_config_empresas'
        ORDER BY ordinal_position";

$colunas = $db->fetchAll($sql);
foreach ($colunas as $col) {
    echo "{$col['column_name']} ({$col['data_type']})\n";
}

echo "\n=== DADOS DAS EMPRESAS ===\n";
$empresas = $db->fetchAll("SELECT * FROM sysapp_config_empresas");
foreach ($empresas as $emp) {
    echo "\n--- Empresa {$emp['cd_empresa']} ---\n";
    print_r($emp);
}
