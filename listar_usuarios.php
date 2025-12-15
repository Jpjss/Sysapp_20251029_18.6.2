<?php
require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== USUÁRIOS CADASTRADOS ===\n\n";

$result = pg_query($conn, 'SELECT cd_usuario, nm_usuario, ds_email, ds_senha FROM sysapp_config_user ORDER BY cd_usuario');

while ($row = pg_fetch_assoc($result)) {
    echo "ID: " . $row['cd_usuario'] . "\n";
    echo "Nome: " . $row['nm_usuario'] . "\n";
    echo "Email: " . $row['ds_email'] . "\n";
    echo "Senha (hash): " . substr($row['ds_senha'], 0, 40) . "...\n";
    echo "---\n";
}

echo "\n=== TESTE DE LOGIN ===\n\n";

// Testa login com admin/admin
$email = 'admin';
$senha = 'admin';

$sql = "SELECT * FROM sysapp_config_user WHERE ds_email = $1";
$result = pg_query_params($conn, $sql, [$email]);
$user = pg_fetch_assoc($result);

if ($user) {
    echo "Usuário encontrado: {$user['nm_usuario']}\n";
    echo "Senha no banco: " . substr($user['ds_senha'], 0, 40) . "...\n\n";
    
    // Testa diferentes formatos
    $senhaPlain = $senha;
    $senhaMd5 = md5($senha);
    $senhaSha1Salt = sha1('DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi' . $senha);
    
    echo "Tentativa 1 - Senha plana: " . ($user['ds_senha'] === $senhaPlain ? 'OK' : 'FALHOU') . "\n";
    echo "Tentativa 2 - MD5: " . ($user['ds_senha'] === $senhaMd5 ? 'OK' : 'FALHOU') . "\n";
    echo "Tentativa 3 - SHA1+SALT: " . ($user['ds_senha'] === $senhaSha1Salt ? 'OK' : 'FALHOU') . "\n";
    
    echo "\nFormato correto: ";
    if ($user['ds_senha'] === $senhaPlain) echo "PLANA";
    elseif ($user['ds_senha'] === $senhaMd5) echo "MD5";
    elseif ($user['ds_senha'] === $senhaSha1Salt) echo "SHA1+SALT";
    else echo "DESCONHECIDO";
    echo "\n";
} else {
    echo "ERRO: Usuário não encontrado!\n";
}
