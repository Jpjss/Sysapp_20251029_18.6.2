<?php
require_once __DIR__ . '/config/database.php';

echo "=== VERIFICANDO BANCOS DISPONÍVEIS ===\n\n";

$db = Database::getInstance();
$db->connect();
$conn = $db->getConnection();

// Lista bancos disponíveis
$sql = "SELECT datname FROM pg_database WHERE datistemplate = false ORDER BY datname";
$result = pg_query($conn, $sql);

echo "Bancos disponíveis:\n";
while ($row = pg_fetch_assoc($result)) {
    echo "- " . $row['datname'] . "\n";
}

echo "\n\n=== VERIFICANDO BANCO ATUAL (sysapp) ===\n\n";

// Lista tabelas do banco sysapp
$sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename";
$result = pg_query($conn, $sql);

echo "Tabelas em 'sysapp':\n";
$count = 0;
while ($row = pg_fetch_assoc($result)) {
    echo "- " . $row['tablename'] . "\n";
    $count++;
    if ($count > 20) {
        echo "... (mais tabelas)\n";
        break;
    }
}

// Buscar empresas cadastradas
echo "\n\n=== EMPRESAS CADASTRADAS ===\n\n";
$sql = "SELECT cd_empresa, nm_empresa, nm_banco, ds_host, nr_porta FROM sysapp_config_empresas ORDER BY nm_empresa";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {
        echo "ID: {$row['cd_empresa']}\n";
        echo "Nome: {$row['nm_empresa']}\n";
        echo "Banco: {$row['nm_banco']}\n";
        echo "Host: {$row['ds_host']}\n";
        echo "Porta: {$row['nr_porta']}\n";
        echo "---\n";
    }
} else {
    echo "Nenhuma empresa cadastrada.\n";
}
