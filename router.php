<?php
/**
 * Router para servidor PHP embutido
 * Uso: php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Roteamento para API REST
if (strpos($uri, '/api/') === 0) {
    require_once __DIR__ . '/api/index.php';
    return true;
}

// Arquivos estáticos (CSS, JS, imagens, HTML, etc) e arquivos PHP de teste
// Verifica se é um arquivo estático/teste e se existe
if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js|svg|woff|woff2|ttf|eot|html|htm|php)$/', $uri)) {
    // Normaliza o caminho para Windows
    $filePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uri);
    
    if (file_exists($filePath) && is_file($filePath)) {
        return false; // Serve o arquivo diretamente
    }
}

// Arquivos PHP diretos na raiz (para testes e debug)
// Verifica se é um arquivo .php na raiz e se existe
if (preg_match('/^\/([^\/]+\.php)$/', $uri, $matches)) {
    $phpFile = __DIR__ . DIRECTORY_SEPARATOR . $matches[1];
    
    if (file_exists($phpFile) && is_file($phpFile)) {
        // Serve o arquivo PHP diretamente
        require_once $phpFile;
        return true;
    }
}

// Remove a primeira barra e define a URL
$url = ltrim($uri, '/');

// DEBUG: Log do roteamento
file_put_contents(__DIR__ . '/login_debug.log', "[router.php] URI: $uri | URL: $url | METHOD: {$_SERVER['REQUEST_METHOD']}\n", FILE_APPEND);

// Se a URL não está vazia, define $_GET['url']
if (!empty($url)) {
    $_GET['url'] = $url;
    file_put_contents(__DIR__ . '/login_debug.log', "[router.php] Setting \$_GET['url'] = $url\n", FILE_APPEND);
}

require_once __DIR__ . '/index.php';

