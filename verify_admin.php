<?php
/**
 * Verifica usuário admin
 */

require_once 'config/database.php';

$db = Database::getInstance();

echo "<h1>Verificação de Usuário Admin</h1>";
echo "<hr>";

// Verifica se usuário existe
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email, fg_ativo 
        FROM sysapp_config_user 
        WHERE ds_login = 'admin'";

$user = $db->fetchOne($sql);

if ($user) {
    echo "<p style='color: green;'><strong>✅ Usuário admin encontrado!</strong></p>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $user['cd_usuario'] . "</li>";
    echo "<li><strong>Nome:</strong> " . $user['nm_usuario'] . "</li>";
    echo "<li><strong>Login:</strong> " . $user['ds_login'] . "</li>";
    echo "<li><strong>Email:</strong> " . $user['ds_email'] . "</li>";
    echo "<li><strong>Ativo:</strong> " . $user['fg_ativo'] . "</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<h2>Credenciais de Acesso:</h2>";
    echo "<p style='font-size: 18px;'>";
    echo "<strong>Login:</strong> admin<br>";
    echo "<strong>Senha:</strong> admin";
    echo "</p>";
    
    echo "<hr>";
    echo "<p><a href='/usuarios/login' style='padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px;'>Ir para Login</a></p>";
} else {
    echo "<p style='color: red;'><strong>❌ Usuário admin NÃO encontrado!</strong></p>";
    echo "<p>O usuário não foi criado. Vou tentar criar agora...</p>";
    
    $sqlInsert = "INSERT INTO sysapp_config_user (nm_usuario, ds_login, ds_senha, ds_email, fg_ativo)
                  VALUES ('Administrador', 'admin', MD5('adminDYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi'), 'admin@sysapp.com', 'S')
                  ON CONFLICT (ds_login) DO NOTHING";
    
    if ($db->query($sqlInsert)) {
        echo "<p style='color: green;'><strong>✅ Usuário criado com sucesso!</strong></p>";
        echo "<p><strong>Login:</strong> admin</p>";
        echo "<p><strong>Senha:</strong> admin</p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Erro ao criar usuário!</strong></p>";
        echo "<p>" . pg_last_error($db->getConnection()) . "</p>";
    }
}

echo "<hr>";
echo "<h2>Todas as tabelas sysapp:</h2>";
$tables = $db->fetchAll("SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'sysapp_%' ORDER BY table_name");
if ($tables) {
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table['table_name'] . "</li>";
    }
    echo "</ul>";
}
