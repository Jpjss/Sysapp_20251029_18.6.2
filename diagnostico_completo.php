<?php
/**
 * DIAGNÓSTICO COMPLETO DO SISTEMA DE LOGIN
 * Este script identifica TODOS os problemas e os corrige
 */

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=" . str_repeat("=", 70) . "\n";
echo "    DIAGNÓSTICO COMPLETO DO SISTEMA - " . date('d/m/Y H:i:s') . "\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$problemas = [];
$correcoes = [];

// ============================================================================
// 1. VERIFICAR TABELAS NECESSÁRIAS
// ============================================================================
echo "1. VERIFICANDO TABELAS...\n";

$tabelas_necessarias = [
    'sysapp_config_user' => 'Tabela de usuários',
    'sysapp_config_empresas' => 'Tabela de empresas',
    'sysapp_config_user_empresas' => 'Vinculação usuário-empresa',
    'sysapp_config_user_interfaces' => 'Permissões dos usuários'
];

foreach ($tabelas_necessarias as $tabela => $desc) {
    $sql = "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_name = '$tabela'
    )";
    $result = pg_query($conn, $sql);
    $existe = pg_fetch_row($result)[0] === 't';
    
    if ($existe) {
        echo "   ✅ $tabela\n";
    } else {
        echo "   ❌ $tabela - NÃO EXISTE!\n";
        $problemas[] = "Tabela $tabela não existe";
        
        // Criar a tabela se não existir
        if ($tabela === 'sysapp_config_user_interfaces') {
            $correcoes[] = "Criando tabela sysapp_config_user_interfaces...";
            $sql = "CREATE TABLE IF NOT EXISTS sysapp_config_user_interfaces (
                cd_usuario INTEGER NOT NULL,
                nm_interface VARCHAR(50) NOT NULL,
                PRIMARY KEY (cd_usuario, nm_interface)
            )";
            pg_query($conn, $sql);
        }
    }
}

// ============================================================================
// 2. VERIFICAR VIEW vw_login_empresa_interface
// ============================================================================
echo "\n2. VERIFICANDO VIEW DE PERMISSÕES...\n";

$sql = "SELECT EXISTS (
    SELECT FROM information_schema.views 
    WHERE table_schema = 'public' AND table_name = 'vw_login_empresa_interface'
)";
$result = pg_query($conn, $sql);
$existe = pg_fetch_row($result)[0] === 't';

if ($existe) {
    echo "   ✅ vw_login_empresa_interface existe\n";
    
    // Verifica se retorna dados
    $sql = "SELECT COUNT(*) FROM vw_login_empresa_interface";
    $result = pg_query($conn, $sql);
    $count = (int)pg_fetch_row($result)[0];
    echo "   📊 Registros na view: $count\n";
    
    if ($count === 0) {
        $problemas[] = "View vw_login_empresa_interface está vazia";
    }
} else {
    echo "   ❌ vw_login_empresa_interface NÃO EXISTE!\n";
    $problemas[] = "View vw_login_empresa_interface não existe";
    
    // Criar a view
    $correcoes[] = "Criando view vw_login_empresa_interface...";
    $sql = "CREATE OR REPLACE VIEW vw_login_empresa_interface AS
            SELECT DISTINCT cd_usuario, nm_interface
            FROM sysapp_config_user_interfaces";
    pg_query($conn, $sql);
    echo "   🔧 View criada!\n";
}

// ============================================================================
// 3. VERIFICAR USUÁRIOS E SEUS DADOS
// ============================================================================
echo "\n3. VERIFICANDO USUÁRIOS...\n";

$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha, fg_ativo 
        FROM sysapp_config_user ORDER BY cd_usuario";
$result = pg_query($conn, $sql);

while ($user = pg_fetch_assoc($result)) {
    $id = $user['cd_usuario'];
    $nome = $user['nm_usuario'];
    $login = $user['ds_login'];
    $ativo = $user['fg_ativo'];
    $senha = $user['ds_senha'];
    
    echo "\n   ┌─────────────────────────────────────────────────────\n";
    echo "   │ Usuário #$id: $nome\n";
    echo "   ├─────────────────────────────────────────────────────\n";
    echo "   │ Login: " . ($login ?: 'NÃO DEFINIDO') . "\n";
    echo "   │ Senha: " . ($senha ?: 'NÃO DEFINIDA') . "\n";
    echo "   │ Ativo: " . ($ativo === 'S' ? '✅ Sim' : '❌ Não') . "\n";
    
    // Verificar empresas
    $sql2 = "SELECT e.cd_empresa, e.nm_empresa 
             FROM sysapp_config_user_empresas ue
             JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
             WHERE ue.cd_usuario = $id";
    $result2 = pg_query($conn, $sql2);
    $empresas = pg_fetch_all($result2);
    
    if (empty($empresas)) {
        echo "   │ Empresas: ❌ NENHUMA\n";
        $problemas[] = "Usuário '$login' (ID:$id) não tem empresas";
    } else {
        echo "   │ Empresas: ✅ " . count($empresas) . " - ";
        echo implode(', ', array_column($empresas, 'nm_empresa')) . "\n";
    }
    
    // Verificar permissões
    $sql2 = "SELECT nm_interface FROM sysapp_config_user_interfaces WHERE cd_usuario = $id";
    $result2 = pg_query($conn, $sql2);
    $perms = pg_fetch_all($result2);
    
    if (empty($perms)) {
        echo "   │ Permissões: ❌ NENHUMA\n";
        $problemas[] = "Usuário '$login' (ID:$id) não tem permissões";
    } else {
        echo "   │ Permissões: ✅ " . count($perms) . " - ";
        echo implode(', ', array_column($perms, 'nm_interface')) . "\n";
    }
    
    echo "   └─────────────────────────────────────────────────────\n";
    
    // Corrigir problemas automaticamente
    if ($ativo !== 'S') {
        $correcoes[] = "Ativando usuário '$login'...";
        pg_query($conn, "UPDATE sysapp_config_user SET fg_ativo = 'S' WHERE cd_usuario = $id");
    }
    
    if (empty($empresas)) {
        // Vincular à primeira empresa disponível
        $sql3 = "SELECT cd_empresa FROM sysapp_config_empresas WHERE fg_ativo = 'S' ORDER BY cd_empresa LIMIT 1";
        $result3 = pg_query($conn, $sql3);
        $empresa = pg_fetch_assoc($result3);
        
        if ($empresa) {
            $correcoes[] = "Vinculando usuário '$login' à empresa padrão...";
            $sql3 = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
                     VALUES ($id, {$empresa['cd_empresa']})
                     ON CONFLICT DO NOTHING";
            pg_query($conn, $sql3);
        }
    }
    
    if (empty($perms)) {
        $correcoes[] = "Adicionando permissões básicas para '$login'...";
        $perms_basicas = ['relatorios', 'clientes', 'questionarios'];
        foreach ($perms_basicas as $perm) {
            $sql3 = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
                     VALUES ($id, '$perm')
                     ON CONFLICT DO NOTHING";
            pg_query($conn, $sql3);
        }
    }
}

// ============================================================================
// 4. VERIFICAR EMPRESAS
// ============================================================================
echo "\n4. VERIFICANDO EMPRESAS...\n";

$sql = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, fg_ativo 
        FROM sysapp_config_empresas ORDER BY cd_empresa";
$result = pg_query($conn, $sql);
$empresas = pg_fetch_all($result);

if (empty($empresas)) {
    echo "   ❌ NENHUMA EMPRESA CADASTRADA!\n";
    $problemas[] = "Nenhuma empresa cadastrada no sistema";
} else {
    foreach ($empresas as $emp) {
        $status = $emp['fg_ativo'] === 'S' ? '✅' : '❌';
        echo "   $status {$emp['nm_empresa']} (ID: {$emp['cd_empresa']})\n";
        echo "      Host: {$emp['ds_host']} | DB: {$emp['ds_banco']} | User: {$emp['ds_usuario']}\n";
    }
}

// ============================================================================
// 5. RESUMO
// ============================================================================
echo "\n" . str_repeat("=", 72) . "\n";
echo "RESUMO\n";
echo str_repeat("=", 72) . "\n";

if (count($problemas) > 0) {
    echo "\n⚠️  PROBLEMAS ENCONTRADOS:\n";
    foreach ($problemas as $i => $prob) {
        echo "   " . ($i + 1) . ". $prob\n";
    }
}

if (count($correcoes) > 0) {
    echo "\n🔧 CORREÇÕES APLICADAS:\n";
    foreach ($correcoes as $i => $cor) {
        echo "   " . ($i + 1) . ". $cor\n";
    }
}

// ============================================================================
// 6. CREDENCIAIS ATUALIZADAS
// ============================================================================
echo "\n" . str_repeat("=", 72) . "\n";
echo "CREDENCIAIS PARA LOGIN (APÓS CORREÇÕES)\n";
echo str_repeat("=", 72) . "\n\n";

$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_senha, u.fg_ativo,
               (SELECT COUNT(*) FROM sysapp_config_user_empresas WHERE cd_usuario = u.cd_usuario) as qt_empresas,
               (SELECT COUNT(*) FROM sysapp_config_user_interfaces WHERE cd_usuario = u.cd_usuario) as qt_perms
        FROM sysapp_config_user u
        ORDER BY u.cd_usuario";
$result = pg_query($conn, $sql);

echo "┌─────────────────────────────────────────────────────────────────────┐\n";
echo "│ LOGIN         │ SENHA         │ ATIVO │ EMPRESAS │ PERMISSÕES      │\n";
echo "├─────────────────────────────────────────────────────────────────────┤\n";

while ($row = pg_fetch_assoc($result)) {
    $login = str_pad($row['ds_login'] ?? 'N/A', 13);
    $senha = str_pad($row['ds_senha'] ?? 'N/A', 13);
    $ativo = $row['fg_ativo'] === 'S' ? ' ✅  ' : ' ❌  ';
    $emps = str_pad($row['qt_empresas'], 8);
    $perms = str_pad($row['qt_perms'], 15);
    
    echo "│ $login │ $senha │$ativo │ $emps │ $perms │\n";
}

echo "└─────────────────────────────────────────────────────────────────────┘\n";

echo "\n" . str_repeat("=", 72) . "\n";
echo "Acesse: http://localhost:8000/usuarios/login\n";
echo str_repeat("=", 72) . "\n";
