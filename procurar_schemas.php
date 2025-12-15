<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect();

echo "=== PROCURANDO TABELAS NOS SCHEMAS ===\n\n";

$tabelas = ['prc_pessoa', 'rc_lanc_cpl', 'glb_questionarios', 'glb_questionario_respostas'];

foreach ($tabelas as $tabela) {
    $sql = "SELECT table_schema 
            FROM information_schema.tables 
            WHERE table_name = '$tabela'";
    $result = pg_query($db->getConnection(), $sql);
    
    $schemas = [];
    while ($row = pg_fetch_assoc($result)) {
        $schemas[] = $row['table_schema'];
    }
    
    if (empty($schemas)) {
        echo "✗ $tabela: NÃO ENCONTRADA\n";
    } else {
        echo "✓ $tabela: " . implode(', ', $schemas) . "\n";
    }
}

echo "\n\n=== SCHEMAS DISPONÍVEIS NO BANCO ===\n\n";
$sql = "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT LIKE 'pg_%' AND schema_name != 'information_schema' ORDER BY schema_name";
$result = pg_query($db->getConnection(), $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "- " . $row['schema_name'] . "\n";
}

echo "\n\n=== SEARCH PATH ATUAL ===\n\n";
$sql = "SHOW search_path";
$result = pg_query($db->getConnection(), $sql);
$row = pg_fetch_assoc($result);
echo "Search path: " . $row['search_path'] . "\n";
