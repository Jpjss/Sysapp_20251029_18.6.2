<?php
$conn = pg_connect('host=localhost port=5432 dbname=sysapp user=postgres password=postgres');
$res = pg_query($conn, "SELECT column_name FROM information_schema.columns WHERE table_name = 'sysapp_config_empresas' ORDER BY ordinal_position");
echo "Colunas da tabela sysapp_config_empresas:\n";
while ($row = pg_fetch_assoc($res)) {
    echo "- " . $row['column_name'] . "\n";
}
?>
