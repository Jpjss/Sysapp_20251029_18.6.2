<?php
/**
 * SysApp - Sistema de Questionários
 * Versão PHP Puro - 18.6.2
 * 
 * Arquivo principal de entrada
 */

// Carrega configurações
require_once 'config/config.php';
require_once 'config/database.php';

// Carrega classes core
require_once 'core/Session.php';
require_once 'core/Security.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';

// Inicia sessão
Session::start();

// Conecta ao banco de dados
$db = Database::getInstance();

// DEBUG: Log da decisão de conexão
$dbConfigExists = Session::check('Config.database');
$sessionValid = Session::isValid();
file_put_contents(__DIR__ . '/login_debug.log', "\n[INDEX] Config.database: " . ($dbConfigExists ? 'SIM' : 'NÃO') . " | Session::isValid: " . ($sessionValid ? 'SIM' : 'NÃO') . "\n", FILE_APPEND);

// Se há configuração de banco na sessão (usuário já selecionou empresa), usa ela
if ($dbConfigExists) {
    file_put_contents(__DIR__ . '/login_debug.log', "[INDEX] Conectando ao banco da sessão: " . Session::read('Config.database') . "\n", FILE_APPEND);
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    $db->connect($host, $database, $user, $password, $port);
} else {
    // Senão, conecta ao banco padrão onde estão os usuários e configurações do sistema
    file_put_contents(__DIR__ . '/login_debug.log', "[INDEX] Conectando ao banco padrão do sistema\n", FILE_APPEND);
    $db->connect();
}

// Cria e executa o router
$router = new Router();
$router->dispatch();
