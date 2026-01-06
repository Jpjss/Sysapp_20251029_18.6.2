<?php
/**
 * TESTE DIRETO DO MODEL RELATORIO
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/models/Relatorio.php';

Session::start();

echo "🧪 TESTE DIRETO DO MODEL RELATORIO\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // Criar instância do Model
    echo "1️⃣  Criando instância de Relatorio...\n";
    $relatorio = new Relatorio();
    echo "   ✅ Instância criada\n\n";
    
    // Acessar propriedade privada $db via reflection
    $reflection = new ReflectionClass($relatorio);
    $dbProperty = $reflection->getProperty('db');
    $dbProperty->setAccessible(true);
    $db = $dbProperty->getValue($relatorio);
    
    echo "2️⃣  Verificando Database interno:\n";
    echo "   Classe: " . get_class($db) . "\n";
    
    $conn = $db->getConnection();
    if ($conn) {
        $result = pg_query($conn, "SELECT current_database()");
        $row = pg_fetch_assoc($result);
        echo "   Banco conectado: " . $row['current_database'] . "\n\n";
    } else {
        echo "   ❌ Sem conexão!\n\n";
    }
    
    // Testar cada método
    echo "3️⃣  Testando getEstatisticas()...\n";
    $stats = $relatorio->getEstatisticas();
    
    echo "   Resultado:\n";
    print_r($stats);
    echo "\n";
    
    if (isset($stats['total_clientes']) && $stats['total_clientes'] > 0) {
        echo "   ✅ SUCESSO - Retornou {$stats['total_clientes']} clientes\n";
    } else {
        echo "   ❌ PROBLEMA - Retornou 0 ou NULL\n";
        
        // Testar query diretamente usando o $db do model
        echo "\n   Testando query direta com o mesmo $db:\n";
        $sql = "SELECT COUNT(DISTINCT cd_pessoa) as total FROM dm_orcamento_vendas_consolidadas";
        $result = $db->fetchOne($sql);
        echo "   Result da query direta: ";
        print_r($result);
        echo "\n";
    }
    
    echo "\n4️⃣  Testando getAtendimentosPorPeriodo()...\n";
    $dt_inicio = '2026-01-01';
    $dt_fim = '2026-01-06';
    
    $atendimentos = $relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
    
    echo "   Período: {$dt_inicio} até {$dt_fim}\n";
    echo "   Tipo retornado: " . gettype($atendimentos) . "\n";
    echo "   Count: " . (is_array($atendimentos) ? count($atendimentos) : 'N/A') . "\n";
    
    if (is_array($atendimentos) && count($atendimentos) > 0) {
        echo "   ✅ SUCESSO - Retornou " . count($atendimentos) . " registros\n";
        echo "   Primeiro registro:\n";
        print_r($atendimentos[0]);
    } else {
        echo "   ❌ PROBLEMA - Array vazio\n";
    }
    
    echo "\n5️⃣  Testando getTopClientes(5)...\n";
    $clientes = $relatorio->getTopClientes(5);
    
    echo "   Tipo retornado: " . gettype($clientes) . "\n";
    echo "   Count: " . (is_array($clientes) ? count($clientes) : 'N/A') . "\n";
    
    if (is_array($clientes) && count($clientes) > 0) {
        echo "   ✅ SUCESSO\n";
    } else {
        echo "   ❌ PROBLEMA\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ TESTE COMPLETO\n";
