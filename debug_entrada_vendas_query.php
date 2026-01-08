<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== DEBUG: ENTRADA X VENDAS - PASSO A PASSO ===\n\n";

// Passo 1: Verificar vendas no período
echo "1. Vendas no período (01/10 a 07/10/2025):\n";
$sql = "SELECT COUNT(*) as total FROM dm_orcamento_vendas_consolidadas 
        WHERE DATE(dt_emi_pedido) >= '2025-10-01'::date
        AND DATE(dt_emi_pedido) <= '2025-10-07'::date";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total: {$row['total']} vendas\n\n";

// Passo 2: Vendas com match em dm_produto
echo "2. Vendas com match em dm_produto:\n";
$sql = "SELECT COUNT(*) as total 
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
        AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Com match: {$row['total']} vendas\n\n";

// Passo 3: Vendas com produtos que TÊM marca
echo "3. Vendas de produtos com marca:\n";
$sql = "SELECT COUNT(*) as total 
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
        AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date
        AND p.cd_marca IS NOT NULL";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Com marca: {$row['total']} vendas\n\n";

// Passo 4: Agrupar por marca
echo "4. Vendas agrupadas por marca (top 10):\n";
$sql = "SELECT 
            p.cd_marca,
            p.ds_marca,
            COUNT(*) as total_vendas,
            SUM(v.qtde_produto) as qtde_vendida
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
        AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date
        AND p.cd_marca IS NOT NULL
        GROUP BY p.cd_marca, p.ds_marca
        ORDER BY total_vendas DESC
        LIMIT 10";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "  [{$row['cd_marca']}] {$row['ds_marca']}: {$row['total_vendas']} vendas\n";
}

// Passo 5: Verificar estoque por marca
echo "\n5. Estoque por marca (10 primeiras):\n";
$sql = "SELECT 
            p.cd_marca,
            p.ds_marca,
            COUNT(*) as qtde_registros,
            SUM(est.qtde_estoque) as estoque_total
        FROM dm_estoque_atual est
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = est.cd_cpl_tamanho
        WHERE p.cd_marca IS NOT NULL
        AND est.ativo = 1
        GROUP BY p.cd_marca, p.ds_marca
        ORDER BY estoque_total DESC
        LIMIT 10";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo sprintf("  [%d] %s: %.0f unidades\n",
        $row['cd_marca'],
        $row['ds_marca'],
        $row['estoque_total']
    );
}

// Passo 6: Query SIMPLIFICADA (sem estoque)
echo "\n6. Query SIMPLIFICADA (vendas por marca - sem filtro de estoque):\n";
$sql = "
    WITH vendas AS (
        SELECT 
            p.cd_marca,
            v.cd_filial,
            COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida,
            COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0) as valor_vendido
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
          AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date
          AND p.cd_marca IS NOT NULL
        GROUP BY p.cd_marca, v.cd_filial
    )
    SELECT 
        vd.cd_marca,
        vd.cd_filial,
        vd.qtde_vendida,
        vd.valor_vendido
    FROM vendas vd
    ORDER BY qtde_vendida DESC
    LIMIT 10
";
$result = pg_query($conn, $sql);
$count = pg_num_rows($result);
echo "Registros retornados: $count\n";
if ($count > 0) {
    echo "Primeiros 10:\n";
    while ($row = pg_fetch_assoc($result)) {
        echo sprintf("  Marca %d, Filial %d: %.0f vendas, R$ %.2f\n",
            $row['cd_marca'],
            $row['cd_filial'],
            $row['qtde_vendida'],
            $row['valor_vendido']
        );
    }
}

// Passo 7: Query COMPLETA com estoque e CROSS JOIN
echo "\n7. Query COMPLETA (com CROSS JOIN e estoque):\n";
$sql = "
    WITH estoque_atual AS (
        SELECT 
            p.cd_marca,
            est.cd_filial,
            COALESCE(SUM(est.qtde_estoque), 0) as qtde_atual
        FROM dm_estoque_atual est
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = est.cd_cpl_tamanho
        WHERE p.cd_marca IS NOT NULL
          AND est.ativo = 1
        GROUP BY p.cd_marca, est.cd_filial
    ),
    vendas AS (
        SELECT 
            p.cd_marca,
            v.cd_filial,
            COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
          AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date
          AND p.cd_marca IS NOT NULL
        GROUP BY p.cd_marca, v.cd_filial
    ),
    marcas_distintas AS (
        SELECT DISTINCT cd_marca, ds_marca
        FROM dm_produto
        WHERE cd_marca IS NOT NULL AND ds_marca IS NOT NULL
        LIMIT 10
    ),
    filiais_ativas AS (
        SELECT cd_filial, COALESCE(nm_fant, rz_filial) as nm_filial
        FROM prc_filial
        WHERE sts_filial = 1
        LIMIT 5
    )
    SELECT 
        f.cd_filial,
        m.cd_marca,
        m.ds_marca,
        COALESCE(est.qtde_atual, 0) as estoque_atual,
        COALESCE(vd.qtde_vendida, 0) as qtde_vendida
    FROM filiais_ativas f
    CROSS JOIN marcas_distintas m
    LEFT JOIN estoque_atual est ON est.cd_marca = m.cd_marca AND est.cd_filial = f.cd_filial
    LEFT JOIN vendas vd ON vd.cd_marca = m.cd_marca AND vd.cd_filial = f.cd_filial
    WHERE COALESCE(est.qtde_atual, 0) > 0
    LIMIT 20
";
$result = pg_query($conn, $sql);
if ($result) {
    $count = pg_num_rows($result);
    echo "Registros retornados: $count\n";
    if ($count > 0) {
        echo "Primeiros 10:\n";
        $i = 0;
        while ($row = pg_fetch_assoc($result) && $i < 10) {
            echo sprintf("  Filial %d - %s: Estoque=%.0f, Vendas=%.0f\n",
                $row['cd_filial'],
                $row['ds_marca'],
                $row['estoque_atual'],
                $row['qtde_vendida']
            );
            $i++;
        }
    }
} else {
    echo "ERRO: " . pg_last_error($conn) . "\n";
}

pg_close($conn);
