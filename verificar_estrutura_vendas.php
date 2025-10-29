<?php
/**
 * Verifica estrutura de rc_lanc_cpl
 */

require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', 5432);

echo "=== RC_LANC_CPL (PARCELAS) ===\n\n";

$sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'rc_lanc_cpl' ORDER BY ordinal_position";
$colunas = $db->fetchAll($sql);
foreach ($colunas as $col) {
    echo "{$col['column_name']} ({$col['data_type']})\n";
}

echo "\n=== EXEMPLO DE REGISTRO ===\n";
$sql = "SELECT * FROM rc_lanc_cpl LIMIT 1";
$exemplo = $db->fetchOne($sql);
print_r($exemplo);

echo "\n=== VENDAS POR DT_VENCTO (últimos 30 dias) ===\n";
$sql = "SELECT DATE(dt_vencto) as data, COUNT(DISTINCT cd_lanc) as total 
        FROM rc_lanc_cpl 
        WHERE dt_vencto >= CURRENT_DATE - INTERVAL '30 days'
        GROUP BY DATE(dt_vencto)
        ORDER BY DATE(dt_vencto) DESC
        LIMIT 10";
$vendas = $db->fetchAll($sql);
foreach ($vendas as $v) {
    echo "{$v['data']}: {$v['total']} vendas\n";
}

echo "\n=== VERIFICANDO PED_VD (PEDIDOS) ===\n";
$sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'ped_vd' ORDER BY ordinal_position LIMIT 20";
$colunas = $db->fetchAll($sql);
foreach ($colunas as $col) {
    echo "{$col['column_name']} ({$col['data_type']})\n";
}

echo "\n=== EXEMPLO DE PEDIDO ===\n";
$sql = "SELECT * FROM ped_vd LIMIT 1";
$exemplo = $db->fetchOne($sql);
if ($exemplo) {
    print_r($exemplo);
}

echo "\n=== FIM ===\n";
