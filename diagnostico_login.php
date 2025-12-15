<?php
/**
 * DIAGNÓSTICO COMPLETO DO SISTEMA DE LOGIN
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== DIAGNÓSTICO COMPLETO DO LOGIN ===\n\n";

// 1. Lista todos os usuários
echo "1. USUÁRIOS NO BANCO:\n";
echo str_repeat("-", 80) . "\n";

$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha, fg_ativo 
        FROM sysapp_config_user ORDER BY cd_usuario";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "ID: {$row['cd_usuario']}\n";
    echo "  Nome: {$row['nm_usuario']}\n";
    echo "  Login: {$row['ds_login']}\n";
    echo "  Email: {$row['ds_email']}\n";
    echo "  Senha: {$row['ds_senha']}\n";
    echo "  Ativo: {$row['fg_ativo']}\n";
    
    // Verifica empresas
    $sql2 = "SELECT e.nm_empresa 
             FROM sysapp_config_user_empresas ue
             JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
             WHERE ue.cd_usuario = {$row['cd_usuario']}";
    $result2 = pg_query($conn, $sql2);
    $empresas = [];
    while ($emp = pg_fetch_assoc($result2)) {
        $empresas[] = $emp['nm_empresa'];
    }
    echo "  Empresas: " . (empty($empresas) ? "NENHUMA!" : implode(", ", $empresas)) . "\n";
    
    // Verifica permissões
    $sql3 = "SELECT nm_interface FROM sysapp_config_user_interfaces WHERE cd_usuario = {$row['cd_usuario']}";
    $result3 = pg_query($conn, $sql3);
    $perms = [];
    while ($perm = pg_fetch_assoc($result3)) {
        $perms[] = $perm['nm_interface'];
    }
    echo "  Permissões: " . (empty($perms) ? "NENHUMA!" : implode(", ", $perms)) . "\n";
    
    echo "\n";
}

echo str_repeat("=", 80) . "\n";
echo "\n2. PROBLEMAS ENCONTRADOS:\n";
echo str_repeat("-", 80) . "\n";

// Verifica problemas
$problemas = [];

// Usuários sem empresa
$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        WHERE ue.cd_empresa IS NULL AND u.fg_ativo = 'S'";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    $problemas[] = "❌ Usuário '{$row['ds_login']}' (ID:{$row['cd_usuario']}) NÃO TEM EMPRESA vinculada!";
}

// Usuários sem permissão
$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_interfaces ui ON u.cd_usuario = ui.cd_usuario
        WHERE ui.nm_interface IS NULL AND u.fg_ativo = 'S'";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    $problemas[] = "❌ Usuário '{$row['ds_login']}' (ID:{$row['cd_usuario']}) NÃO TEM PERMISSÕES!";
}

// Usuários inativos
$sql = "SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user WHERE fg_ativo = 'N'";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    $problemas[] = "⚠️ Usuário '{$row['ds_login']}' (ID:{$row['cd_usuario']}) está INATIVO";
}

if (empty($problemas)) {
    echo "✅ Nenhum problema encontrado!\n";
} else {
    foreach ($problemas as $p) {
        echo "$p\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "\n3. CORRIGINDO AUTOMATICAMENTE...\n";
echo str_repeat("-", 80) . "\n";

// Pega a primeira empresa ativa
$sql = "SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas WHERE fg_ativo = 'S' ORDER BY cd_empresa LIMIT 1";
$result = pg_query($conn, $sql);
$empresaPadrao = pg_fetch_assoc($result);

if (!$empresaPadrao) {
    echo "❌ ERRO CRÍTICO: Não há empresas ativas no sistema!\n";
    exit;
}

echo "Empresa padrão: {$empresaPadrao['nm_empresa']} (ID: {$empresaPadrao['cd_empresa']})\n\n";

// Corrige usuários sem empresa
$sql = "SELECT u.cd_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        WHERE ue.cd_empresa IS NULL AND u.fg_ativo = 'S'";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    $sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
            VALUES ({$row['cd_usuario']}, {$empresaPadrao['cd_empresa']})
            ON CONFLICT (cd_usuario, cd_empresa) DO NOTHING";
    pg_query($conn, $sql);
    echo "✅ Vinculou '{$row['ds_login']}' à empresa '{$empresaPadrao['nm_empresa']}'\n";
}

// Corrige usuários sem permissão
$permissoesPadrao = ['relatorios', 'clientes', 'questionarios'];

$sql = "SELECT u.cd_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_interfaces ui ON u.cd_usuario = ui.cd_usuario
        WHERE ui.nm_interface IS NULL AND u.fg_ativo = 'S'";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    foreach ($permissoesPadrao as $perm) {
        $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
                VALUES ({$row['cd_usuario']}, '$perm')
                ON CONFLICT (cd_usuario, nm_interface) DO NOTHING";
        pg_query($conn, $sql);
    }
    echo "✅ Adicionou permissões básicas para '{$row['ds_login']}'\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "\n4. CREDENCIAIS DE ACESSO:\n";
echo str_repeat("-", 80) . "\n";

$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_senha
        FROM sysapp_config_user u
        WHERE u.fg_ativo = 'S'
        ORDER BY u.cd_usuario";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "👤 {$row['nm_usuario']}\n";
    echo "   Login: {$row['ds_login']}\n";
    echo "   Senha: {$row['ds_senha']}\n\n";
}

echo str_repeat("=", 80) . "\n";
echo "DIAGNÓSTICO COMPLETO!\n";
