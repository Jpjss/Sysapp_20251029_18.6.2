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

// Se há configuração de banco na sessão (usuário já selecionou empresa), usa ela
if (Session::check('Config.database')) {
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    error_log("=== USANDO BANCO DA SESSÃO ===");
    error_log("Database: $database, Host: $host");
    
    $db->connect($host, $database, $user, $password, $port);
} else {
    // Senão, conecta ao banco padrão (sysapp)
    error_log("=== USANDO BANCO PADRÃO (sysapp) ===");
    $db->connect();
}

error_log("=== APLICAÇÃO INICIADA ===");
error_log("Banco de dados conectado: " . ($db->getConnection() ? "SIM" : "NÃO"));

// Cria e executa o router
$router = new Router();
$router->dispatch();
