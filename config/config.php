<?php
/**
 * Configurações Gerais do Sistema
 */

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 em produção com HTTPS

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro (desabilitado para produção)
error_reporting(0);
ini_set('display_errors', 0);

// Configurações do sistema
define('BASE_PATH', dirname(__DIR__));
define('APP_NAME', 'SysApp');
define('APP_VERSION', '18.6.2');

// Salt para criptografia (mesmo do CakePHP)
define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

// Configurações de banco de dados padrão
// IMPORTANTE: Ajuste estas configurações conforme seu ambiente
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres'); // Senha definida na instalação do PostgreSQL

// URLs
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Para servidor embutido do PHP, dirname retorna '/' então não precisamos dele
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseDir = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

define('BASE_URL', $protocol . '://' . $host . $baseDir);
