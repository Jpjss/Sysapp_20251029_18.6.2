<?php
/**
 * Teste de Login no Backend PHP Original
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Security.php';

Session::start();

echo "=== TESTE DE LOGIN BACKEND PHP ===\n\n";

// Limpa sessão
Session::destroy();
Session::start();

// Testa login com admin/admin
$login = 'admin';
$senha = 'admin';

echo "Testando login com: $login / $senha\n\n";

$db = Database::getInstance();
$db->connect();

// Busca usuário na tabela sysapp_config_user
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_senha, ds_email, fg_ativo 
        FROM sysapp_config_user 
        WHERE (LOWER(ds_login) = LOWER($1) OR LOWER(ds_email) = LOWER($1))
        AND fg_ativo = 'S'";

$result = pg_query_params($db->getConnection(), $sql, [$login]);

if (pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);
    
    echo "✓ Usuário encontrado:\n";
    echo "  ID: {$usuario['cd_usuario']}\n";
    echo "  Nome: {$usuario['nm_usuario']}\n";
    echo "  Login: {$usuario['ds_login']}\n";
    echo "  Email: {$usuario['ds_email']}\n";
    echo "  Senha no banco: {$usuario['ds_senha']}\n\n";
    
    // Verifica senha
    if ($senha === $usuario['ds_senha']) {
        echo "✓✓✓ SENHA TEXTO PLANO CORRETA!\n\n";
        
        // Cria sessão
        Session::write('Questionarios.cd_usu', $usuario['cd_usuario']);
        Session::write('Questionarios.nm_usu', $usuario['nm_usuario']);
        Session::write('Dados.id_usuario', $usuario['cd_usuario']);
        
        echo "✓ Sessão criada com sucesso!\n";
        echo "  Dados da sessão:\n";
        echo "  - Questionarios.cd_usu: " . Session::read('Questionarios.cd_usu') . "\n";
        echo "  - Questionarios.nm_usu: " . Session::read('Questionarios.nm_usu') . "\n";
        echo "  - Dados.id_usuario: " . Session::read('Dados.id_usuario') . "\n\n";
        
        echo "✓ LOGIN BACKEND PHP FUNCIONANDO!\n";
        
    } elseif (md5($senha) === $usuario['ds_senha']) {
        echo "✓ Senha MD5 correta (mas login frontend espera texto plano)\n";
        echo "  Atualizando para texto plano...\n";
        
        $sqlUpdate = "UPDATE sysapp_config_user SET ds_senha = $1 WHERE cd_usuario = $2";
        pg_query_params($db->getConnection(), $sqlUpdate, [$senha, $usuario['cd_usuario']]);
        
        echo "✓ Senha atualizada!\n";
    } else {
        echo "✗ SENHA INCORRETA!\n";
        echo "  Senha fornecida: $senha\n";
        echo "  Senha no banco: {$usuario['ds_senha']}\n";
        echo "  MD5 da senha: " . md5($senha) . "\n";
    }
    
} else {
    echo "✗ USUÁRIO NÃO ENCONTRADO!\n";
    echo "Criando usuário admin...\n\n";
    
    // Cria usuário admin
    $sqlInsert = "INSERT INTO sysapp_config_user 
                  (nm_usuario, ds_login, ds_senha, ds_email, fg_ativo, dt_cadastro) 
                  VALUES ($1, $2, $3, $4, 'S', NOW())
                  RETURNING cd_usuario";
    
    $result = pg_query_params($db->getConnection(), $sqlInsert, [
        'Administrador',
        'admin',
        'admin',
        'admin@sysapp.com'
    ]);
    
    $novo = pg_fetch_assoc($result);
    echo "✓ Usuário admin criado com ID: {$novo['cd_usuario']}\n";
}

echo "\n\n=== TESTANDO ACESSO AO BACKEND PHP ===\n\n";
echo "URL Backend: http://localhost:8000/index.php\n";
echo "URL Login: http://localhost:8000/usuarios/login\n\n";

echo "Credenciais:\n";
echo "Login: admin\n";
echo "Senha: admin\n";
