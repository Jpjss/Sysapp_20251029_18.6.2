<?php
/**
 * CORREÇÃO DEFINITIVA DO SISTEMA DE LOGIN
 * Corrige a view e recria estruturas necessárias
 */

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=" . str_repeat("=", 60) . "\n";
echo "   CORREÇÃO DEFINITIVA DO SISTEMA DE LOGIN\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// 1. Recriar a view vw_login_empresa_interface
echo "1. Recriando view vw_login_empresa_interface...\n";

$sql = "DROP VIEW IF EXISTS vw_login_empresa_interface CASCADE";
pg_query($conn, $sql);

$sql = "CREATE OR REPLACE VIEW vw_login_empresa_interface AS
        SELECT DISTINCT cd_usuario, nm_interface
        FROM sysapp_config_user_interfaces";
pg_query($conn, $sql);

echo "   ✅ View recriada!\n";

// 2. Verificar registros na view
$sql = "SELECT COUNT(*) as total FROM vw_login_empresa_interface";
$result = pg_query($conn, $sql);
$count = pg_fetch_row($result)[0];
echo "   📊 Registros na view agora: $count\n\n";

// 3. Verificar se o problema é na função getEmpresasInfo
echo "2. Verificando função getEmpresasInfo...\n";

// Testar a query que é usada no login
$cd_usuario = 2; // diaazze
$sql = "SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario";
$result = pg_query($conn, $sql);
$empresas = pg_fetch_all($result);

if ($empresas) {
    echo "   ✅ Empresas do usuário diaazze: ";
    $ids = array_column($empresas, 'cd_empresa');
    echo implode(', ', $ids) . "\n";
    
    // Testar a query de info das empresas
    $cd_empresas = implode(',', $ids);
    $sql = "SELECT ce.cd_empresa, ce.nm_empresa as nome_empresa, ce.ds_host as hostname_banco, 
                   ce.ds_banco as nome_banco, ce.ds_usuario as usuario_banco, 
                   ce.ds_senha as senha_banco, ce.ds_porta as porta_banco
            FROM sysapp_config_empresas ce
            INNER JOIN sysapp_config_user_empresas cue 
                ON ce.cd_empresa = cue.cd_empresa
            WHERE cue.cd_usuario = $cd_usuario 
            AND ce.cd_empresa IN ($cd_empresas)
            ORDER BY ce.nm_empresa";
    
    $result = pg_query($conn, $sql);
    $info = pg_fetch_all($result);
    
    if ($info) {
        echo "   ✅ Informações das empresas recuperadas: " . count($info) . "\n";
        foreach ($info as $emp) {
            echo "      - {$emp['nome_empresa']} ({$emp['hostname_banco']})\n";
        }
    } else {
        echo "   ❌ ERRO: Não conseguiu recuperar informações das empresas!\n";
    }
} else {
    echo "   ❌ Usuário diaazze não tem empresas vinculadas!\n";
}

// 4. Testar permissões
echo "\n3. Testando permissões do usuário diaazze...\n";

$sql = "SELECT nm_interface as nome_interface 
        FROM vw_login_empresa_interface 
        WHERE cd_usuario = $cd_usuario";
$result = pg_query($conn, $sql);
$perms = pg_fetch_all($result);

if ($perms) {
    echo "   ✅ Permissões encontradas: " . count($perms) . "\n";
    foreach ($perms as $p) {
        echo "      - {$p['nome_interface']}\n";
    }
} else {
    echo "   ❌ NENHUMA permissão encontrada via view!\n";
    
    // Verificar direto na tabela
    echo "\n   Verificando direto na tabela sysapp_config_user_interfaces...\n";
    $sql = "SELECT nm_interface FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario";
    $result = pg_query($conn, $sql);
    $perms_direto = pg_fetch_all($result);
    
    if ($perms_direto) {
        echo "   ✅ Permissões na tabela: " . count($perms_direto) . "\n";
        foreach ($perms_direto as $p) {
            echo "      - {$p['nm_interface']}\n";
        }
    } else {
        echo "   ❌ Tabela também está vazia para este usuário!\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "TESTE DE LOGIN COMPLETO\n";
echo str_repeat("=", 60) . "\n\n";

// Simular o fluxo de login
$login = 'diaazze';
$senha = 'diaazze123';

echo "Tentando login com: $login / $senha\n\n";

// 1. Buscar usuário
$sql = "SELECT cd_usuario FROM sysapp_config_user 
        WHERE LOWER(ds_email) = LOWER('$login') 
           OR LOWER(ds_login) = LOWER('$login') 
           OR LOWER(nm_usuario) = LOWER('$login')";
$result = pg_query($conn, $sql);
$user = pg_fetch_assoc($result);

if (!$user) {
    echo "❌ ERRO: Usuário não encontrado!\n";
    exit;
}
echo "✅ Usuário encontrado: ID = {$user['cd_usuario']}\n";

// 2. Buscar dados completos
$cd_usuario = $user['cd_usuario'];
$sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_senha as senha_usuario 
        FROM sysapp_config_user 
        WHERE cd_usuario = $cd_usuario AND fg_ativo = 'S'";
$result = pg_query($conn, $sql);
$usuario = pg_fetch_assoc($result);

if (!$usuario) {
    echo "❌ ERRO: Usuário inativo ou não encontrado!\n";
    exit;
}
echo "✅ Usuário ativo: {$usuario['nome_usuario']}\n";

// 3. Verificar senha
if ($senha === $usuario['senha_usuario']) {
    echo "✅ Senha correta!\n";
} else {
    echo "❌ Senha incorreta! (esperada: {$usuario['senha_usuario']})\n";
    exit;
}

// 4. Buscar empresas
$sql = "SELECT DISTINCT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario";
$result = pg_query($conn, $sql);
$empresas = pg_fetch_all($result);

if (empty($empresas)) {
    echo "❌ ERRO: Usuário sem empresas!\n";
    exit;
}
echo "✅ Empresas encontradas: " . count($empresas) . "\n";

// 5. Buscar info das empresas
$cd_empresas = implode(',', array_column($empresas, 'cd_empresa'));
$sql = "SELECT ce.cd_empresa, ce.nm_empresa as nome_empresa, ce.ds_host, 
               ce.ds_banco as nome_banco, ce.ds_usuario, ce.ds_senha, ce.ds_porta
        FROM sysapp_config_empresas ce
        INNER JOIN sysapp_config_user_empresas cue 
            ON ce.cd_empresa = cue.cd_empresa
        WHERE cue.cd_usuario = $cd_usuario 
        AND ce.cd_empresa IN ($cd_empresas)
        ORDER BY ce.nm_empresa";
$result = pg_query($conn, $sql);
$infoDb = pg_fetch_all($result);

if (empty($infoDb)) {
    echo "❌ ERRO: Informações das empresas não encontradas!\n";
    exit;
}
echo "✅ Info das empresas: " . count($infoDb) . "\n";

// 6. Buscar permissões
$sql = "SELECT nm_interface as nome_interface FROM vw_login_empresa_interface WHERE cd_usuario = $cd_usuario";
$result = pg_query($conn, $sql);
$permissoes = pg_fetch_all($result);

if (empty($permissoes)) {
    echo "❌ ERRO: Usuário sem permissões!\n";
    
    // Se a view está vazia, pegar direto da tabela
    $sql = "SELECT nm_interface as nome_interface FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario";
    $result = pg_query($conn, $sql);
    $permissoes = pg_fetch_all($result);
    
    if (empty($permissoes)) {
        echo "❌ ERRO CRÍTICO: Tabela de permissões também vazia!\n";
        exit;
    }
    
    echo "✅ Permissões da tabela: " . count($permissoes) . "\n";
}

$perms = [];
foreach ($permissoes as $row) {
    $perms[] = $row['nome_interface'];
}
echo "✅ Permissões: " . implode(', ', $perms) . "\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ LOGIN SIMULADO COM SUCESSO!\n";
echo str_repeat("=", 60) . "\n";
echo "\nTeste real: http://localhost:8000/usuarios/login\n";
echo "   Login: diaazze\n";
echo "   Senha: diaazze123\n";
