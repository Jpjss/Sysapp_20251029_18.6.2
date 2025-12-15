<?php
/**
 * Teste da API de Relatórios - Verificar se retorna dados
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

// Simula uma sessão válida para teste
$_SESSION['Dados']['id_usuario'] = 1;
$_SESSION['Questionarios']['nm_usu'] = 'Admin';

echo "=== TESTE DE API DE RELATÓRIOS ===\n\n";

// Teste 1: Conectar ao banco
echo "1. Testando conexão com banco...\n";
$db = Database::getInstance();
$db->connect();
$conn = $db->getConnection();

if ($conn) {
    echo "✓ Conexão estabelecida\n\n";
} else {
    echo "✗ Falha na conexão\n";
    exit;
}

// Teste 2: Verificar se existe tabela de questionários
echo "2. Verificando tipo de banco...\n";
$sqlCheckQuest = "SELECT COUNT(*) as total FROM glb_questionarios";
$resultCheck = pg_query($conn, $sqlCheckQuest);
$hasQuestionarios = pg_fetch_assoc($resultCheck)['total'] > 0;

echo "Tipo: " . ($hasQuestionarios ? "QUESTIONÁRIOS" : "COMERCIAL (ERP)") . "\n\n";

// Teste 3: Buscar estatísticas
echo "3. Buscando estatísticas...\n";

if ($hasQuestionarios) {
    // Total de clientes
    $sqlClientes = "SELECT COUNT(DISTINCT cd_pessoa) as total FROM glb_questionario_respostas";
    $resultClientes = pg_query($conn, $sqlClientes);
    $totalClientes = pg_fetch_assoc($resultClientes)['total'] ?? 0;
    echo "Total de clientes: $totalClientes\n";
    
    // Total de respostas
    $sqlRespostas = "SELECT COUNT(*) as total FROM glb_questionario_respostas";
    $resultRespostas = pg_query($conn, $sqlRespostas);
    $totalRespostas = pg_fetch_assoc($resultRespostas)['total'] ?? 0;
    echo "Total de respostas: $totalRespostas\n";
} else {
    // Total de clientes (tabela prc_pessoa)
    $sqlClientes = "SELECT COUNT(*) as total FROM prc_pessoa WHERE fg_tipo IN ('C', 'A')";
    $resultClientes = pg_query($conn, $sqlClientes);
    $totalClientes = pg_fetch_assoc($resultClientes)['total'] ?? 0;
    echo "Total de clientes: $totalClientes\n";
    
    // Total de vendas
    $sqlVendas = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total, 
                 COALESCE(SUM(vl_total), 0) as valor_total
                 FROM rc_lanc_cpl 
                 WHERE dt_mov IS NOT NULL";
    $resultVendas = pg_query($conn, $sqlVendas);
    $vendas = pg_fetch_assoc($resultVendas);
    echo "Total de vendas: " . ($vendas['total'] ?? 0) . "\n";
    echo "Valor total: R$ " . number_format($vendas['valor_total'] ?? 0, 2, ',', '.') . "\n";
}

echo "\n4. Testando endpoint da API...\n";

// Simula requisição GET para /api/relatorios/dashboard
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/relatorios/dashboard';

// Captura output da API
ob_start();
$method = 'GET';
$action = 'dashboard';
include __DIR__ . '/api/relatorios.php';
$apiResponse = ob_get_clean();

echo "Resposta da API:\n";
echo $apiResponse . "\n";

// Decodifica JSON
$data = json_decode($apiResponse, true);
if ($data && isset($data['success']) && $data['success']) {
    echo "\n✓ API retornando dados corretamente!\n";
    echo "Stats: " . json_encode($data['stats'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "\n✗ API não retornou dados válidos\n";
}
