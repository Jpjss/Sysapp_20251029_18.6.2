<?php
// Teste direto da API de login
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Security.php';

Session::start();
$db = Database::getInstance();
$db->connect();

$login = 'diaazze@sys.io';
$senha = 'c6WUh^xH3H5gH64r2iOIPtHXHVAvRA';

echo "=== TESTE DE LOGIN API ===\n\n";
echo "Login: $login\n";
echo "Senha: $senha\n\n";

// Busca usuário
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_senha, ds_email 
        FROM sysapp_config_user 
        WHERE (LOWER(ds_login) = LOWER($1) OR LOWER(ds_email) = LOWER($1))
        AND fg_ativo = 'S'";

$result = pg_query_params($db->getConnection(), $sql, [$login]);

echo "Query executada. Resultado: " . pg_num_rows($result) . " linhas\n\n";

if (pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);
    echo "Usuário encontrado:\n";
    print_r($usuario);
    
    echo "\n=== VALIDAÇÃO DE SENHA ===\n";
    echo "Senha fornecida: $senha\n";
    echo "Senha no banco: {$usuario['ds_senha']}\n";
    echo "MD5 da senha fornecida: " . md5($senha) . "\n\n";
    
    $senhaCorreta = false;
    
    // Tenta MD5
    if (md5($senha) === $usuario['ds_senha']) {
        echo "✓ Senha MD5 corresponde!\n";
        $senhaCorreta = true;
    }
    // Tenta senha direta
    elseif ($senha === $usuario['ds_senha']) {
        echo "✓ Senha em texto plano corresponde!\n";
        $senhaCorreta = true;
    } else {
        echo "✗ Senha não corresponde (nem MD5 nem texto plano)\n";
    }
    
    if ($senhaCorreta) {
        echo "\n✓✓✓ LOGIN SUCESSO! ✓✓✓\n";
    } else {
        echo "\n✗✗✗ LOGIN FALHOU! ✗✗✗\n";
    }
} else {
    echo "✗ Nenhum usuário encontrado!\n";
}

// Também testa com admin
echo "\n\n=== TESTANDO COM ADMIN ===\n";
$login2 = 'admin';
$senha2 = 'admin';

$result2 = pg_query_params($db->getConnection(), $sql, [$login2]);

if (pg_num_rows($result2) > 0) {
    $usuario2 = pg_fetch_assoc($result2);
    echo "Usuário: {$usuario2['ds_login']}\n";
    echo "Senha no banco: {$usuario2['ds_senha']}\n";
    echo "Senha fornecida: $senha2\n";
    
    if ($senha2 === $usuario2['ds_senha']) {
        echo "✓ ADMIN LOGIN OK!\n";
    } else {
        echo "✗ ADMIN LOGIN FALHOU!\n";
    }
}
