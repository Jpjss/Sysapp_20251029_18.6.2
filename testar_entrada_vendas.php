<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== TESTE: ENTRADA X VENDAS ===\n\n";

// 1. Verificar se dm_estoque_atual existe
echo "1. Verificando tabela dm_estoque_atual...\n";
$sql = "SELECT tablename FROM pg_tables WHERE tablename = 'dm_estoque_atual'";
$result = pg_query($conn, $sql);
if (pg_num_rows($result) > 0) {
    echo "✅ Tabela dm_estoque_atual EXISTE\n";
    
    // Ver estrutura
    $sql = "SELECT column_name, data_type FROM information_schema.columns 
            WHERE table_name = 'dm_estoque_atual' ORDER BY ordinal_position";
    $result = pg_query($conn, $sql);
    echo "\nColunas de dm_estoque_atual:\n";
    while ($row = pg_fetch_assoc($result)) {
        echo "  - {$row['column_name']} ({$row['data_type']})\n";
    }
} else {
    echo "❌ Tabela dm_estoque_atual NÃO EXISTE\n";
    
    // Procurar tabelas de estoque
    echo "\nProcurando tabelas com 'estoque'...\n";
    $sql = "SELECT tablename FROM pg_tables WHERE tablename LIKE '%estoque%' OR tablename LIKE '%est_%'";
    $result = pg_query($conn, $sql);
    while ($row = pg_fetch_assoc($result)) {
        echo "  - {$row['tablename']}\n";
    }
}

echo "\n\n2. Testando query do relatório Entrada X Vendas...\n";

// Query simplificada para teste
$sql = "
    SELECT COUNT(*) as total
    FROM dm_orcamento_vendas_consolidadas v
    INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
    WHERE DATE(v.dt_emi_pedido) >= '2026-01-01'::date
      AND DATE(v.dt_emi_pedido) <= '2026-01-06'::date
      AND p.cd_marca IS NOT NULL
";

$result = pg_query($conn, $sql);
if ($result) {
    $row = pg_fetch_assoc($result);
    echo "Total de vendas no período (01/01 a 06/01): {$row['total']}\n";
    
    if ($row['total'] > 0) {
        echo "\n3. Testando agrupamento por marca e filial...\n";
        $sql = "
            SELECT 
                v.cd_filial,
                p.cd_marca,
                p.ds_marca,
                COUNT(DISTINCT v.cd_pedido) as qtde_pedidos,
                COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida,
                COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_vendido
            FROM dm_orcamento_vendas_consolidadas v
            INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
            WHERE DATE(v.dt_emi_pedido) >= '2026-01-01'::date
              AND DATE(v.dt_emi_pedido) <= '2026-01-06'::date
              AND p.cd_marca IS NOT NULL
            GROUP BY v.cd_filial, p.cd_marca, p.ds_marca
            ORDER BY qtde_vendida DESC
            LIMIT 10
        ";
        
        $result = pg_query($conn, $sql);
        echo "\nTop 10 marcas/filiais:\n";
        while ($row = pg_fetch_assoc($result)) {
            echo sprintf("  Filial %d - %s: %d vendas, R$ %s\n",
                $row['cd_filial'],
                $row['ds_marca'],
                $row['qtde_vendida'],
                number_format($row['valor_vendido'], 2, ',', '.')
            );
        }
    }
} else {
    echo "❌ ERRO: " . pg_last_error($conn) . "\n";
}

echo "\n\n4. Verificando filiais ativas...\n";
$sql = "SELECT cd_filial, COALESCE(nm_fant, nm_filial, 'Filial ' || cd_filial) as nm_filial 
        FROM prc_filial WHERE sts_filial = 1 ORDER BY cd_filial";
$result = pg_query($conn, $sql);
echo "Filiais ativas:\n";
while ($row = pg_fetch_assoc($result)) {
    echo "  - [{$row['cd_filial']}] {$row['nm_filial']}\n";
}

pg_close($conn);
