<?php
/**
 * Teste que força o reload de todos os arquivos
 */

// Limpa qualquer cache de require/include
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Remove todas as classes carregadas
spl_autoload_unregister('spl_autoload');

// Recarrega TUDO do zero
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
require_once 'models/Usuario.php';

echo "=== TESTE FORÇADO COM RELOAD ===\n\n";

// Cria nova instância
$Usuario = new Usuario();

echo "1. Testando findByLogin('teste'):\n";
$result = $Usuario->findByLogin('teste');
echo "   Resultado: ";
print_r($result);

if ($result && $result['cd_usuario'] == 4) {
    echo "   ✅ CORRETO! Usuário ID 4\n\n";
    
    echo "2. Testando findForAuth(4):\n";
    $usuario = $Usuario->findForAuth(4);
    echo "   Nome: {$usuario['nome_usuario']}\n";
    echo "   Senha: {$usuario['senha_usuario']}\n\n";
    
    echo "3. Comparando senha:\n";
    if ('teste123' === $usuario['senha_usuario']) {
        echo "   ✅ Senha CORRETA!\n\n";
        echo "🎉 TUDO OK! O usuário 'teste' pode fazer login!\n";
    } else {
        echo "   ❌ Senha INCORRETA\n";
        echo "   Esperado: teste123\n";
        echo "   Recebido: {$usuario['senha_usuario']}\n";
    }
} else {
    echo "   ❌ ERRO! Retornou usuário ID " . ($result['cd_usuario'] ?? 'NULL') . " em vez de 4\n";
}
