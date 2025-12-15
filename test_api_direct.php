<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simula ambiente de API
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/empresas';

$method = 'GET';
$action = '';

// Carrega dependências
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

echo "=== TESTE API EMPRESAS (simulado) ===\n\n";

try {
    // Executa o código da API
    include __DIR__ . '/api/empresas.php';
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
