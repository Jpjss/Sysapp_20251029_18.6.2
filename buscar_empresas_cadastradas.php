<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "=== BUSCAR EMPRESAS CADASTRADAS ===\n\n";

$db = Database::getInstance();
// Conectar no banco central (bd_propasso)
$conn = @pg_connect("host=banco.propasso.systec.ftp.sh port=5432 dbname=bd_propasso user=usr_propasso password=senhapropassosystec123");

if (!$conn) {
    die("Erro ao conectar no bd_propasso\n");
}

echo "✅ Conectado em bd_propasso\n\n";

// Procurar tabelas que possam conter empresas
echo "Procurando tabelas de empresas...\n";
$sql = "SELECT tablename FROM pg_tables 
        WHERE schemaname = 'public' 
        AND (tablename LIKE '%empresa%' OR tablename LIKE '%cliente%' OR tablename LIKE '%filial%' OR tablename LIKE '%banco%')
        ORDER BY tablename";
$result = pg_query($conn, $sql);

echo "Tabelas encontradas:\n";
while ($row = pg_fetch_assoc($result)) {
    echo "  - {$row['tablename']}\n";
}

// Verificar prc_filial
echo "\n=== Conteúdo de prc_filial ===\n";
$sql = "SELECT cd_filial, rz_filial, nm_fant, cgc_filial, sts_filial 
        FROM prc_filial 
        ORDER BY cd_filial 
        LIMIT 20";
$result = pg_query($conn, $sql);
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $status = $row['sts_filial'] == 1 ? '✅' : '❌';
        echo sprintf("%s Filial %d: %s (CGC: %s)\n",
            $status,
            $row['cd_filial'],
            $row['nm_fant'] ?? $row['rz_filial'],
            $row['cgc_filial']
        );
    }
}

pg_close($conn);
