<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Security.php';

echo "=== TESTANDO LOGIN BACKEND PHP ===\n\n";

$db = Database::getInstance();
$db->connect();

$email = 'admin';
$senha = 'admin';

echo "Tentando login com:\n";
echo "Email/Login: $email\n";
echo "Senha: $senha\n\n";

// 1. Busca na tabela sysapp_config_user (nova)
echo "1. Verificando sysapp_config_user...\n";
$sql = "SELECT cd_usuario, ds_login, ds_senha, ds_email FROM sysapp_config_user 
        WHERE (ds_login = $1 OR ds_email = $1) AND fg_ativo = 'S'";
$result = pg_query_params($db->getConnection(), $sql, [$email]);

if (pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);
    echo "✓ Usuário encontrado:\n";
    echo "  ID: {$user['cd_usuario']}\n";
    echo "  Login: {$user['ds_login']}\n";
    echo "  Email: {$user['ds_email']}\n";
    echo "  Senha no banco: {$user['ds_senha']}\n\n";
    
    // Testa senha sem hash
    if ($senha === $user['ds_senha']) {
        echo "✓ Senha TEXTO PLANO corresponde!\n";
    }
    
    // Testa MD5
    $md5_senha = md5($senha);
    echo "  MD5 da senha: $md5_senha\n";
    if ($md5_senha === $user['ds_senha']) {
        echo "✓ Senha MD5 corresponde!\n";
    }
    
    // Testa com SALT
    $senhaHash = Security::hash($senha, 'md5', SECURITY_SALT);
    echo "  Hash com SALT: $senhaHash\n";
    if ($senhaHash === $user['ds_senha']) {
        echo "✓ Senha com SALT corresponde!\n";
    }
    
    echo "\n";
} else {
    echo "✗ Usuário NÃO encontrado em sysapp_config_user\n\n";
}

// 2. Busca na tabela sysapp_usuario (antiga)
echo "2. Verificando sysapp_usuario (tabela antiga)...\n";
$sql2 = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'sysapp_usuario') as existe";
$result2 = pg_query($db->getConnection(), $sql2);
$tabelaExiste = pg_fetch_assoc($result2)['existe'] === 't';

if ($tabelaExiste) {
    $sql3 = "SELECT cd_usuario, email_usuario, senha_usuario FROM sysapp_usuario WHERE email_usuario = $1 OR login_usuario = $1";
    $result3 = pg_query_params($db->getConnection(), $sql3, [$email]);
    
    if (pg_num_rows($result3) > 0) {
        $user2 = pg_fetch_assoc($result3);
        echo "✓ Usuário encontrado na tabela antiga:\n";
        echo "  ID: {$user2['cd_usuario']}\n";
        echo "  Email: {$user2['email_usuario']}\n";
        echo "  Senha: {$user2['senha_usuario']}\n\n";
        
        $senhaHash2 = Security::hash($senha, 'md5', SECURITY_SALT);
        if ($senhaHash2 === $user2['senha_usuario']) {
            echo "✓ Senha com SALT corresponde na tabela antiga!\n";
        }
    } else {
        echo "✗ Usuário não encontrado na tabela antiga\n";
    }
} else {
    echo "✗ Tabela sysapp_usuario não existe\n";
}
