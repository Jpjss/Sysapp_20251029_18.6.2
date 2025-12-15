<?php
/**
 * RESET COMPLETO DO SISTEMA DE LOGIN
 * Cria usuário admin funcional e testa tudo
 */

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== RESET COMPLETO DO SISTEMA ===\n\n";

// 1. LIMPA TUDO
echo "1. Limpando dados antigos...\n";
pg_query($conn, "DELETE FROM sysapp_config_user_empresas_interfaces");
pg_query($conn, "DELETE FROM sysapp_config_user_interfaces");
pg_query($conn, "DELETE FROM sysapp_config_user_empresas");
echo "   ✓ Limpeza concluída\n\n";

// 2. ATUALIZA USUÁRIO ADMIN (não deleta, apenas atualiza)
echo "2. Configurando usuário ADMIN...\n";

// Atualiza admin existente ou cria novo
$sql = "INSERT INTO sysapp_config_user 
        (nm_usuario, ds_login, ds_email, ds_senha, fg_ativo) 
        VALUES ('Administrador', 'admin', 'admin@sysapp.com', 'admin', 'S')
        ON CONFLICT (ds_login) 
        DO UPDATE SET ds_senha = 'admin', fg_ativo = 'S', nm_usuario = 'Administrador'
        RETURNING cd_usuario";

$result = pg_query($conn, $sql);
$admin = pg_fetch_assoc($result);
$cd_admin = $admin['cd_usuario'];

echo "   ✓ Admin criado - ID: $cd_admin\n";
echo "   Login: admin\n";
echo "   Senha: admin\n\n";

// 3. VINCULA TODAS AS EMPRESAS AO ADMIN
echo "3. Vinculando empresas ao admin...\n";

$sql = "SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas WHERE fg_ativo = 'S'";
$result = pg_query($conn, $sql);

$empresas_vinculadas = 0;
while ($empresa = pg_fetch_assoc($result)) {
    $sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
            VALUES ($cd_admin, {$empresa['cd_empresa']})";
    pg_query($conn, $sql);
    echo "   ✓ {$empresa['nm_empresa']}\n";
    $empresas_vinculadas++;
}

if ($empresas_vinculadas == 0) {
    echo "   ⚠️ NENHUMA empresa ativa encontrada!\n";
    echo "   Criando empresa padrão...\n";
    
    $sql = "INSERT INTO sysapp_config_empresas 
            (nm_empresa, ds_host, ds_banco, ds_usuario, ds_senha, ds_porta, fg_ativo)
            VALUES ('Sistema Local', 'localhost', 'sysapp', 'admin', 'systec2011.', '5432', 'S')
            RETURNING cd_empresa";
    
    $result = pg_query($conn, $sql);
    $emp = pg_fetch_assoc($result);
    
    // Vincula ao admin
    pg_query($conn, "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
                     VALUES ($cd_admin, {$emp['cd_empresa']})");
    
    echo "   ✓ Sistema Local criada e vinculada\n";
    $empresas_vinculadas = 1;
}

echo "\n   Total: $empresas_vinculadas empresas\n\n";

// 4. DÁ TODAS AS PERMISSÕES AO ADMIN
echo "4. Configurando permissões...\n";

$permissoes = ['admin', 'usuarios', 'empresas', 'relatorios', 'clientes', 'questionarios'];

foreach ($permissoes as $perm) {
    $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
            VALUES ($cd_admin, '$perm')";
    pg_query($conn, $sql);
    echo "   ✓ $perm\n";
}

echo "\n=== CONFIGURAÇÃO COMPLETA! ===\n\n";

// 5. TESTE DE LOGIN
echo "=== TESTANDO LOGIN ===\n\n";

$login = 'admin';
$senha = 'admin';

echo "Tentando: login='$login' senha='$senha'\n\n";

// Busca usuário
$sql = "SELECT * FROM sysapp_config_user WHERE ds_login = '$login' AND fg_ativo = 'S'";
$result = pg_query($conn, $sql);
$user = pg_fetch_assoc($result);

if (!$user) {
    echo "✗ ERRO: Usuário não encontrado!\n";
    exit(1);
}

echo "✓ Usuário encontrado: {$user['nm_usuario']}\n";

// Testa senha
if ($user['ds_senha'] === $senha) {
    echo "✓ Senha correta!\n\n";
} else {
    echo "✗ Senha incorreta!\n";
    echo "  Esperado: $senha\n";
    echo "  Banco: {$user['ds_senha']}\n";
    exit(1);
}

// Verifica empresas
$sql = "SELECT COUNT(*) as total FROM sysapp_config_user_empresas WHERE cd_usuario = {$user['cd_usuario']}";
$result = pg_query($conn, $sql);
$count = pg_fetch_assoc($result);

if ($count['total'] > 0) {
    echo "✓ Empresas vinculadas: {$count['total']}\n\n";
} else {
    echo "✗ ERRO: Sem empresas vinculadas!\n";
    exit(1);
}

// Verifica permissões
$sql = "SELECT COUNT(*) as total FROM sysapp_config_user_interfaces WHERE cd_usuario = {$user['cd_usuario']}";
$result = pg_query($conn, $sql);
$count = pg_fetch_assoc($result);

if ($count['total'] > 0) {
    echo "✓ Permissões configuradas: {$count['total']}\n\n";
} else {
    echo "✗ ERRO: Sem permissões!\n";
    exit(1);
}

echo "=== ✅ TUDO PRONTO! ===\n\n";
echo "CREDENCIAIS DEFINITIVAS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Login: admin\n";
echo "Senha: admin\n";
echo "URL: http://localhost:8000\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "PRÓXIMOS PASSOS:\n";
echo "1. Feche TODAS as abas do navegador\n";
echo "2. Abra: http://localhost:8000\n";
echo "3. Digite: admin / admin\n";
echo "4. Deve funcionar perfeitamente!\n";
