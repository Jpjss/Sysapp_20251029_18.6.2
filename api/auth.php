<?php
/**
 * API de Autenticação
 */

// Conecta ao banco padrão (sysapp)
$db = Database::getInstance();
$db->connect();

// GET /api/auth/session - Verifica sessão ativa
if ($method === 'GET' && $action === 'session') {
    if (Session::check('Dados.id_usuario')) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => Session::read('Dados.id_usuario'),
                'nome' => Session::read('Dados.nome'),
                'email' => Session::read('Dados.email'),
                'tipo' => Session::read('Dados.tipo'),
                'empresas' => Session::read('Dados.database') ?? []
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'authenticated' => false
        ]);
    }
    exit();
}

// POST /api/auth/login - Fazer login
if ($method === 'POST' && $action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $login = $input['email'] ?? $input['login'] ?? '';
    $senha = $input['senha'] ?? '';
    
    if (empty($login) || empty($senha)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Login/Email e senha são obrigatórios'
        ]);
        exit();
    }
    
    // Busca usuário no banco (ds_login ou ds_email)
    $sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_senha, ds_email 
            FROM sysapp_config_user 
            WHERE (LOWER(ds_login) = LOWER($1) OR LOWER(ds_email) = LOWER($1))
            AND fg_ativo = 'S'";
    $result = pg_query_params($db->getConnection(), $sql, [$login]);
    
    if (!$result || pg_num_rows($result) === 0) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Credenciais inválidas'
        ]);
        exit();
    }
    
    $usuario = pg_fetch_assoc($result);
    
    // Verifica senha (pode ser MD5 ou texto plano dependendo do cadastro)
    $senhaCorreta = false;
    
    // Tenta MD5
    if (md5($senha) === $usuario['ds_senha']) {
        $senhaCorreta = true;
    }
    // Tenta senha direta
    elseif ($senha === $usuario['ds_senha']) {
        $senhaCorreta = true;
    }
    
    if (!$senhaCorreta) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Credenciais inválidas'
        ]);
        exit();
    }
    
    $cd_usuario = $usuario['cd_usuario'];
    
    // Busca empresas do usuário
    $sqlEmpresas = "SELECT ce.cd_empresa as id, ce.nm_empresa, ce.ds_host as host, 
                           ce.ds_banco as database, ce.ds_usuario as usuario, 
                           ce.ds_senha as senha, ce.ds_porta as porta
                    FROM sysapp_config_empresas ce
                    INNER JOIN sysapp_config_user_empresas ue ON ue.cd_empresa = ce.cd_empresa
                    WHERE ue.cd_usuario = $1
                    ORDER BY ce.nm_empresa";
    $resultEmpresas = pg_query_params($db->getConnection(), $sqlEmpresas, [$cd_usuario]);
    
    $empresas = [];
    while ($empresa = pg_fetch_assoc($resultEmpresas)) {
        $empresas[] = $empresa;
    }
    
    // Cria sessão
    Session::write('Dados.id_usuario', $cd_usuario);
    Session::write('Dados.nome', $usuario['nm_usuario']);
    Session::write('Dados.email', $usuario['ds_email'] ?: $usuario['ds_login']);
    Session::write('Dados.login', $usuario['ds_login']);
    Session::write('Dados.tipo', 'admin'); // Ajuste conforme necessário
    Session::write('Dados.database', $empresas);
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $cd_usuario,
            'nome' => $usuario['nm_usuario'],
            'email' => $usuario['ds_email'] ?: $usuario['ds_login'],
            'login' => $usuario['ds_login'],
            'tipo' => 'admin',
            'empresas' => $empresas
        ]
    ]);
    exit();
}

// POST /api/auth/logout - Fazer logout
if ($method === 'POST' && $action === 'logout') {
    Session::destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
    ]);
    exit();
}

// Endpoint não encontrado
http_response_code(404);
echo json_encode([
    'success' => false,
    'error' => 'Endpoint de autenticação não encontrado'
]);
