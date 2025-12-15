<?php
/**
 * TESTE REAL DE LOGIN - Debug completo
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TESTE DE LOGIN - DEBUG</h1>";
echo "<pre>";

// Carregar configurações
define('BASE_PATH', __DIR__);
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$conn) {
    echo "❌ ERRO: Não conseguiu conectar ao banco!\n";
    exit;
}
echo "✅ Conexão com banco OK\n\n";

// Dados do login
$login = 'diaazze';
$senha = 'diaazze123';

echo "=== TESTANDO LOGIN: $login / $senha ===\n\n";

// PASSO 1: Buscar usuário por login
echo "PASSO 1: Buscando usuário...\n";
$sql = "SELECT cd_usuario FROM sysapp_config_user 
        WHERE LOWER(ds_email) = LOWER('$login') 
           OR LOWER(ds_login) = LOWER('$login') 
           OR LOWER(nm_usuario) = LOWER('$login')";
echo "SQL: $sql\n";
$result = pg_query($conn, $sql);
$configUser = pg_fetch_assoc($result);

if (!$configUser) {
    echo "❌ Usuário não encontrado!\n";
    exit;
}
echo "✅ Usuário encontrado: cd_usuario = {$configUser['cd_usuario']}\n\n";

$cd_usuario = $configUser['cd_usuario'];

// PASSO 2: Buscar dados completos
echo "PASSO 2: Buscando dados do usuário...\n";
$sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_senha as senha_usuario 
        FROM sysapp_config_user 
        WHERE cd_usuario = $cd_usuario AND fg_ativo = 'S'";
echo "SQL: $sql\n";
$result = pg_query($conn, $sql);
$usuario = pg_fetch_assoc($result);

if (!$usuario) {
    echo "❌ Usuário inativo ou não encontrado!\n";
    
    // Debug extra
    $sql2 = "SELECT fg_ativo FROM sysapp_config_user WHERE cd_usuario = $cd_usuario";
    $result2 = pg_query($conn, $sql2);
    $status = pg_fetch_assoc($result2);
    echo "   Status atual: fg_ativo = " . ($status['fg_ativo'] ?? 'NULL') . "\n";
    exit;
}
echo "✅ Dados encontrados: {$usuario['nome_usuario']}\n";
echo "   Senha no banco: {$usuario['senha_usuario']}\n\n";

// PASSO 3: Verificar senha
echo "PASSO 3: Verificando senha...\n";
echo "   Senha informada: $senha\n";
echo "   Senha no banco: {$usuario['senha_usuario']}\n";

$senhaCorreta = ($senha === $usuario['senha_usuario']);
echo "   Match texto plano: " . ($senhaCorreta ? "SIM" : "NÃO") . "\n";

if (!$senhaCorreta) {
    // Tenta MD5
    $md5 = md5($senha);
    $senhaCorreta = ($md5 === $usuario['senha_usuario']);
    echo "   Match MD5: " . ($senhaCorreta ? "SIM" : "NÃO") . " (hash: $md5)\n";
}

if (!$senhaCorreta) {
    echo "❌ Senha incorreta!\n";
    exit;
}
echo "✅ Senha correta!\n\n";

// PASSO 4: Buscar empresas
echo "PASSO 4: Buscando empresas do usuário...\n";
$sql = "SELECT DISTINCT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario";
echo "SQL: $sql\n";
$result = pg_query($conn, $sql);
$empresas = pg_fetch_all($result);

if (empty($empresas)) {
    echo "❌ Usuário sem empresas!\n";
    exit;
}
echo "✅ Empresas: " . count($empresas) . "\n";
print_r($empresas);
echo "\n";

// PASSO 5: Buscar info das empresas
echo "PASSO 5: Buscando info das empresas...\n";
$cd_empresas = implode(',', array_column($empresas, 'cd_empresa'));
$sql = "SELECT ce.cd_empresa, ce.nm_empresa as nome_empresa, ce.ds_host as hostname_banco, 
               ce.ds_banco as nome_banco, ce.ds_usuario as usuario_banco, 
               ce.ds_senha as senha_banco, ce.ds_porta as porta_banco
        FROM sysapp_config_empresas ce
        INNER JOIN sysapp_config_user_empresas cue 
            ON ce.cd_empresa = cue.cd_empresa
        WHERE cue.cd_usuario = $cd_usuario 
        AND ce.cd_empresa IN ($cd_empresas)
        ORDER BY ce.nm_empresa";
echo "SQL: $sql\n";
$result = pg_query($conn, $sql);
$infoDb = pg_fetch_all($result);

if (empty($infoDb)) {
    echo "❌ Info das empresas não encontrada!\n";
    exit;
}
echo "✅ Info das empresas:\n";
print_r($infoDb);
echo "\n";

// PASSO 6: Buscar permissões
echo "PASSO 6: Buscando permissões...\n";
$sql = "SELECT nm_interface as nome_interface FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario";
echo "SQL: $sql\n";
$result = pg_query($conn, $sql);
$permissoes_raw = pg_fetch_all($result);

if (empty($permissoes_raw)) {
    echo "❌ Sem permissões!\n";
    exit;
}

$permissoes = [];
foreach ($permissoes_raw as $row) {
    $permissoes[] = $row['nome_interface'];
}
echo "✅ Permissões: " . implode(', ', $permissoes) . "\n\n";

echo "=".str_repeat("=", 50)."\n";
echo "✅ TODOS OS PASSOS PASSARAM!\n";
echo "=".str_repeat("=", 50)."\n\n";

echo "O login DEVERIA funcionar. Se não funciona, o problema está:\n";
echo "1. Na sessão PHP\n";
echo "2. No redirecionamento\n";
echo "3. No layout/view\n\n";

// Testar sessão
echo "=== TESTANDO SESSÃO ===\n";
session_start();
$_SESSION['teste'] = 'funcionando';
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . " (1=disabled, 2=active)\n";
echo "Session save path: " . session_save_path() . "\n";

if (session_save_path() === '') {
    echo "⚠️  Save path vazio - usando temp do sistema\n";
}

echo "\n</pre>";

// Form de teste
echo "<hr>";
echo "<h2>Formulário de Teste Direto</h2>";
echo "<form method='POST' action='/usuarios/login'>";
echo "<input type='text' name='email' value='diaazze' placeholder='Login'><br><br>";
echo "<input type='password' name='senha' value='diaazze123' placeholder='Senha'><br><br>";
echo "<button type='submit'>Testar Login Real</button>";
echo "</form>";
