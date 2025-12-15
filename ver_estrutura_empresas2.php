<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect();

echo "=== ESTRUTURA DA TABELA sysapp_config_empresas ===\n\n";
$sql = "SELECT column_name, data_type FROM information_schema.columns 
        WHERE table_name = 'sysapp_config_empresas' 
        ORDER BY ordinal_position";
$result = pg_query($db->getConnection(), $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "{$row['column_name']} ({$row['data_type']})\n";
}

echo "\n\n=== EMPRESAS CADASTRADAS ===\n\n";
$sql = "SELECT * FROM sysapp_config_empresas ORDER BY cd_empresa LIMIT 3";
$result = pg_query($db->getConnection(), $sql);

while ($row = pg_fetch_assoc($result)) {
    print_r($row);
    echo "\n---\n\n";
}
