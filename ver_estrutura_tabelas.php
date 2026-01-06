<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== ESTRUTURA DAS TABELAS ===\n\n";

// 1. prc_filial
echo "1. Tabela prc_filial:\n";
$sql = "SELECT column_name, data_type FROM information_schema.columns 
        WHERE table_name = 'prc_filial' ORDER BY ordinal_position";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "  - {$row['column_name']} ({$row['data_type']})\n";
}

// 2. dm_produto
echo "\n2. Tabela dm_produto (primeiras 10 colunas):\n";
$sql = "SELECT column_name, data_type FROM information_schema.columns 
        WHERE table_name = 'dm_produto' ORDER BY ordinal_position LIMIT 10";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "  - {$row['column_name']} ({$row['data_type']})\n";
}

// 3. Verificar se há produtos com marca
echo "\n3. Produtos com marca:\n";
$sql = "SELECT COUNT(*) as total FROM dm_produto WHERE cd_marca IS NOT NULL";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total de produtos com marca: {$row['total']}\n";

// 4. Ver algumas marcas
echo "\n4. Marcas existentes (10 primeiras):\n";
$sql = "SELECT DISTINCT cd_marca, ds_marca FROM dm_produto 
        WHERE cd_marca IS NOT NULL AND ds_marca IS NOT NULL 
        ORDER BY ds_marca LIMIT 10";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "  - [{$row['cd_marca']}] {$row['ds_marca']}\n";
}

// 5. Verificar vendas com produtos que têm marca
echo "\n5. Vendas com produtos que têm marca (período 01/01 a 06/01):\n";
$sql = "
    SELECT COUNT(*) as total
    FROM dm_orcamento_vendas_consolidadas v
    WHERE EXISTS (
        SELECT 1 FROM dm_produto p 
        WHERE p.cd_cpl_tamanho = v.cd_cpl_tamanho 
        AND p.cd_marca IS NOT NULL
    )
    AND DATE(v.dt_emi_pedido) >= '2026-01-01'::date
    AND DATE(v.dt_emi_pedido) <= '2026-01-06'::date
";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total de vendas: {$row['total']}\n";

pg_close($conn);
