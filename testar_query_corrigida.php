<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== TESTE: QUERY CORRIGIDA (BASEADA EM VENDAS) ===\n\n";

$sql = "
    WITH vendas_periodo AS (
        SELECT 
            p.cd_marca,
            p.ds_marca,
            v.cd_filial,
            COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida,
            COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0) as valor_vendido,
            COALESCE(AVG(v.vl_tot_it / NULLIF(v.qtde_produto, 0)), 0) as preco_medio
        FROM dm_orcamento_vendas_consolidadas v
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
        WHERE DATE(v.dt_emi_pedido) >= '2025-10-01'::date
          AND DATE(v.dt_emi_pedido) <= '2025-10-07'::date
          AND p.cd_marca IS NOT NULL
          AND p.ds_marca IS NOT NULL
        GROUP BY p.cd_marca, p.ds_marca, v.cd_filial
    ),
    estoque_atual AS (
        SELECT 
            p.cd_marca,
            est.cd_filial,
            COALESCE(SUM(est.qtde_estoque), 0) as qtde_atual,
            COALESCE(AVG(est.vlr_custo), 0) as preco_custo_medio
        FROM dm_estoque_atual est
        INNER JOIN dm_produto p ON p.cd_cpl_tamanho = est.cd_cpl_tamanho
        WHERE p.cd_marca IS NOT NULL
          AND est.ativo = 1
        GROUP BY p.cd_marca, est.cd_filial
    ),
    filiais_ativas AS (
        SELECT cd_filial, 
               COALESCE(nm_fant, rz_filial, 'Filial ' || cd_filial) as nm_filial
        FROM prc_filial
        WHERE sts_filial = 1
    )
    SELECT 
        f.nm_filial,
        f.cd_filial,
        vd.ds_marca as nm_marca,
        vd.cd_marca,
        COALESCE(est.qtde_atual, 0) as estoque_atual,
        0 as qtde_entradas,
        vd.qtde_vendida,
        COALESCE(est.qtde_atual * est.preco_custo_medio, 0)::NUMERIC(14,2) as valor_estoque,
        vd.valor_vendido::NUMERIC(14,2) as valor_vendido,
        COALESCE(est.preco_custo_medio, 0)::NUMERIC(14,2) as preco_custo,
        vd.preco_medio::NUMERIC(14,2) as preco_venda
    FROM vendas_periodo vd
    INNER JOIN filiais_ativas f ON f.cd_filial = vd.cd_filial
    LEFT JOIN estoque_atual est ON est.cd_marca = vd.cd_marca AND est.cd_filial = vd.cd_filial
    WHERE COALESCE(est.qtde_atual, 0) > 0
    ORDER BY f.nm_filial, vd.ds_marca, vd.qtde_vendida DESC
    LIMIT 20
";

$result = pg_query($conn, $sql);

if ($result) {
    $count = pg_num_rows($result);
    echo "✅ Query executada com sucesso!\n";
    echo "Registros retornados: $count\n\n";
    
    if ($count > 0) {
        echo "Primeiros 20 registros:\n";
        echo str_repeat("-", 100) . "\n";
        printf("%-25s %-20s %10s %10s %12s\n", "FILIAL", "MARCA", "ESTOQUE", "VENDAS", "VALOR VENDA");
        echo str_repeat("-", 100) . "\n";
        
        while ($row = pg_fetch_assoc($result)) {
            printf("%-25s %-20s %10.0f %10.0f %12.2f\n",
                substr($row['nm_filial'], 0, 25),
                substr($row['nm_marca'], 0, 20),
                $row['estoque_atual'],
                $row['qtde_vendida'],
                $row['valor_vendido']
            );
        }
    } else {
        echo "❌ NENHUM registro retornado\n";
        echo "\nTestando SEM filtro de estoque:\n";
        
        // Refazer query sem filtro
        $sql2 = str_replace("WHERE COALESCE(est.qtde_atual, 0) > 0", "WHERE 1=1", $sql);
        $result2 = pg_query($conn, $sql2);
        $count2 = pg_num_rows($result2);
        echo "Sem filtro de estoque: $count2 registros\n";
        
        if ($count2 > 0) {
            echo "\n⚠️ PROBLEMA: A tabela dm_estoque_atual está VAZIA!\n";
            echo "Solução: Mostrar vendas mesmo sem estoque.\n";
        }
    }
} else {
    echo "❌ ERRO na query: " . pg_last_error($conn) . "\n";
}

pg_close($conn);
