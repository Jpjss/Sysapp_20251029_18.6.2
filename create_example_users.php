<?php
// Criar usuário de exemplo diaazze@sys.io
$conn = pg_connect("host=localhost port=5432 dbname=sysapp user=postgres password=postgres");

if ($conn) {
    // Verifica se usuário já existe
    $result = pg_query_params($conn, "SELECT cd_usuario FROM sysapp_config_user WHERE ds_email = $1", ['diaazze@sys.io']);
    
    if (pg_num_rows($result) > 0) {
        echo "Usuário diaazze@sys.io já existe. Atualizando senha...\n";
        $sql = "UPDATE sysapp_config_user SET ds_senha = $1 WHERE ds_email = $2";
        pg_query_params($conn, $sql, ['c6WUh^xH3H5gH64r2iOIPtHXHVAvRA', 'diaazze@sys.io']);
    } else {
        echo "Criando usuário diaazze@sys.io...\n";
        
        // Busca próximo ID
        $result = pg_query($conn, "SELECT COALESCE(MAX(cd_usuario), 0) + 1 as next_id FROM sysapp_config_user");
        $row = pg_fetch_assoc($result);
        $next_id = $row['next_id'];
        
        $sql = "INSERT INTO sysapp_config_user 
                (cd_usuario, nm_usuario, ds_login, ds_senha, ds_email, fg_ativo, dt_cadastro, dt_atualizacao) 
                VALUES ($1, $2, $3, $4, $5, 'S', NOW(), NOW())";
        
        pg_query_params($conn, $sql, [
            $next_id,
            'Diaazze',
            'diaazze',
            'c6WUh^xH3H5gH64r2iOIPtHXHVAvRA', // Senha em texto plano conforme solicitado
            'diaazze@sys.io'
        ]);
    }
    
    // Verifica criação
    $result = pg_query_params($conn, "SELECT * FROM sysapp_config_user WHERE ds_email = $1", ['diaazze@sys.io']);
    
    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        echo "\n✓ Usuário criado/atualizado com sucesso!\n";
        echo "  ID: {$user['cd_usuario']}\n";
        echo "  Nome: {$user['nm_usuario']}\n";
        echo "  Login: {$user['ds_login']}\n";
        echo "  Email: {$user['ds_email']}\n";
        echo "  Senha: c6WUh^xH3H5gH64r2iOIPtHXHVAvRA\n";
        echo "  Ativo: {$user['fg_ativo']}\n";
    }
    
    // Também atualizar admin com senha 'admin' em texto plano
    echo "\n=== Atualizando usuário admin ===\n";
    pg_query($conn, "UPDATE sysapp_config_user SET ds_senha = 'admin' WHERE ds_login = 'admin'");
    
    $result = pg_query($conn, "SELECT * FROM sysapp_config_user WHERE ds_login = 'admin'");
    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        echo "✓ Usuário admin atualizado!\n";
        echo "  Login: {$user['ds_login']}\n";
        echo "  Email: {$user['ds_email']}\n";
        echo "  Senha: admin (texto plano)\n";
    }
    
    pg_close($conn);
    
    echo "\n=== Credenciais de Acesso ===\n";
    echo "1. Login: admin | Senha: admin\n";
    echo "2. Email: diaazze@sys.io | Senha: c6WUh^xH3H5gH64r2iOIPtHXHVAvRA\n";
    
} else {
    echo "Erro ao conectar ao banco!\n";
}
