<?php
/**
 * API REST - SysApp
 * Ponto de entrada para todas as requisições da API
 */

// Headers CORS
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=UTF-8');

// Responde ao preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Carrega dependências
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Security.php';

// Inicia sessão
Session::start();

// Obtém a rota da requisição
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove prefixo /api/ da URI
$route = preg_replace('#^/api/?#', '', parse_url($uri, PHP_URL_PATH));
$route = trim($route, '/');

// Separa a rota em partes
$parts = explode('/', $route);
$resource = $parts[0] ?? '';
$action = $parts[1] ?? '';
$param = $parts[2] ?? '';

// Roteamento da API
try {
    switch ($resource) {
        case 'auth':
            require_once __DIR__ . '/auth.php';
            break;
            
        case 'empresas':
            require_once __DIR__ . '/empresas.php';
            break;
            
        case 'questionarios':
            require_once __DIR__ . '/questionarios.php';
            break;
            
        case 'relatorios':
            require_once __DIR__ . '/relatorios.php';
            break;
            
        case 'usuarios':
            require_once __DIR__ . '/usuarios.php';
            break;
            
        case 'xml':
            require_once __DIR__ . '/xml.php';
            break;
            
        case 'clientes':
            require_once __DIR__ . '/clientes.php';
            break;
            
        case 'marcas_vendas':
        case 'marcas_vendas.php':
            require_once __DIR__ . '/marcas_vendas.php';
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Endpoint não encontrado',
                'route' => $route
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
