<?php
// generate_mapping.php
// Gera mapeamento sugerido de tabelas/campos para relatórios a partir do
// arquivo schema_report_diaazze.json

if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'schema_report_diaazze.json')) {
    fwrite(STDERR, "Arquivo schema_report_diaazze.json não encontrado. Execute discover_single_db.php primeiro.\n");
    exit(1);
}

$raw = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'schema_report_diaazze.json');
$data = json_decode($raw, true);
if ($data === null) {
    fwrite(STDERR, "Erro ao decodificar JSON do schema_report_diaazze.json\n");
    exit(1);
}

$tables = $data['tables'] ?? [];

function find_by_name($tables, $patterns) {
    $res = [];
    foreach ($tables as $t) {
        $name = strtolower($t['table']);
        foreach ((array)$patterns as $pat) {
            if (strpos($name, $pat) !== false) {
                $res[] = $t;
                break;
            }
        }
    }
    return $res;
}

function pick_best($matches) {
    if (empty($matches)) return null;
    // prefer table named exactly or with shortest name
    usort($matches, function($a,$b){
        $la = strlen($a['table']); $lb = strlen($b['table']);
        return $la - $lb;
    });
    return $matches[0]['table'];
}

// Heurísticas
$mapping = [];

$mapping['clientes_table'] = pick_best(find_by_name($tables, ['pessoa','cliente','clientes','cad_pessoa','glb_pessoa']));
$mapping['produtos_table'] = pick_best(find_by_name($tables, ['produto','prod','prd','est_produto','produto']));
$mapping['vendas_table'] = pick_best(find_by_name($tables, ['venda','pedido','ped_vd','ped_venda','ped'])) ;
$mapping['venda_item_table'] = pick_best(find_by_name($tables, ['venda_item','item','ped_vd_produto','pedido_item','itens_venda','venda_produto']));
$mapping['estoque_table'] = pick_best(find_by_name($tables, ['estoque','est_produto','estoque_movimento','est_produto_cpl','prc_filial','estoque']));
$mapping['contas_receber_table'] = pick_best(find_by_name($tables, ['contas_receber','receber','titulos_receber','contareceber']));
$mapping['pagamentos_table'] = pick_best(find_by_name($tables, ['pagamento','pagamentos','recebimento','pagto','pagos']));
$mapping['nota_fiscal_table'] = pick_best(find_by_name($tables, ['nota_fiscal','nota_fis','nf','nfe','notafiscal']));
$mapping['questionario_table'] = pick_best(find_by_name($tables, ['questionario','questionarios','glb_questionario']));

// Função para achar colunas chave em uma tabela
function find_columns($tables, $tableName, $candidates) {
    if (!$tableName) return [];
    foreach ($tables as $t) {
        if ($t['table'] === $tableName) {
            $cols = array_column($t['columns'], 'column_name');
            $found = [];
            foreach ($candidates as $cand) {
                foreach ($cols as $c) {
                    if (stripos($c, $cand) !== false) {
                        $found[] = $c;
                        break;
                    }
                }
            }
            return array_values(array_unique($found));
        }
    }
    return [];
}

$mapping_details = [];

$mapping_details['clientes'] = [
    'table' => $mapping['clientes_table'],
    'fields' => find_columns($tables, $mapping['clientes_table'], ['cd_pessoa','cd_cliente','nm_pessoa','nm_cliente','cpf','cnpj','nr_cpf_cnpj','ds_email','email'])
];

$mapping_details['produtos'] = [
    'table' => $mapping['produtos_table'],
    'fields' => find_columns($tables, $mapping['produtos_table'], ['cd_produto','nm_produto','sku','vl_venda','vlr_venda','vl_custo'])
];

$mapping_details['vendas'] = [
    'table' => $mapping['vendas_table'],
    'fields' => find_columns($tables, $mapping['vendas_table'], ['cd_venda','cd_ped','dt_venda','dt_hr_ped','vl_total','valor_total','cd_pessoa'])
];

$mapping_details['venda_item'] = [
    'table' => $mapping['venda_item_table'],
    'fields' => find_columns($tables, $mapping['venda_item_table'], ['cd_item','cd_venda','cd_produto','qt','qtde','vl_unit','vlr_vd'])
];

$mapping_details['estoque'] = [
    'table' => $mapping['estoque_table'],
    'fields' => find_columns($tables, $mapping['estoque_table'], ['cd_produto','qt','qtde_estoque','qtde_atual','tipo_mov','cd_filial'])
];

$mapping_details['contas_receber'] = [
    'table' => $mapping['contas_receber_table'],
    'fields' => find_columns($tables, $mapping['contas_receber_table'], ['cd_receber','dt_vencimento','vl_parcela','dt_pagamento','situacao','cd_pessoa'])
];

$mapping_details['pagamentos'] = [
    'table' => $mapping['pagamentos_table'],
    'fields' => find_columns($tables, $mapping['pagamentos_table'], ['cd_pagto','dt_pagto','vl_pagto','meio_pagto','referencia'])
];

$mapping_details['nota_fiscal'] = [
    'table' => $mapping['nota_fiscal_table'],
    'fields' => find_columns($tables, $mapping['nota_fiscal_table'], ['cd_nf','nr_nf','dt_emissao','vl_total','chave_nf','cd_venda'])
];

$mapping_details['questionarios'] = [
    'table' => $mapping['questionario_table'],
    'fields' => find_columns($tables, $mapping['questionario_table'], ['cd_questionario','nm_questionario','fg_ativo'])
];

$out = ['summary' => ['tables_count' => count($tables)], 'mapping' => $mapping_details];

$outfile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mapping_diaazze.json';
file_put_contents($outfile, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Mapping salvo em: {$outfile}\n";
echo "Resumo: Tabelas encontradas = " . count($tables) . "\n";

?>
