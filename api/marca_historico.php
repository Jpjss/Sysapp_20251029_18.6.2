<?php
/**
 * API para retornar histórico de vendas de uma marca específica ao longo do tempo
 * Endpoint: /api/marca_historico.php
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
        'error' => 'Nenhuma empresa selecionada.'
    ]);
    exit;
}

try {
    // Conectar ao banco do cliente
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Parâmetros obrigatórios
    $cd_marca = $_GET['cd_marca'] ?? null;
    
    if (!$cd_marca) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Código da marca não fornecido'
        ]);
        exit;
    }
    
    // Parâmetros de filtro
    $periodo = $_GET['periodo'] ?? '30'; // Padrão: últimos 30 dias
    $agrupamento = $_GET['agrupamento'] ?? 'dia'; // dia, semana, mes
    
    // Definir formato de agrupamento
    switch ($agrupamento) {
        case 'semana':
            $dateFormat = 'YYYY-"Sem"IW';
            $intervalFormat = '1 week';
            break;
        case 'mes':
            $dateFormat = 'YYYY-MM';
            $intervalFormat = '1 month';
            break;
        default: // dia
            $dateFormat = 'YYYY-MM-DD';
            $intervalFormat = '1 day';
            break;
    }
    
    // Buscar nome da marca
    $sqlMarca = "
        SELECT DISTINCT ds_marca 
        FROM dm_produto 
        WHERE cd_marca = :cd_marca
        LIMIT 1
    ";
    $stmtMarca = $conn->prepare($sqlMarca);
    $stmtMarca->bindParam(':cd_marca', $cd_marca, PDO::PARAM_STR);
    $stmtMarca->execute();
    $marcaInfo = $stmtMarca->fetch(PDO::FETCH_ASSOC);
    $ds_marca = $marcaInfo['ds_marca'] ?? "Marca {$cd_marca}";
    
    // Construir query de histórico
    $sql = "
        WITH date_series AS (
            SELECT 
                generate_series(
                    CURRENT_DATE - INTERVAL '{$periodo} days',
                    CURRENT_DATE,
                    INTERVAL '{$intervalFormat}'
                )::date AS data
        ),
        vendas_marca AS (
            SELECT 
                TO_CHAR(dm_venda.dt_emi_pedido, '{$dateFormat}') as periodo,
                dm_venda.dt_emi_pedido::date as data_base,
                COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
                SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
                SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
            FROM dm_produto
            INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
                ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
            WHERE dm_produto.cd_marca = :cd_marca
                AND dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '{$periodo} days'
            GROUP BY periodo, data_base
        )
        SELECT 
            ds.data,
            TO_CHAR(ds.data, '{$dateFormat}') as periodo,
            COALESCE(vm.total_vendas, 0) as total_vendas,
            COALESCE(vm.quantidade_vendida, 0) as quantidade_vendida,
            COALESCE(vm.valor_total, 0)::NUMERIC(14,2) as valor_total
        FROM date_series ds
        LEFT JOIN vendas_marca vm ON TO_CHAR(ds.data, '{$dateFormat}') = vm.periodo
        ORDER BY ds.data ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cd_marca', $cd_marca, PDO::PARAM_STR);
    $stmt->execute();
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados para o gráfico
    $labels = [];
    $quantidades = [];
    $valores = [];
    $vendas = [];
    
    foreach ($historico as $item) {
        // Formatar label de acordo com agrupamento
        switch ($agrupamento) {
            case 'semana':
                $labels[] = $item['periodo'];
                break;
            case 'mes':
                $date = new DateTime($item['data']);
                $labels[] = strftime('%b/%Y', $date->getTimestamp());
                break;
            default: // dia
                $date = new DateTime($item['data']);
                $labels[] = $date->format('d/m');
                break;
        }
        
        $quantidades[] = (int)$item['quantidade_vendida'];
        $valores[] = (float)$item['valor_total'];
        $vendas[] = (int)$item['total_vendas'];
    }
    
    // Calcular totais do período
    $totalVendas = array_sum($vendas);
    $totalQuantidade = array_sum($quantidades);
    $totalValor = array_sum($valores);
    
    // Retornar dados
    echo json_encode([
        'success' => true,
        'cd_marca' => $cd_marca,
        'ds_marca' => $ds_marca,
        'periodo' => (int)$periodo,
        'agrupamento' => $agrupamento,
        'timestamp' => date('Y-m-d H:i:s'),
        'totais' => [
            'vendas' => $totalVendas,
            'quantidade' => $totalQuantidade,
            'valor' => $totalValor
        ],
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Quantidade Vendida',
                    'data' => $quantidades
                ],
                [
                    'label' => 'Valor Total (R$)',
                    'data' => $valores
                ],
                [
                    'label' => 'Total de Vendas',
                    'data' => $vendas
                ]
            ]
        ],
        'historico_detalhado' => $historico
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
