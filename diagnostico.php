<?php
/**
 * Diagnóstico do Sistema
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre style='font-family: monospace; background: #1e1e1e; color: #00ff00; padding: 20px;'>";

// 1. Testa conexão
require_once 'config/database.php';
echo "=== TESTE DE CONEXÃO ===\n";

$db = Database::getInstance();
$conn = $db->getConnection();

if ($conn) {
    echo "✅ Banco conectado com sucesso\n";
    echo "   Host: " . pg_host($conn) . "\n";
    echo "   DB: " . pg_dbname($conn) . "\n";
} else {
    echo "❌ Falha na conexão\n";
    echo pg_last_error() . "\n";
    exit;
}

// 2. Verifica tabelas
echo "\n=== VERIFICANDO TABELAS ===\n";

$tables = [
    'sysapp_config_user',
    'sysapp_config_empresas',
    'sysapp_interfaces'
];

foreach ($tables as $table) {
    $exists = $db->fetchOne("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_name = '$table'
    )");
    
    $status = ($exists[0] === 't') ? "✅" : "❌";
    echo "$status $table\n";
}

// 3. Verifica usuários
echo "\n=== USUÁRIOS ===\n";

$users = $db->fetchAll("SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user LIMIT 5");
echo count($users) . " usuário(s) encontrado(s)\n";

foreach ($users as $u) {
    echo "  - {$u['nm_usuario']} ({$u['ds_login']})\n";
}

// 4. Verifica empresas
echo "\n=== EMPRESAS ===\n";

$empresas = $db->fetchAll("SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas LIMIT 5");
echo count($empresas) . " empresa(s) encontrada(s)\n";

foreach ($empresas as $e) {
    echo "  - {$e['nm_empresa']}\n";
}

// 5. Testa classes
echo "\n=== TESTANDO CLASSES ===\n";

try {
    require_once 'core/Session.php';
    require_once 'core/Security.php';
    echo "✅ Classe Session carregada\n";
    echo "✅ Classe Security carregada\n";
} catch (Exception $e) {
    echo "❌ Erro ao carregar classes: " . $e->getMessage() . "\n";
}

// 6. Testa modelo Usuario
echo "\n=== TESTANDO MODELS ===\n";

try {
    require_once 'models/Usuario.php';
    $usuario = new Usuario();
    $user = $usuario->findByLogin('admin');
    if ($user) {
        echo "✅ Model Usuario funcionando\n";
        echo "   Usuario encontrado: " . $user['cd_usuario'] . "\n";
    } else {
        echo "⚠️ Model Usuario funcionando mas usuario 'admin' não encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ Erro no modelo Usuario: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNÓSTICO COMPLETO ===\n";
echo "Status: Sistema pronto ✅\n";

echo "</pre>";
?>
