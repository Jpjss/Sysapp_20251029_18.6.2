<?php
session_start();

require_once 'config/database.php';

echo "=== TESTE DE LOGIN ===\n\n";

// Simula POST do formulário
$_POST['email'] = 'admin@sysapp.com';
$_POST['senha'] = 'admin';

$email = $_POST['email'];
$senha = $_POST['senha'];

echo "Tentando login com:\n";
echo "Email: $email\n";
echo "Senha: $senha\n\n";

$db = Database::getInstance();
$conn = $db->getConnection();

// Busca usuário
$sql = "SELECT * FROM sysapp_config_user WHERE ds_email = $1";
$result = pg_query_params($conn, $sql, [$email]);

if (!$result) {
    echo "ERRO na query: " . pg_last_error($conn) . "\n";
    exit;
}

$user = pg_fetch_assoc($result);

if ($user) {
    echo "✓ Usuário encontrado!\n";
    echo "ID: {$user['cd_usuario']}\n";
    echo "Nome: {$user['nm_usuario']}\n";
    echo "Senha no DB: {$user['ds_senha']}\n\n";
    
    // Testa validação de senha
    $senhaCorreta = false;
    
    // 1. Senha plana
    if ($user['ds_senha'] === $senha) {
        echo "✓ SENHA VÁLIDA (texto plano)\n";
        $senhaCorreta = true;
    }
    // 2. MD5
    elseif ($user['ds_senha'] === md5($senha)) {
        echo "✓ SENHA VÁLIDA (MD5)\n";
        $senhaCorreta = true;
    }
    // 3. SHA1 com salt
    elseif ($user['ds_senha'] === sha1('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi' . $senha)) {
        echo "✓ SENHA VÁLIDA (SHA1+SALT)\n";
        $senhaCorreta = true;
    }
    else {
        echo "✗ SENHA INVÁLIDA\n";
        echo "Tentativas:\n";
        echo "  - Plana: " . $senha . " != {$user['ds_senha']}\n";
        echo "  - MD5: " . md5($senha) . " != {$user['ds_senha']}\n";
        echo "  - SHA1: " . sha1('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi' . $senha) . " != {$user['ds_senha']}\n";
    }
    
    if ($senhaCorreta) {
        echo "\n=== LOGIN SERIA ACEITO ===\n";
    } else {
        echo "\n=== LOGIN SERIA REJEITADO ===\n";
    }
} else {
    echo "✗ Usuário NÃO encontrado com email: $email\n";
}
