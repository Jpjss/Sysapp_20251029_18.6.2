<?php
/**
 * API de Empresas
 * Gerenciado pelo api/index.php
 */

// Conecta ao banco master  
$dbInstance = Database::getInstance();
$db = $dbInstance->getConnection();

// GET /api/empresas - Lista todas as empresas ativas
if ($method === 'GET' && empty($action)) {
    $sql = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_porta, fg_ativo 
            FROM sysapp_config_empresas 
            WHERE fg_ativo = 'S' 
            ORDER BY nm_empresa";
    
    $result = pg_query($db, $sql);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao buscar empresas: ' . pg_last_error($db)
        ]);
        exit();
    }
    
    $empresas = [];
    while ($row = pg_fetch_assoc($result)) {
        $empresas[] = [
            'id' => (int)$row['cd_empresa'],
            'nome' => $row['nm_empresa'],
            'host' => $row['ds_host'],
            'banco' => $row['ds_banco'],
            'porta' => $row['ds_porta']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'empresas' => $empresas
    ]);
    exit();
}

// POST /api/empresas/selecionar - Seleciona uma empresa
if ($method === 'POST' && $action === 'selecionar') {
    $input = json_decode(file_get_contents('php://input'), true);
    $empresaId = $input['empresaId'] ?? $input['empresa_id'] ?? '';
    
    if (empty($empresaId)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'ID da empresa é obrigatório'
        ]);
        exit();
    }
    
    // Busca dados da empresa no banco
    $sql = "SELECT * FROM sysapp_config_empresas WHERE cd_empresa = $1 AND fg_ativo = 'S'";
    $result = pg_query_params($db, $sql, [$empresaId]);
    
    if (!$result || pg_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Empresa não encontrada ou inativa'
        ]);
        exit();
    }
    
    $empresa = pg_fetch_assoc($result);
    
    // Salva na sessão
    Session::write('Config.cd_empresa', $empresa['cd_empresa']);
    Session::write('Config.empresa', $empresa['nm_empresa']);
    Session::write('Config.host', $empresa['ds_host']);
    Session::write('Config.database', $empresa['ds_banco']);
    Session::write('Config.user', $empresa['ds_usuario']);
    Session::write('Config.password', $empresa['ds_senha']);
    Session::write('Config.porta', $empresa['ds_porta']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Empresa selecionada com sucesso',
        'empresa' => [
            'id' => (int)$empresa['cd_empresa'],
            'nome' => $empresa['nm_empresa'],
            'database' => $empresa['ds_banco']
        ]
    ]);
    exit();
}

// GET /api/empresas/atual - Retorna empresa selecionada
if ($method === 'GET' && $action === 'atual') {
    if (Session::check('Config.cd_empresa')) {
        echo json_encode([
            'success' => true,
            'empresa' => [
                'id' => Session::read('Config.cd_empresa'),
                'nome' => Session::read('Config.empresa'),
                'database' => Session::read('Config.database')
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'empresa' => null
        ]);
    }
    exit();
}

// Nenhum endpoint correspondeu
http_response_code(404);
echo json_encode([
    'success' => false,
    'error' => 'Endpoint não encontrado',
    'method' => $method,
    'action' => $action
]);
