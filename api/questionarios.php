<?php
/**
 * API de Questionários
 */

// Verifica autenticação
if (!Session::check('Dados.id_usuario')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit();
}

// Verifica se empresa foi selecionada
if (!Session::check('Config.database')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nenhuma empresa selecionada']);
    exit();
}

require_once __DIR__ . '/../Model/GlbQuestionario.php';
require_once __DIR__ . '/../Model/Cliente.php';

$db = Database::getInstance();
$empresaDb = Session::read('Config.database');
$db->connect(
    Session::read('Config.host'),
    $empresaDb,
    Session::read('Config.user'),
    Session::read('Config.password'),
    Session::read('Config.porta')
);

// GET /api/questionarios - Lista questionários disponíveis
if ($method === 'GET' && empty($action)) {
    $questionarioModel = new GlbQuestionario();
    $questionarios = $questionarioModel->findAll();
    
    echo json_encode([
        'success' => true,
        'questionarios' => $questionarios
    ]);
    exit();
}

// GET /api/questionarios/pendentes - Lista atendimentos pendentes
if ($method === 'GET' && $action === 'pendentes') {
    $sql = "SELECT DISTINCT
                c.id,
                c.nome,
                c.fantasia,
                c.telefone,
                c.celular,
                c.email,
                TO_CHAR(c.data_ultimo_atendimento, 'DD/MM/YYYY') as ultimo_atendimento,
                COALESCE(c.qtde_compras, 0) as qtde_compras,
                COALESCE(c.valor_total_compras, 0) as valor_total_compras
            FROM clientes c
            WHERE c.ativo = true
            AND (c.data_ultimo_atendimento IS NULL 
                 OR c.data_ultimo_atendimento < CURRENT_DATE - INTERVAL '30 days')
            ORDER BY c.data_ultimo_atendimento ASC NULLS FIRST
            LIMIT 50";
    
    $result = $db->query($sql);
    $clientes = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $clientes[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes
    ]);
    exit();
}

// GET /api/questionarios/{id}/perguntas - Obtém perguntas de um questionário
if ($method === 'GET' && is_numeric($action)) {
    $questionarioId = (int)$action;
    
    $sql = "SELECT 
                p.id,
                p.ordem,
                p.pergunta,
                p.tipo_resposta,
                p.obrigatoria,
                p.opcoes
            FROM glb_questionario_perguntas p
            WHERE p.questionario_id = $1
            ORDER BY p.ordem ASC";
    
    $result = pg_query_params($db->getConnection(), $sql, [$questionarioId]);
    $perguntas = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $perguntas[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'perguntas' => $perguntas
    ]);
    exit();
}

// POST /api/questionarios/responder - Salva respostas de questionário
if ($method === 'POST' && $action === 'responder') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $clienteId = $input['clienteId'] ?? null;
    $questionarioId = $input['questionarioId'] ?? null;
    $respostas = $input['respostas'] ?? [];
    
    if (!$clienteId || !$questionarioId || empty($respostas)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
        exit();
    }
    
    $usuarioId = Session::read('Dados.id_usuario');
    
    // Inicia transação
    pg_query($db->getConnection(), 'BEGIN');
    
    try {
        // Insere atendimento
        $sqlAtendimento = "INSERT INTO glb_questionario_respostas 
            (questionario_id, cliente_id, usuario_id, data_atendimento, observacao)
            VALUES ($1, $2, $3, NOW(), $4)
            RETURNING id";
        
        $result = pg_query_params($db->getConnection(), $sqlAtendimento, [
            $questionarioId,
            $clienteId,
            $usuarioId,
            $input['observacao'] ?? ''
        ]);
        
        $atendimento = pg_fetch_assoc($result);
        $atendimentoId = $atendimento['id'];
        
        // Insere respostas
        foreach ($respostas as $resposta) {
            $sqlResposta = "INSERT INTO glb_questionario_respostas_itens 
                (atendimento_id, pergunta_id, resposta)
                VALUES ($1, $2, $3)";
            
            pg_query_params($db->getConnection(), $sqlResposta, [
                $atendimentoId,
                $resposta['perguntaId'],
                $resposta['resposta']
            ]);
        }
        
        // Atualiza data último atendimento do cliente
        $sqlUpdate = "UPDATE clientes SET data_ultimo_atendimento = NOW() WHERE id = $1";
        pg_query_params($db->getConnection(), $sqlUpdate, [$clienteId]);
        
        pg_query($db->getConnection(), 'COMMIT');
        
        echo json_encode([
            'success' => true,
            'atendimentoId' => $atendimentoId,
            'message' => 'Atendimento registrado com sucesso'
        ]);
        
    } catch (Exception $e) {
        pg_query($db->getConnection(), 'ROLLBACK');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit();
}

// GET /api/questionarios/historico - Histórico de atendimentos
if ($method === 'GET' && $action === 'historico') {
    $limite = $_GET['limite'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    
    $sql = "SELECT 
                r.id,
                r.data_atendimento,
                c.nome as cliente_nome,
                c.fantasia as cliente_fantasia,
                u.nome as usuario_nome,
                q.titulo as questionario_titulo
            FROM glb_questionario_respostas r
            INNER JOIN clientes c ON c.id = r.cliente_id
            INNER JOIN usuarios u ON u.id = r.usuario_id
            INNER JOIN glb_questionario q ON q.id = r.questionario_id
            ORDER BY r.data_atendimento DESC
            LIMIT $1 OFFSET $2";
    
    $result = pg_query_params($db->getConnection(), $sql, [$limite, $offset]);
    $historico = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $historico[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'historico' => $historico
    ]);
    exit();
}

http_response_code(404);
echo json_encode(['success' => false, 'error' => 'Endpoint não encontrado']);
