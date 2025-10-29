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

// Cria e executa o router
$router = new Router();
$router->dispatch();
