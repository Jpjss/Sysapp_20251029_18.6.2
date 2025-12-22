<?php
/**
 * Script para limpar sessão
 */

// Inicia sessão
session_start();

// Destrói sessão
$_SESSION = [];

// Remove cookie de sessão
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();

// Redireciona para login
header('Location: http://localhost:8000/usuarios/login');
exit;
?>
