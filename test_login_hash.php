<?php
// Carregar configurações
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Security.php';

echo "<h1>Teste de Login</h1>";

$senha = 'admin';
$hashGerado = Security::hash($senha, 'md5', SECURITY_SALT);

echo "<p><strong>Senha:</strong> $senha</p>";
echo "<p><strong>Hash gerado pelo sistema:</strong> $hashGerado</p>";

// Conectar ao banco
$conn_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
$conn = pg_connect($conn_string);

if ($conn) {
    echo "<p style='color:green'>✓ Conectado ao banco</p>";
    
    $result = pg_query($conn, "SELECT cd_usuario, nm_usuario, ds_login, ds_senha FROM sysapp_config_user WHERE ds_login='admin'");
    $user = pg_fetch_assoc($result);
    
    if ($user) {
        echo "<p><strong>Hash no banco:</strong> " . $user['ds_senha'] . "</p>";
        
        if ($hashGerado === $user['ds_senha']) {
            echo "<h2 style='color:green'>✓ SUCESSO! A senha 'admin' está correta!</h2>";
            echo "<p>Você pode fazer login com:</p>";
            echo "<ul>";
            echo "<li><strong>Usuário:</strong> admin</li>";
            echo "<li><strong>Senha:</strong> admin</li>";
            echo "</ul>";
        } else {
            echo "<h2 style='color:red'>✗ ERRO! Os hashes não conferem!</h2>";
        }
    } else {
        echo "<p style='color:red'>✗ Usuário admin não encontrado no banco</p>";
    }
    
    pg_close($conn);
} else {
    echo "<p style='color:red'>✗ Erro ao conectar ao banco</p>";
}
