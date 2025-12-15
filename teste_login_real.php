<?php
/**
 * TESTE DE LOGIN REAL - SIMULA O PROCESSO COMPLETO
 */

// Simula o POST do formulário
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'diaazze';
$_POST['senha'] = 'diaazze123';

// Inicia sessão
session_start();

// Define constantes necessárias
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
define('APP_NAME', 'SYSAPP');
define('APP_VERSION', '2.0');
define('SECURITY_SALT', 'SysApp2024SecureKey!@#');

// Constantes do banco (sysapp)
define('DB_HOST', 'localhost');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_PORT', '5432');

require_once 'config/database.php';
require_once 'core/Controller.php';
require_once 'core/Session.php';
require_once 'core/Security.php';
require_once 'models/Usuario.php';
require_once 'controllers/UsuariosController.php';

echo "=" . str_repeat("=", 70) . "\n";
echo "TESTE DE LOGIN REAL\n";
echo "=" . str_repeat("=", 70) . "\n\n";

echo "POST Data:\n";
echo "  - email: " . $_POST['email'] . "\n";
echo "  - senha: " . $_POST['senha'] . "\n";
echo "  - REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

try {
    $controller = new UsuariosController();
    
    echo "1. Controller criado ✅\n";
    echo "2. Executando login()...\n\n";
    
    // Captura o output
    ob_start();
    $controller->login();
    $output = ob_get_clean();
    
    echo "3. Login executado ✅\n\n";
    
    // Verifica sessão
    if (Session::check('Questionarios.cd_usu')) {
        echo "✅ ✅ ✅ LOGIN BEM SUCEDIDO! ✅ ✅ ✅\n\n";
        echo "Dados da sessão:\n";
        echo "   - Usuário: " . Session::read('Questionarios.nm_usu') . "\n";
        echo "   - ID: " . Session::read('Questionarios.cd_usu') . "\n";
        echo "   - Hora: " . Session::read('Questionarios.hora_login') . "\n";
        
        if (Session::check('Config.empresa')) {
            echo "   - Empresa: " . Session::read('Config.empresa') . "\n";
            echo "   - Database: " . Session::read('Config.database') . "\n";
        }
        
        $perms = Session::read('Questionarios.permissoes');
        if ($perms) {
            echo "   - Permissões: " . implode(', ', $perms) . "\n";
        }
        
        echo "\n✅ PODE FAZER LOGIN NO NAVEGADOR!\n";
    } else {
        echo "❌ LOGIN FALHOU!\n\n";
        
        if (Session::check('flash')) {
            echo "Mensagem de erro: " . Session::read('flash.message') . "\n";
        }
        
        echo "\nOutput do controller:\n";
        echo $output;
    }
    
} catch (Exception $e) {
    echo "❌ ERRO CRÍTICO: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
