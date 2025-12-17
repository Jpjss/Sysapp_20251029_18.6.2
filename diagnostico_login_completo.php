<?php
/**
 * Diagnóstico Completo de Login
 * Simula exatamente o que acontece no navegador
 */

// Inicia sessão limpa
session_start();
session_destroy();
session_start();

// Define constantes
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

// Simula POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'teste';
$_POST['senha'] = 'teste123';

// Carrega classes
require_once 'config/database.php';
require_once 'core/Controller.php';
require_once 'core/Session.php';
require_once 'core/Security.php';
require_once 'models/Usuario.php';

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║       DIAGNÓSTICO COMPLETO DE LOGIN - USUÁRIO TESTE      ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

echo "📋 DADOS DO LOGIN:\n";
echo "   Email/Login: " . $_POST['email'] . "\n";
echo "   Senha: " . $_POST['senha'] . "\n\n";

$Usuario = new Usuario();

// PASSO 1: Buscar usuário
echo "▶️  PASSO 1: Buscando usuário...\n";
$configUser = $Usuario->findByLogin($_POST['email']);

if (!$configUser) {
    echo "   ❌ FALHOU: Usuário não encontrado!\n\n";
    
    // Debug: busca todas as formas possíveis
    echo "🔍 TENTANDO OUTRAS BUSCAS:\n";
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $email = strtolower($_POST['email']);
    
    echo "   - LOWER(ds_email) = '$email':\n";
    $r = pg_query($conn, "SELECT cd_usuario FROM sysapp_config_user WHERE LOWER(ds_email) = '$email'");
    echo "     Resultado: " . pg_num_rows($r) . " registros\n";
    
    echo "   - LOWER(ds_login) = '$email':\n";
    $r = pg_query($conn, "SELECT cd_usuario FROM sysapp_config_user WHERE LOWER(ds_login) = '$email'");
    echo "     Resultado: " . pg_num_rows($r) . " registros\n";
    
    echo "   - LOWER(nm_usuario) = '$email':\n";
    $r = pg_query($conn, "SELECT cd_usuario FROM sysapp_config_user WHERE LOWER(nm_usuario) = '$email'");
    echo "     Resultado: " . pg_num_rows($r) . " registros\n";
    
    echo "\n📊 TODOS OS USUÁRIOS:\n";
    $r = pg_query($conn, "SELECT cd_usuario, ds_login, ds_email, nm_usuario FROM sysapp_config_user ORDER BY cd_usuario");
    while ($u = pg_fetch_assoc($r)) {
        echo "   ID {$u['cd_usuario']}: login='{$u['ds_login']}' | email='{$u['ds_email']}' | nome='{$u['nm_usuario']}'\n";
    }
    
    exit;
}

echo "   ✅ Encontrado: ID = " . $configUser['cd_usuario'] . "\n\n";
$cd_usuario = $configUser['cd_usuario'];

// PASSO 2: Buscar dados para autenticação
echo "▶️  PASSO 2: Buscando dados de autenticação...\n";
$usuario = $Usuario->findForAuth($cd_usuario);

if (!$usuario) {
    echo "   ❌ FALHOU: Não encontrou dados do usuário!\n";
    exit;
}

echo "   ✅ Dados encontrados\n";
echo "   Nome: " . $usuario['nome_usuario'] . "\n";
echo "   Senha no banco: '" . $usuario['senha_usuario'] . "'\n";
echo "   Senha digitada: '" . $_POST['senha'] . "'\n\n";

// PASSO 3: Verificar senha
echo "▶️  PASSO 3: Verificando senha...\n";

if ($_POST['senha'] !== $usuario['senha_usuario']) {
    echo "   ❌ FALHOU: Senhas não conferem!\n";
    echo "   Comparação:\n";
    echo "   - Digitada: '" . $_POST['senha'] . "' (length: " . strlen($_POST['senha']) . ")\n";
    echo "   - No banco: '" . $usuario['senha_usuario'] . "' (length: " . strlen($usuario['senha_usuario']) . ")\n";
    
    // Testa com MD5
    $senhaMd5 = md5($_POST['senha']);
    echo "\n   Testando MD5: " . ($senhaMd5 === $usuario['senha_usuario'] ? 'MATCH ✅' : 'NO MATCH ❌') . "\n";
    
    // Testa com SHA1+SALT
    $senhaSha1 = sha1(SECURITY_SALT . $_POST['senha']);
    echo "   Testando SHA1+SALT: " . ($senhaSha1 === $usuario['senha_usuario'] ? 'MATCH ✅' : 'NO MATCH ❌') . "\n";
    
    exit;
}

echo "   ✅ Senha correta!\n\n";

// PASSO 4: Buscar empresas
echo "▶️  PASSO 4: Buscando empresas do usuário...\n";
$empresas = $Usuario->getEmpresas($cd_usuario);

if (empty($empresas)) {
    echo "   ❌ FALHOU: Usuário sem empresas!\n";
    exit;
}

echo "   ✅ Encontradas: " . count($empresas) . " empresa(s)\n";
foreach ($empresas as $emp) {
    echo "      - Empresa ID: " . $emp['cd_empresa'] . "\n";
}
echo "\n";

// PASSO 5: Buscar info das empresas
echo "▶️  PASSO 5: Buscando informações das empresas...\n";
$cd_empresas = array_column($empresas, 'cd_empresa');
$cd_empresas_str = implode(',', $cd_empresas);
$infoDb = $Usuario->getEmpresasInfo($cd_usuario, $cd_empresas_str);

if (empty($infoDb)) {
    echo "   ❌ FALHOU: Não encontrou informações das empresas!\n";
    exit;
}

echo "   ✅ Info carregada: " . count($infoDb) . " empresa(s)\n\n";

// PASSO 6: Buscar permissões
echo "▶️  PASSO 6: Buscando permissões...\n";
$permissoes = $Usuario->getPermissoes($cd_usuario);

if (empty($permissoes)) {
    echo "   ❌ FALHOU: Usuário sem permissões!\n";
    exit;
}

echo "   ✅ Permissões: " . implode(', ', $permissoes) . "\n\n";

// RESULTADO FINAL
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║               🎉 TODOS OS PASSOS OK! 🎉                  ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

echo "✅ O usuário 'teste' pode fazer login sem problemas!\n\n";

echo "📝 RESUMO:\n";
echo "   - Usuário: {$usuario['nome_usuario']} (ID: {$cd_usuario})\n";
echo "   - Empresas: " . count($infoDb) . "\n";
echo "   - Permissões: " . count($permissoes) . "\n\n";

echo "🔗 PRÓXIMO PASSO:\n";
echo "   1. Acesse: http://localhost:8000/usuarios/login\n";
echo "   2. Digite: teste / teste123\n";
echo "   3. Se ainda falhar, verifique:\n";
echo "      - Console do navegador (F12)\n";
echo "      - Network tab para ver a resposta do servidor\n";
echo "      - Cookies/sessão podem estar bloqueados\n";
