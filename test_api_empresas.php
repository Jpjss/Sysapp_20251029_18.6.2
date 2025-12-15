<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TESTE API EMPRESAS ===\n\n";

// Define constantes
define('BASE_PATH', __DIR__);

// Testa conexão
require_once __DIR__ . '/config/database.php';

try {
    $dbInstance = Database::getInstance();
    $db = $dbInstance->getConnection();
    echo "✓ Conexão com banco: OK\n";
    
    // Testa query
    $sql = "SELECT cd_empresa, nm_empresa, nm_servidor, nm_banco, nm_porta, fg_ativa 
            FROM sysapp_config_empresas 
            WHERE fg_ativa = 'S' 
            ORDER BY nm_empresa";
    
    $result = pg_query($db, $sql);
    
    if (!$result) {
        echo "✗ Erro na query: " . pg_last_error($db) . "\n";
        exit;
    }
    
    echo "✓ Query executada: OK\n\n";
    
    $empresas = [];
    while ($row = pg_fetch_assoc($result)) {
        $empresas[] = [
            'id' => (int)$row['cd_empresa'],
            'nome' => $row['nm_empresa'],
            'host' => $row['nm_servidor'],
            'banco' => $row['nm_banco'],
            'porta' => $row['nm_porta']
        ];
    }
    
    echo "Total de empresas: " . count($empresas) . "\n\n";
    
    foreach ($empresas as $emp) {
        echo "ID: {$emp['id']}\n";
        echo "Nome: {$emp['nome']}\n";
        echo "Servidor: {$emp['host']}:{$emp['porta']}\n";
        echo "Banco: {$emp['banco']}\n";
        echo "---\n";
    }
    
    echo "\nJSON:\n";
    echo json_encode([
        'success' => true,
        'empresas' => $empresas
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
