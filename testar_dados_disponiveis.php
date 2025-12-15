<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect(); // Conecta ao banco padrão (sysapp)

echo "=== TESTANDO BANCO SYSAPP (PADRÃO) ===\n\n";

// Verifica se tem tabelas comerciais
$tabelas_comerciais = [
    'prc_pessoa' => 'Clientes',
    'rc_lanc_cpl' => 'Lançamentos/Vendas',
    'glb_questionarios' => 'Questionários',
    'glb_questionario_respostas' => 'Respostas'
];

foreach ($tabelas_comerciais as $tabela => $descricao) {
    $sql = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = '$tabela') as existe";
    $result = pg_query($db->getConnection(), $sql);
    $existe = pg_fetch_assoc($result)['existe'] === 't';
    
    if ($existe) {
        // Conta registros
        $sqlCount = "SELECT COUNT(*) as total FROM $tabela";
        $resultCount = pg_query($db->getConnection(), $sqlCount);
        $total = pg_fetch_assoc($resultCount)['total'];
        echo "✓ $descricao ($tabela): $total registros\n";
    } else {
        echo "✗ $descricao ($tabela): NÃO EXISTE\n";
    }
}

echo "\n\n=== DADOS DISPONÍVEIS PARA DASHBOARD ===\n\n";

// Se não tem questionários, mostra dados comerciais
$sqlCheck = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'glb_questionarios') as existe";
$result = pg_query($db->getConnection(), $sqlCheck);
$temQuestionarios = pg_fetch_assoc($result)['existe'] === 't';

if ($temQuestionarios) {
    $sqlCount = "SELECT COUNT(*) FROM glb_questionarios";
    $result = pg_query($db->getConnection(), $sqlCount);
    $count = pg_fetch_assoc($result)['count'];
    
    if ($count > 0) {
        echo "TIPO: QUESTIONÁRIOS\n";
    } else {
        echo "TIPO: COMERCIAL (tabela glb_questionarios existe mas vazia)\n";
    }
} else {
    echo "TIPO: COMERCIAL (sem tabela glb_questionarios)\n";
}

// Mostra estatísticas
if (!$temQuestionarios || true) {
    echo "\n=== ESTATÍSTICAS COMERCIAIS ===\n\n";
    
    // Clientes
    $sql = "SELECT COUNT(*) as total FROM prc_pessoa WHERE fg_tipo IN ('C', 'A')";
    $result = pg_query($db->getConnection(), $sql);
    $total_clientes = pg_fetch_assoc($result)['total'];
    echo "Total de clientes: $total_clientes\n";
    
    // Vendas
    $sql = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total, 
            COALESCE(SUM(vl_total), 0) as valor_total
            FROM rc_lanc_cpl 
            WHERE dt_mov IS NOT NULL";
    $result = pg_query($db->getConnection(), $sql);
    $vendas = pg_fetch_assoc($result);
    echo "Total de vendas: {$vendas['total']}\n";
    echo "Valor total: R$ " . number_format($vendas['valor_total'], 2, ',', '.') . "\n";
    
    // Vendas hoje
    $sql = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total,
            COALESCE(SUM(vl_total), 0) as valor
            FROM rc_lanc_cpl 
            WHERE DATE(dt_mov) = CURRENT_DATE";
    $result = pg_query($db->getConnection(), $sql);
    $hoje = pg_fetch_assoc($result);
    echo "Vendas hoje: {$hoje['total']}\n";
    echo "Valor hoje: R$ " . number_format($hoje['valor'], 2, ',', '.') . "\n";
}
