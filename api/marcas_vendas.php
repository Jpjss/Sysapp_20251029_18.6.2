<?php
/**
 * API para retornar dados de marcas mais vendidas em tempo real
 * Endpoint: /api/marcas_vendas.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Importar configurações
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Session.php';

Session::start();

// Verificar se o usuário está logado
if (!Session::isValid()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuário não autenticado'
    ]);
    exit;
}

// Verificar se há configuração de banco do cliente
if (!Session::check('Config.database')) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Nenhuma empresa selecionada. Por favor, selecione uma empresa antes de acessar os dados.'
    ]);
    exit;
}

try {
    // Conectar ao banco do cliente usando as configurações da sessão
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Parâmetros de filtro
    $periodo = $_GET['periodo'] ?? '30'; // Padrão: últimos 30 dias
    $limite = $_GET['limite'] ?? '10'; // Top 10 marcas
    $cd_filial = $_GET['cd_filial'] ?? null;
    
    // Construir condição de período
    // Se período = 0, filtra apenas HOJE (00:00:00 até 23:59:59)
    if ($periodo === '0' || $periodo === 0) {
        $condicaoPeriodo = "dm_venda.dt_emi_pedido >= CURRENT_DATE AND dm_venda.dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'";
    } else {
        $condicaoPeriodo = "dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '$periodo days'";
    }
    
    // Construir query de marcas mais vendidas
    $sql = "
        SELECT 
            dm_produto.cd_marca,
            dm_produto.ds_marca,
            COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
            SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
            SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
        FROM dm_produto
        INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
            ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
        WHERE 1=1
            AND $condicaoPeriodo
            AND dm_produto.cd_marca IS NOT NULL
            AND dm_produto.ds_marca IS NOT NULL
    ";
    
    // Filtro por filial se fornecido
    if ($cd_filial) {
        $sql .= " AND dm_venda.cd_filial = :cd_filial";
    }
    
    $sql .= "
        GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
        ORDER BY quantidade_vendida DESC
        LIMIT :limite
    ";
    
    $stmt = $conn->prepare($sql);
    
    if ($cd_filial) {
        $stmt->bindParam(':cd_filial', $cd_filial, PDO::PARAM_INT);
    }
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    
    $stmt->execute();
    $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados para os gráficos
    $labels = [];
    $quantidades = [];
    $valores = [];
    $vendas = [];
    
    foreach ($marcas as $marca) {
        $labels[] = $marca['ds_marca'] ?? 'Marca ' . $marca['cd_marca'];
        $quantidades[] = (int)$marca['quantidade_vendida'];
        $valores[] = (float)$marca['valor_total'];
        $vendas[] = (int)$marca['total_vendas'];
    }
    
    // Retornar dados
    echo json_encode([
        'success' => true,
        'periodo' => (int)$periodo,
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Quantidade Vendida',
                    'data' => $quantidades,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Valor Total (R$)',
                    'data' => $valores,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Total de Vendas',
                    'data' => $vendas,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.6)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 1
                ]
            ]
        ],
        'marcas_detalhadas' => $marcas
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao consultar banco de dados',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage()
    ]);
}
