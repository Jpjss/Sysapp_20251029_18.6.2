<?php
/**
 * API de Relatórios
 */

// Verifica autenticação
if (!Session::check('Dados.id_usuario')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado', 'redirectTo' => '/login']);
    exit();
}

// Verifica se empresa foi selecionada - OBRIGATÓRIO para relatórios
if (!Session::check('Config.database') || !Session::check('Config.cd_empresa')) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Nenhuma empresa selecionada',
        'redirectTo' => '/escolher-empresa'
    ]);
    exit();
}

// Conecta ao banco da empresa selecionada
$db = Database::getInstance();
$db->connect(
    Session::read('Config.host'),
    Session::read('Config.database'),
    Session::read('Config.user'),
    Session::read('Config.password'),
    Session::read('Config.porta')
);

// GET /api/relatorios/dashboard - Dados do dashboard
if ($method === 'GET' && $action === 'dashboard') {
    $conn = $db->getConnection();
    
    // Verifica se existem questionários no banco
    $sqlCheckQuest = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'glb_questionarios') as existe";
    $resultCheck = pg_query($conn, $sqlCheckQuest);
    $tabelaQuestionariosExiste = pg_fetch_assoc($resultCheck)['existe'] === 't';
    
    $hasQuestionarios = false;
    if ($tabelaQuestionariosExiste) {
        $sqlCount = "SELECT COUNT(*) as total FROM glb_questionarios";
        $resultCount = pg_query($conn, $sqlCount);
        $hasQuestionarios = pg_fetch_assoc($resultCount)['total'] > 0;
    }
    
    $stats = [];
    
    if ($hasQuestionarios) {
        // Se tem questionários, busca dados de atendimentos
        
        // Total de clientes
        $sqlClientes = "SELECT COUNT(DISTINCT cd_pessoa) as total FROM glb_questionario_respostas";
        $resultClientes = pg_query($conn, $sqlClientes);
        $stats['total_clientes'] = (int)(pg_fetch_assoc($resultClientes)['total'] ?? 0);
        
        // Total de questionários/respostas
        $sqlRespostas = "SELECT COUNT(*) as total FROM glb_questionario_respostas";
        $resultRespostas = pg_query($conn, $sqlRespostas);
        $stats['total_respostas'] = (int)(pg_fetch_assoc($resultRespostas)['total'] ?? 0);
        
        // Atendimentos hoje
        $sqlHoje = "SELECT COUNT(*) as total FROM glb_questionario_respostas 
                   WHERE DATE(dt_resposta) = CURRENT_DATE";
        $resultHoje = pg_query($conn, $sqlHoje);
        $stats['atendimentos_hoje'] = (int)(pg_fetch_assoc($resultHoje)['total'] ?? 0);
        
        // Atendimentos no mês
        $sqlMes = "SELECT COUNT(*) as total FROM glb_questionario_respostas 
                  WHERE EXTRACT(MONTH FROM dt_resposta) = EXTRACT(MONTH FROM CURRENT_DATE)
                  AND EXTRACT(YEAR FROM dt_resposta) = EXTRACT(YEAR FROM CURRENT_DATE)";
        $resultMes = pg_query($conn, $sqlMes);
        $stats['atendimentos_mes'] = (int)(pg_fetch_assoc($resultMes)['total'] ?? 0);
        
        $stats['tipo'] = 'questionarios';
        
    } else {
        // Verifica se tem tabelas comerciais
        $sqlCheckPessoa = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'prc_pessoa') as existe";
        $resultCheckPessoa = pg_query($conn, $sqlCheckPessoa);
        $temPrcPessoa = pg_fetch_assoc($resultCheckPessoa)['existe'] === 't';
        
        $sqlCheckLanc = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'rc_lanc_cpl') as existe";
        $resultCheckLanc = pg_query($conn, $sqlCheckLanc);
        $temRcLanc = pg_fetch_assoc($resultCheckLanc)['existe'] === 't';
        
        if ($temPrcPessoa && $temRcLanc) {
            // Se não tem questionários mas tem dados comerciais, busca dados comerciais
            
            // Total de clientes (tabela prc_pessoa)
            $sqlClientes = "SELECT COUNT(*) as total FROM prc_pessoa WHERE fg_tipo IN ('C', 'A')";
            $resultClientes = pg_query($conn, $sqlClientes);
            $stats['total_clientes'] = (int)(pg_fetch_assoc($resultClientes)['total'] ?? 0);
            
            // Total de vendas (rc_lanc_cpl)
            $sqlVendas = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total, 
                         COALESCE(SUM(vl_total), 0) as valor_total
                         FROM rc_lanc_cpl 
                         WHERE dt_mov IS NOT NULL";
            $resultVendas = pg_query($conn, $sqlVendas);
            $vendas = pg_fetch_assoc($resultVendas);
            $stats['total_respostas'] = (int)($vendas['total'] ?? 0);
            $stats['valor_total_vendas'] = (float)($vendas['valor_total'] ?? 0);
            
            // Vendas hoje
            $sqlHoje = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total,
                       COALESCE(SUM(vl_total), 0) as valor
                       FROM rc_lanc_cpl 
                       WHERE DATE(dt_mov) = CURRENT_DATE";
            $resultHoje = pg_query($conn, $sqlHoje);
            $hoje = pg_fetch_assoc($resultHoje);
            $stats['atendimentos_hoje'] = (int)($hoje['total'] ?? 0);
            $stats['valor_vendas_hoje'] = (float)($hoje['valor'] ?? 0);
            
            // Vendas no mês
            $sqlMes = "SELECT COUNT(DISTINCT cd_lanc_cpl) as total,
                      COALESCE(SUM(vl_total), 0) as valor
                      FROM rc_lanc_cpl 
                      WHERE EXTRACT(MONTH FROM dt_mov) = EXTRACT(MONTH FROM CURRENT_DATE)
                      AND EXTRACT(YEAR FROM dt_mov) = EXTRACT(YEAR FROM CURRENT_DATE)";
            $resultMes = pg_query($conn, $sqlMes);
            $mes = pg_fetch_assoc($resultMes);
            $stats['atendimentos_mes'] = (int)($mes['total'] ?? 0);
            $stats['valor_vendas_mes'] = (float)($mes['valor'] ?? 0);
            
            $stats['tipo'] = 'comercial';
        } else {
            // Banco não tem dados suficientes
            $stats['total_clientes'] = 0;
            $stats['total_respostas'] = 0;
            $stats['atendimentos_hoje'] = 0;
            $stats['atendimentos_mes'] = 0;
            $stats['tipo'] = 'sem_dados';
            $stats['mensagem'] = 'Este banco não possui dados de questionários nem dados comerciais. Selecione outra empresa.';
        }
    }
    
    $stats['total_questionarios'] = $hasQuestionarios ? 1 : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    exit();
}

// GET /api/relatorios/estoque - Relatório de estoque
if ($method === 'GET' && $action === 'estoque') {
    $limite = $_GET['limite'] ?? 100;
    $offset = $_GET['offset'] ?? 0;
    $busca = $_GET['busca'] ?? '';
    
    $sql = "SELECT 
                p.cd_produto,
                p.nm_produto,
                m.nm_marca,
                COALESCE(s.qt_saldo, 0) as quantidade,
                p.vl_venda,
                (COALESCE(s.qt_saldo, 0) * p.vl_venda) as valor_total
            FROM est_produto p
            LEFT JOIN est_produto_marca m ON m.cd_marca = p.cd_marca
            LEFT JOIN est_saldo s ON s.cd_produto = p.cd_produto
            WHERE 1=1";
    
    $params = [];
    $paramCount = 1;
    
    if ($busca) {
        $sql .= " AND (p.nm_produto ILIKE $" . $paramCount . " OR p.cd_produto::text ILIKE $" . $paramCount . ")";
        $params[] = "%$busca%";
        $paramCount++;
    }
    
    $sql .= " ORDER BY p.nm_produto LIMIT $" . $paramCount . " OFFSET $" . ($paramCount + 1);
    $params[] = $limite;
    $params[] = $offset;
    
    $result = pg_query_params($db->getConnection(), $sql, $params);
    $produtos = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $produtos[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'produtos' => $produtos
    ]);
    exit();
}

// GET /api/relatorios/ultimos-dias - Dados dos últimos 7 dias
if ($method === 'GET' && $action === 'ultimos-dias') {
    $conn = $db->getConnection();
    
    // Verifica tipo de dados
    $sqlCheckQuest = "SELECT COUNT(*) as total FROM glb_questionarios";
    $resultCheck = pg_query($conn, $sqlCheckQuest);
    $hasQuestionarios = pg_fetch_assoc($resultCheck)['total'] > 0;
    
    $dados = [];
    
    if ($hasQuestionarios) {
        // Últimos 7 dias de atendimentos
        $sql = "SELECT DATE(dt_resposta) as data, COUNT(*) as total
               FROM glb_questionario_respostas
               WHERE dt_resposta >= CURRENT_DATE - INTERVAL '7 days'
               GROUP BY DATE(dt_resposta)
               ORDER BY data";
    } else {
        // Últimos 7 dias de vendas
        $sql = "SELECT DATE(dt_mov) as data, COUNT(DISTINCT cd_lanc_cpl) as total
               FROM rc_lanc_cpl
               WHERE dt_mov >= CURRENT_DATE - INTERVAL '7 days'
               AND dt_mov IS NOT NULL
               GROUP BY DATE(dt_mov)
               ORDER BY data";
    }
    
    $result = pg_query($conn, $sql);
    while ($row = pg_fetch_assoc($result)) {
        $dados[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'dados' => $dados,
        'tipo' => $hasQuestionarios ? 'questionarios' : 'comercial'
    ]);
    exit();
}

// GET /api/relatorios/top-clientes - Top 5 clientes
if ($method === 'GET' && $action === 'top-clientes') {
    $conn = $db->getConnection();
    
    $sqlCheckQuest = "SELECT COUNT(*) as total FROM glb_questionarios";
    $resultCheck = pg_query($conn, $sqlCheckQuest);
    $hasQuestionarios = pg_fetch_assoc($resultCheck)['total'] > 0;
    
    if ($hasQuestionarios) {
        // Top clientes por atendimentos
        $sql = "SELECT p.cd_pessoa, p.nm_fant, 
               COUNT(r.cd_questionario_resposta) as total_atendimentos,
               MAX(r.dt_resposta) as ultimo_atendimento
               FROM glb_questionario_respostas r
               INNER JOIN prc_pessoa p ON r.cd_pessoa = p.cd_pessoa
               GROUP BY p.cd_pessoa, p.nm_fant
               ORDER BY total_atendimentos DESC
               LIMIT 5";
    } else {
        // Top clientes por compras
        $sql = "SELECT p.cd_pessoa, p.nm_fant,
               COUNT(DISTINCT l.cd_lanc_cpl) as total_atendimentos,
               MAX(l.dt_mov) as ultimo_atendimento
               FROM rc_lanc_cpl l
               INNER JOIN prc_pessoa p ON l.cd_pessoa = p.cd_pessoa
               WHERE l.dt_mov IS NOT NULL
               GROUP BY p.cd_pessoa, p.nm_fant
               ORDER BY total_atendimentos DESC
               LIMIT 5";
    }
    
    $result = pg_query($conn, $sql);
    $clientes = [];
    while ($row = pg_fetch_assoc($result)) {
        $clientes[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'tipo' => $hasQuestionarios ? 'questionarios' : 'comercial'
    ]);
    exit();
}

// GET /api/relatorios/vendas - Relatório de vendas
if ($method === 'GET' && $action === 'vendas') {
    $dataInicio = $_GET['dataInicio'] ?? date('Y-m-01');
    $dataFim = $_GET['dataFim'] ?? date('Y-m-d');
    
    $sql = "SELECT 
                DATE(v.dt_pedido) as data,
                COUNT(DISTINCT v.nr_pedido) as total_vendas,
                SUM(vi.qt_produto * vi.vl_unitario) as valor_total,
                COUNT(DISTINCT v.cd_cliente) as clientes_distintos
            FROM ped_vd v
            INNER JOIN ped_vd_it vi ON vi.nr_pedido = v.nr_pedido
            WHERE v.dt_pedido BETWEEN $1 AND $2
            GROUP BY DATE(v.dt_pedido)
            ORDER BY DATE(v.dt_pedido) DESC";
    
    $result = pg_query_params($db->getConnection(), $sql, [$dataInicio, $dataFim]);
    $vendas = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $vendas[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'vendas' => $vendas
    ]);
    exit();
}

// GET /api/relatorios/top-produtos - Top produtos mais vendidos
if ($method === 'GET' && $action === 'top-produtos') {
    $limite = $_GET['limite'] ?? 10;
    $dataInicio = $_GET['dataInicio'] ?? date('Y-m-01');
    $dataFim = $_GET['dataFim'] ?? date('Y-m-d');
    
    $sql = "SELECT 
                p.cd_produto,
                p.nm_produto,
                m.nm_marca,
                SUM(vi.qt_produto) as quantidade_vendida,
                SUM(vi.qt_produto * vi.vl_unitario) as valor_total
            FROM ped_vd_it vi
            INNER JOIN ped_vd v ON v.nr_pedido = vi.nr_pedido
            INNER JOIN est_produto p ON p.cd_produto = vi.cd_produto
            LEFT JOIN est_produto_marca m ON m.cd_marca = p.cd_marca
            WHERE v.dt_pedido BETWEEN $1 AND $2
            GROUP BY p.cd_produto, p.nm_produto, m.nm_marca
            ORDER BY quantidade_vendida DESC
            LIMIT $3";
    
    $result = pg_query_params($db->getConnection(), $sql, [$dataInicio, $dataFim, $limite]);
    $produtos = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $produtos[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'produtos' => $produtos
    ]);
    exit();
}

http_response_code(404);
echo json_encode(['success' => false, 'error' => 'Endpoint não encontrado']);
