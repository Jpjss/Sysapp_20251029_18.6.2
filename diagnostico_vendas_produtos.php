<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== DIAGNÓSTICO: POR QUE VENDAS RETORNAM 0? ===\n\n";

// 1. Total de vendas no período
echo "1. Total de vendas no período (01/01 a 06/01):\n";
$sql = "SELECT COUNT(*) as total FROM dm_orcamento_vendas_consolidadas 
        WHERE DATE(dt_emi_pedido) >= '2026-01-01'::date
        AND DATE(dt_emi_pedido) <= '2026-01-06'::date";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total geral: {$row['total']}\n";

// 2. Ver alguns cd_cpl_tamanho de vendas
echo "\n2. cd_cpl_tamanho das vendas (10 primeiros):\n";
$sql = "SELECT DISTINCT cd_cpl_tamanho FROM dm_orcamento_vendas_consolidadas 
        WHERE DATE(dt_emi_pedido) >= '2026-01-01'::date
        AND DATE(dt_emi_pedido) <= '2026-01-06'::date
        LIMIT 10";
$result = pg_query($conn, $sql);
$vendas_cpl = [];
while ($row = pg_fetch_assoc($result)) {
    $vendas_cpl[] = $row['cd_cpl_tamanho'];
    echo "  - {$row['cd_cpl_tamanho']}\n";
}

// 3. Ver se esses cd_cpl_tamanho existem em dm_produto
if (!empty($vendas_cpl)) {
    echo "\n3. Verificando se esses cd_cpl_tamanho existem em dm_produto:\n";
    $cpl_list = implode(',', $vendas_cpl);
    $sql = "SELECT cd_cpl_tamanho, cd_marca, ds_marca 
            FROM dm_produto 
            WHERE cd_cpl_tamanho IN ($cpl_list)";
    $result = pg_query($conn, $sql);
    $found = 0;
    while ($row = pg_fetch_assoc($result)) {
        $found++;
        echo sprintf("  ✅ cd_cpl_tamanho %d encontrado - Marca: [%s] %s\n",
            $row['cd_cpl_tamanho'],
            $row['cd_marca'] ?? 'NULL',
            $row['ds_marca'] ?? 'SEM MARCA'
        );
    }
    
    if ($found == 0) {
        echo "  ❌ NENHUM cd_cpl_tamanho das vendas foi encontrado em dm_produto!\n";
    }
}

// 4. Verificar se dm_produto tem os mesmos cd_cpl_tamanho
echo "\n4. Verificando overlap entre vendas e produtos:\n";
$sql = "
    SELECT COUNT(DISTINCT v.cd_cpl_tamanho) as total_vendas,
           COUNT(DISTINCT p.cd_cpl_tamanho) as total_match
    FROM dm_orcamento_vendas_consolidadas v
    LEFT JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
    WHERE DATE(v.dt_emi_pedido) >= '2026-01-01'::date
      AND DATE(v.dt_emi_pedido) <= '2026-01-06'::date
";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "cd_cpl_tamanho únicos em vendas: {$row['total_vendas']}\n";
echo "cd_cpl_tamanho com match em dm_produto: {$row['total_match']}\n";

// 5. Exemplo de venda sem match
echo "\n5. Exemplo de venda SEM match em dm_produto:\n";
$sql = "
    SELECT v.cd_cpl_tamanho, v.cd_pedido, v.dt_emi_pedido
    FROM dm_orcamento_vendas_consolidadas v
    LEFT JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
    WHERE DATE(v.dt_emi_pedido) >= '2026-01-01'::date
      AND DATE(v.dt_emi_pedido) <= '2026-01-06'::date
      AND p.cd_cpl_tamanho IS NULL
    LIMIT 5
";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo sprintf("  - Pedido %d: cd_cpl_tamanho %d (Data: %s)\n",
        $row['cd_pedido'],
        $row['cd_cpl_tamanho'],
        $row['dt_emi_pedido']
    );
}

pg_close($conn);
