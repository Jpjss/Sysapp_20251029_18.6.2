<?php
/**
 * Router para servidor PHP embutido
 * Uso: php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Arquivos estáticos (CSS, JS, imagens, etc)
// Verifica se é um arquivo estático e se existe
if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js|svg|woff|woff2|ttf|eot)$/', $uri)) {
    // Normaliza o caminho para Windows
    $filePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uri);
    
    if (file_exists($filePath) && is_file($filePath)) {
        return false; // Serve o arquivo diretamente
    }
}

// Remove a primeira barra e define a URL
$url = ltrim($uri, '/');

// Se a URL não está vazia, define $_GET['url']
if (!empty($url)) {
    $_GET['url'] = $url;
}

require_once __DIR__ . '/index.php';
