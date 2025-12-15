<?php
// Verificar hash da senha admin
$senha = 'admin';
$hash = md5($senha);

echo "Senha: $senha\n";
echo "Hash MD5: $hash\n\n";

// Conectar ao banco
$conn = pg_connect("host=localhost port=5432 dbname=sysapp user=postgres password=postgres");

if ($conn) {
    echo "Conexão OK\n\n";
    
    // Buscar usuário admin
    $result = pg_query($conn, "SELECT cd_usuario, nm_usuario, ds_login, ds_senha FROM sysapp_config_user WHERE ds_login = 'admin'");
    
    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        echo "Usuário encontrado:\n";
        echo "  ID: {$user['cd_usuario']}\n";
        echo "  Nome: {$user['nm_usuario']}\n";
        echo "  Login: {$user['ds_login']}\n";
        echo "  Senha (hash no banco): {$user['ds_senha']}\n";
        echo "  Senha (hash calculado): $hash\n";
        echo "  Senhas coincidem? " . ($user['ds_senha'] === $hash ? 'SIM' : 'NÃO') . "\n";
    } else {
        echo "Usuário admin não encontrado!\n";
    }
    
    pg_close($conn);
} else {
    echo "Erro ao conectar ao banco!\n";
}
