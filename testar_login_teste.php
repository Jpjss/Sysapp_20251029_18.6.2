<?php
/**
 * TESTE: Login com usuário recém-criado
 */

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'teste';
$_POST['senha'] = 'teste123';

session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
define('APP_NAME', 'SYSAPP');
define('APP_VERSION', '2.0');
define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
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

echo "=== TESTE DE LOGIN COM USUÁRIO 'teste' ===\n\n";
echo "Login: teste\n";
echo "Senha: teste123\n\n";

// Primeiro vamos testar manualmente o fluxo
require_once 'models/Usuario.php';
$Usuario = new Usuario();

echo "1. Buscando usuário...\n";
$configUser = $Usuario->findByLogin('teste');
if ($configUser) {
    echo "   ✅ Encontrado: ID = " . $configUser['cd_usuario'] . "\n";
    
    $cd_usuario = $configUser['cd_usuario'];
    
    echo "\n2. Buscando dados de auth...\n";
    $usuario = $Usuario->findForAuth($cd_usuario);
    if ($usuario) {
        echo "   ✅ Nome: {$usuario['nome_usuario']}\n";
        echo "   ✅ Senha no banco: {$usuario['senha_usuario']}\n";
        echo "   ✅ Senha digitada: teste123\n";
        echo "   ✅ Senhas conferem: " . ($usuario['senha_usuario'] === 'teste123' ? 'SIM' : 'NÃO') . "\n";
        
        echo "\n3. Buscando empresas...\n";
        $empresas = $Usuario->getEmpresas($cd_usuario);
        if (empty($empresas)) {
            echo "   ❌ NENHUMA EMPRESA!\n";
        } else {
            echo "   ✅ Empresas: " . count($empresas) . "\n";
            print_r($empresas);
        }
        
        echo "\n4. Buscando permissões...\n";
        $permissoes = $Usuario->getPermissoes($cd_usuario);
        if (empty($permissoes)) {
            echo "   ❌ NENHUMA PERMISSÃO!\n";
        } else {
            echo "   ✅ Permissões: " . implode(', ', $permissoes) . "\n";
        }
    }
} else {
    echo "   ❌ Usuário não encontrado!\n";
}

echo "\n\n=== AGORA TESTANDO COM O CONTROLLER ===\n\n";

try {
    $controller = new UsuariosController();
    
    ob_start();
    $controller->login();
    ob_end_clean();
    
    if (Session::check('Questionarios.cd_usu')) {
        echo "✅ ✅ ✅ LOGIN BEM SUCEDIDO! ✅ ✅ ✅\n\n";
        echo "Dados da sessão:\n";
        echo "   - Usuário: " . Session::read('Questionarios.nm_usu') . "\n";
        echo "   - ID: " . Session::read('Questionarios.cd_usu') . "\n";
        
        if (Session::check('Config.empresa')) {
            echo "   - Empresa: " . Session::read('Config.empresa') . "\n";
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
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
