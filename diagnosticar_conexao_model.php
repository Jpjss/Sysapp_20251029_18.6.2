<?php
/**
 * DIAGNÓSTICO: Por que o Model conecta no banco errado?
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

echo "🔍 DIAGNÓSTICO DE CONEXÃO DO MODEL\n";
echo str_repeat("=", 70) . "\n\n";

// 1. Verificar sessão
echo "1️⃣  Estado da Sessão:\n";
if (Session::check('Config.database')) {
    echo "   ✅ Sessão tem config\n";
    echo "   Banco: " . Session::read('Config.database') . "\n";
} else {
    echo "   ❌ Sessão NÃO tem config (vai usar padrão)\n";
}

// 2. Verificar Database::getInstance()
echo "\n2️⃣  Testando Database::getInstance():\n";
$db = Database::getInstance();
echo "   Instância criada: " . get_class($db) . "\n";

// 3. Conectar usando método padrão
echo "\n3️⃣  Conectando com método padrão:\n";
$conn = $db->connect();

if ($conn) {
    echo "   ✅ Conexão estabelecida\n";
    
    // Verificar qual banco está conectado
    $result = pg_query($conn, "SELECT current_database()");
    $row = pg_fetch_assoc($result);
    echo "   Banco atual: " . $row['current_database'] . "\n";
    
    // Verificar host
    $result = pg_query($conn, "SELECT inet_server_addr()");
    $row = pg_fetch_assoc($result);
    echo "   Host: " . ($row['inet_server_addr'] ?? 'localhost') . "\n";
    
    // Testar query na dm_orcamento_vendas_consolidadas
    echo "\n4️⃣  Testando query na VIEW:\n";
    $result = pg_query($conn, "SELECT COUNT(*) as total FROM dm_orcamento_vendas_consolidadas");
    
    if ($result) {
        $row = pg_fetch_assoc($result);
        echo "   ✅ Query executada - Total: " . $row['total'] . " registros\n";
    } else {
        echo "   ❌ Query falhou: " . pg_last_error($conn) . "\n";
    }
    
    // Testar com fetchOne do Database
    echo "\n5️⃣  Testando Database->fetchOne():\n";
    $sql = "SELECT COUNT(*) as total FROM dm_orcamento_vendas_consolidadas";
    $result = $db->fetchOne($sql);
    
    if ($result) {
        echo "   ✅ fetchOne() OK - Total: " . $result['total'] . "\n";
    } else {
        echo "   ❌ fetchOne() retornou NULL\n";
    }
    
} else {
    echo "   ❌ Falha na conexão\n";
}

echo "\n" . str_repeat("=", 70) . "\n";

// 6. Verificar propriedades privadas da classe Database via reflection
echo "6️⃣  Propriedades internas do Database:\n";
$reflection = new ReflectionClass($db);

$properties = [
    'host' => null,
    'database' => null,
    'username' => null,
    'port' => null
];

foreach ($properties as $propName => $value) {
    try {
        $prop = $reflection->getProperty($propName);
        $prop->setAccessible(true);
        $value = $prop->getValue($db);
        echo "   {$propName}: {$value}\n";
    } catch (Exception $e) {
        echo "   {$propName}: (erro ao ler)\n";
    }
}

echo "\n✅ DIAGNÓSTICO COMPLETO\n";
