<?php
// Debug do login
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();
$db = Database::getInstance();
$db->connect();

$login = 'admin';
$senha = 'admin';

echo "=== TESTE DE LOGIN ===\n\n";
echo "Login informado: $login\n";
echo "Senha informada: $senha\n";
echo "Hash da senha: " . md5($senha) . "\n\n";

// Busca usuário
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_senha 
        FROM sysapp_config_user 
        WHERE LOWER(ds_login) = LOWER($1)";
        
echo "SQL: $sql\n";
echo "Parâmetro: [$login]\n\n";

$result = pg_query_params($db->getConnection(), $sql, [$login]);

if (!$result) {
    echo "ERRO na query: " . pg_last_error($db->getConnection()) . "\n";
    exit;
}

echo "Linhas retornadas: " . pg_num_rows($result) . "\n\n";

if (pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);
    echo "Usuário encontrado:\n";
    print_r($usuario);
    
    echo "\nComparação de senhas:\n";
    echo "  Hash informado: " . md5($senha) . "\n";
    echo "  Hash no banco:  " . $usuario['ds_senha'] . "\n";
    echo "  Coincidem? " . (md5($senha) === $usuario['ds_senha'] ? 'SIM' : 'NÃO') . "\n";
    
    if (md5($senha) === $usuario['ds_senha']) {
        echo "\n✓ LOGIN SUCESSO!\n";
    } else {
        echo "\n✗ SENHA INCORRETA!\n";
    }
} else {
    echo "✗ Usuário não encontrado!\n";
}
