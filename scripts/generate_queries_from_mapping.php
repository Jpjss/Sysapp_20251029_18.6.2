<?php
// generate_queries_from_mapping.php
// Gera consultas SQL básicas usando mapping_diaazze_refined.json se existir,
// caso contrário usa mapping_diaazze.json

$mapFile1 = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mapping_diaazze_refined.json';
$mapFile2 = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mapping_diaazze.json';
$mapFile = file_exists($mapFile1) ? $mapFile1 : $mapFile2;
if (!file_exists($mapFile)) { fwrite(STDERR, "mapping_diaazze.json não encontrado.\n"); exit(1); }

$map = json_decode(file_get_contents($mapFile), true);
$m = isset($map['mapping']) ? $map['mapping'] : array();

$outDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'reports_sql';
if (!is_dir($outDir)) mkdir($outDir);

// 1) Vendas por período
$vendaTable = isset($m['vendas']['table']) ? $m['vendas']['table'] : null;
$vendaDate = (isset($m['vendas']['fields']) && isset($m['vendas']['fields'][1])) ? $m['vendas']['fields'][1] : 'dt_venda';
$vendaTotal = (isset($m['vendas']['fields']) && isset($m['vendas']['fields'][4])) ? $m['vendas']['fields'][4] : 'vl_total';
$sql1 = "SELECT DATE({$vendaDate}) AS dia, COUNT(*) AS pedidos, SUM({$vendaTotal}) AS total_vendido\nFROM {$vendaTable}\nWHERE {$vendaDate}::date BETWEEN :start_date::date AND :end_date::date\nGROUP BY DATE({$vendaDate})\nORDER BY DATE({$vendaDate});\n";
file_put_contents($outDir . DIRECTORY_SEPARATOR . 'rel_vendas_periodo.sql', $sql1);

// 2) Top produtos
$viTable = isset($m['venda_item']['table']) ? $m['venda_item']['table'] : null;
$prodTable = isset($m['produtos']['table']) ? $m['produtos']['table'] : null;
$prodIdVI = (isset($m['venda_item']['fields']) && isset($m['venda_item']['fields'][2])) ? $m['venda_item']['fields'][2] : 'cd_produto';
$qtVI = (isset($m['venda_item']['fields']) && isset($m['venda_item']['fields'][3])) ? $m['venda_item']['fields'][3] : 'qt';
$vlVI = (isset($m['venda_item']['fields']) && isset($m['venda_item']['fields'][4])) ? $m['venda_item']['fields'][4] : 'vl_unit';
$vendaPk = (isset($m['vendas']['fields']) && isset($m['vendas']['fields'][0])) ? $m['vendas']['fields'][0] : 'cd_venda';
$viVendaFk = (isset($m['venda_item']['fields']) && isset($m['venda_item']['fields'][1])) ? $m['venda_item']['fields'][1] : 'cd_venda';
$sql2 = "SELECT pr.{$prodIdVI} AS cd_produto, pr.nm_produto, SUM(vi.{$qtVI}) AS quantidade_vendida, SUM(vi.{$vlVI} * vi.{$qtVI}) AS receita\nFROM {$viTable} vi\nLEFT JOIN {$prodTable} pr ON pr.{$prodIdVI} = vi.{$prodIdVI}\nJOIN {$vendaTable} v ON v.{$vendaPk} = vi.{$viVendaFk}\nWHERE v.{$vendaDate}::date BETWEEN :start_date::date AND :end_date::date\nGROUP BY pr.{$prodIdVI}, pr.nm_produto\nORDER BY quantidade_vendida DESC\nLIMIT 50;";
file_put_contents($outDir . DIRECTORY_SEPARATOR . 'rel_top_produtos.sql', $sql2);

// 3) Estoque atual (se houver tabela de estoque)
$estoqueTable = isset($m['estoque']['table']) ? $m['estoque']['table'] : null;
$sql3 = "-- Verifique colunas em mapping para ajustar nome de quantidade\n";
if ($estoqueTable) {
    $prodPk = (isset($m['produtos']['fields']) && isset($m['produtos']['fields'][0])) ? $m['produtos']['fields'][0] : 'cd_produto';
    $sql3 .= "SELECT p.cd_produto, p.nm_produto, COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'entrada' THEN em.qt ELSE 0 END),0) - COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'saida' THEN em.qt ELSE 0 END),0) AS saldo\nFROM {$prodTable} p\nLEFT JOIN {$estoqueTable} em ON em.cd_produto = p.{$prodPk}\nGROUP BY p.cd_produto, p.nm_produto\nORDER BY saldo ASC;";
    file_put_contents($outDir . DIRECTORY_SEPARATOR . 'rel_estoque_saldo.sql', $sql3);
}

// 4) Contas a receber vencidas
$crTable = isset($m['contas_receber']['table']) ? $m['contas_receber']['table'] : null;
$crVenc = (isset($m['contas_receber']['fields']) && isset($m['contas_receber']['fields'][1])) ? $m['contas_receber']['fields'][1] : 'dt_vencimento';
$crVal = (isset($m['contas_receber']['fields']) && isset($m['contas_receber']['fields'][2])) ? $m['contas_receber']['fields'][2] : 'vl_parcela';
$clientePk = (isset($m['clientes']['fields']) && isset($m['clientes']['fields'][0])) ? $m['clientes']['fields'][0] : 'cd_pessoa';
$crPessoaField = (isset($m['contas_receber']['fields']) && isset($m['contas_receber']['fields'][5])) ? $m['contas_receber']['fields'][5] : 'cd_pessoa';
$sql4 = "SELECT cr.*, p.nm_pessoa FROM {$crTable} cr LEFT JOIN {$m['clientes']['table']} p ON p.{$clientePk} = cr.{$crPessoaField} WHERE cr.{$crVenc} < CURRENT_DATE AND COALESCE(UPPER(cr.situacao),'') NOT IN ('PAGO','LIQUIDADO') ORDER BY cr.{$crVenc};";
file_put_contents($outDir . DIRECTORY_SEPARATOR . 'rel_contas_receber_vencidas.sql', $sql4);

echo "Consultas geradas em: {$outDir}\n";

?>
