<?php
require 'config/database.php';
$db = Database::getInstance()->getConnection();
$result = pg_query($db, "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'sysapp_config_empresas' ORDER BY ordinal_position");

echo "=== COLUNAS DA TABELA sysapp_config_empresas ===\n\n";
while($row = pg_fetch_assoc($result)) {
    echo sprintf("%-30s %s\n", $row['column_name'], '(' . $row['data_type'] . ')');
}

echo "\n=== DADOS DA TABELA ===\n\n";
$result2 = pg_query($db, "SELECT * FROM sysapp_config_empresas LIMIT 2");
while($row = pg_fetch_assoc($result2)) {
    print_r($row);
    echo "\n";
}
