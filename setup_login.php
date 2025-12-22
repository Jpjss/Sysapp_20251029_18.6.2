<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carrega as classes necesárias
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Security.php';

$db = Database::getInstance();

echo "<h1>Setup Completo do Login</h1>";

// 1. Verifica e cria usuário admin se não existir
echo "<h2>1. Verificando usuários</h2>";

$sql = "SELECT COUNT(*) as total FROM sysapp_config_user";
$result = $db->fetchOne($sql);
$total = (int)$result['total'];

if ($total == 0) {
    echo "<p style='color: orange;'>⚠️ Nenhum usuário encontrado. Criando admin...</p>";
    
    // Cria usuário admin
    $senha_hash = md5('admin123' . SECURITY_SALT);
    $sql = "INSERT INTO sysapp_config_user 
            (cd_usuario, nm_usuario, ds_login, ds_senha, ds_email, fg_ativo) 
            VALUES (1, 'Administrador', 'admin', '$senha_hash', 'admin@sysapp.com', 'S')";
    
    if ($db->query($sql)) {
        echo "<p style='color: green;'>✅ Usuário admin criado!</p>";
        echo "<p><strong>Login:</strong> admin</p>";
        echo "<p><strong>Senha:</strong> admin123</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar usuário</p>";
    }
} else {
    echo "<p style='color: green;'>✅ $total usuário(s) encontrado(s)</p>";
    
    // Lista usuários
    $sql = "SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user LIMIT 5";
    $usuarios = $db->fetchAll($sql);
    
    echo "<ul>";
    foreach ($usuarios as $user) {
        echo "<li>" . htmlspecialchars($user['nm_usuario']) . " (" . htmlspecialchars($user['ds_login']) . ")</li>";
    }
    echo "</ul>";
}

// 2. Verifica tabelas necessárias
echo "<h2>2. Verificando estrutura do banco</h2>";

$tabelas_necessarias = [
    'sysapp_config_user',
    'sysapp_config_user_empresas',
    'sysapp_config_empresas',
    'sysapp_interfaces'
];

foreach ($tabelas_necessarias as $tabela) {
    $sql = "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_name = '$tabela'
    )";
    $result = $db->fetchOne($sql);
    $exists = $result[0] === 't' ? true : false;
    echo "<p>" . ($exists ? "✅" : "❌") . " Tabela: $tabela</p>";
}

// 3. Teste de login
echo "<h2>3. Teste de Login</h2>";

// Pega primeiro usuário para teste
$sql = "SELECT cd_usuario, ds_login, ds_senha FROM sysapp_config_user LIMIT 1";
$user = $db->fetchOne($sql);

if ($user) {
    echo "<p>Testando com usuário: <strong>" . htmlspecialchars($user['ds_login']) . "</strong></p>";
    
    // Testa senha admin123
    $hash_test = md5('admin123' . SECURITY_SALT);
    if ($hash_test === $user['ds_senha']) {
        echo "<p style='color: green;'>✅ Senha admin123 é VÁLIDA para este usuário!</p>";
    } else {
        echo "<p style='color: red;'>❌ Senha admin123 não corresponde</p>";
    }
}

// 4. Link para login
echo "<h2>4. Acessar Sistema</h2>";
echo "<p><a href='/usuarios/login' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Ir para Login</a></p>";

?>
