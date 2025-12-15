<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect();

echo "=== CORRIGINDO USUÁRIOS ===\n\n";

// 1. Atualizar admin com senha em texto plano
$sql = "UPDATE sysapp_config_user 
        SET ds_senha = $1 
        WHERE ds_login = 'admin'";
pg_query_params($db->getConnection(), $sql, ['admin']);
echo "✓ Admin atualizado com senha 'admin' (texto plano)\n";

// 2. Verificar se diaazze@sys.io já existe
$check = pg_query_params(
    $db->getConnection(),
    "SELECT cd_usuario FROM sysapp_config_user WHERE ds_email = $1",
    ['diaazze@sys.io']
);

if (pg_num_rows($check) > 0) {
    // Atualizar
    $sql = "UPDATE sysapp_config_user 
            SET ds_senha = $1,
                ds_login = 'diaazze',
                nm_usuario = 'Diaazze',
                fg_ativo = 'S'
            WHERE ds_email = $2";
    pg_query_params($db->getConnection(), $sql, ['c6WUh^xH3H5gH64r2iOIPtHXHVAvRA', 'diaazze@sys.io']);
    echo "✓ Usuário diaazze@sys.io atualizado\n";
} else {
    // Inserir
    $sql = "INSERT INTO sysapp_config_user 
            (nm_usuario, ds_login, ds_senha, ds_email, fg_ativo, dt_cadastro) 
            VALUES ($1, $2, $3, $4, 'S', NOW())";
    pg_query_params($db->getConnection(), $sql, [
        'Diaazze',
        'diaazze',
        'c6WUh^xH3H5gH64r2iOIPtHXHVAvRA',
        'diaazze@sys.io'
    ]);
    echo "✓ Usuário diaazze@sys.io criado\n";
}

echo "\n=== VERIFICANDO USUÁRIOS ===\n\n";

$result = pg_query($db->getConnection(), 
    "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha, fg_ativo 
     FROM sysapp_config_user 
     WHERE ds_login IN ('admin', 'diaazze') OR ds_email = 'diaazze@sys.io'
     ORDER BY cd_usuario");

while ($row = pg_fetch_assoc($result)) {
    echo "ID: {$row['cd_usuario']}\n";
    echo "Nome: {$row['nm_usuario']}\n";
    echo "Login: {$row['ds_login']}\n";
    echo "Email: {$row['ds_email']}\n";
    echo "Senha: {$row['ds_senha']}\n";
    echo "Ativo: {$row['fg_ativo']}\n";
    echo "---\n";
}
