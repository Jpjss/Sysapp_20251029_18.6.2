<?php
/**
 * TESTE DE RELATÓRIOS COM FILTROS
 * Simula o que acontece quando o usuário aplica filtros
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/models/Relatorio.php';

Session::start();

echo "🧪 TESTE DE RELATÓRIOS COM FILTROS\n";
echo str_repeat("=", 70) . "\n\n";

// Verificar estado da sessão
echo "📋 ESTADO DA SESSÃO:\n";
echo str_repeat("-", 70) . "\n";

if (Session::check('Config.database')) {
    echo "✅ Sessão tem configuração de banco\n";
    echo "   Host: " . Session::read('Config.host') . "\n";
    echo "   Banco: " . Session::read('Config.database') . "\n";
    echo "   Usuário: " . Session::read('Config.user') . "\n";
    echo "   Porta: " . Session::read('Config.porta') . "\n";
} else {
    echo "⚠️  Sessão NÃO tem configuração de banco\n";
    echo "   Sistema vai usar config padrão\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "🔧 TESTANDO CONEXÃO DO MODEL:\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $relatorio = new Relatorio();
    
    // Teste 1: Estatísticas gerais
    echo "1️⃣  Testando getEstatisticas()...\n";
    $stats = $relatorio->getEstatisticas();
    
    if ($stats && isset($stats['total_clientes'])) {
        echo "   ✅ OK - Clientes: {$stats['total_clientes']}\n";
        echo "   ✅ OK - Vendas totais: {$stats['total_vendas']}\n";
        echo "   ✅ OK - Valor total: R$ " . number_format($stats['valor_total'], 2, ',', '.') . "\n";
    } else {
        echo "   ❌ FALHOU - Retornou vazio ou NULL\n";
        var_dump($stats);
    }
    
    // Teste 2: Atendimentos por período
    echo "\n2️⃣  Testando getAtendimentosPorPeriodo()...\n";
    $dt_inicio = date('Y-m-01'); // Primeiro dia do mês
    $dt_fim = date('Y-m-d');     // Hoje
    
    echo "   Período: {$dt_inicio} até {$dt_fim}\n";
    
    $atendimentos = $relatorio->getAtendimentosPorPeriodo($dt_inicio, $dt_fim);
    
    if (is_array($atendimentos) && count($atendimentos) > 0) {
        echo "   ✅ OK - Retornou " . count($atendimentos) . " dias\n";
        echo "   📊 Amostra (primeiros 3 dias):\n";
        
        foreach (array_slice($atendimentos, 0, 3) as $dia) {
            echo "      {$dia['data']} - {$dia['total']} vendas - {$dia['clientes_unicos']} clientes - R$ {$dia['valor_total']}\n";
        }
    } else {
        echo "   ❌ FALHOU - Retornou vazio\n";
        echo "   Tipo retornado: " . gettype($atendimentos) . "\n";
        if (is_array($atendimentos)) {
            echo "   Array vazio (count=0)\n";
        }
    }
    
    // Teste 3: Top clientes
    echo "\n3️⃣  Testando getTopClientes(5)...\n";
    $topClientes = $relatorio->getTopClientes(5);
    
    if (is_array($topClientes) && count($topClientes) > 0) {
        echo "   ✅ OK - Retornou " . count($topClientes) . " clientes\n";
        echo "   🏆 Top 3:\n";
        
        foreach (array_slice($topClientes, 0, 3) as $i => $cliente) {
            echo "      #" . ($i+1) . " - {$cliente['nm_cliente']} - {$cliente['total_atendimentos']} compras - R$ {$cliente['valor_total']}\n";
        }
    } else {
        echo "   ❌ FALHOU - Retornou vazio\n";
    }
    
    // Teste 4: Atendimentos detalhados
    echo "\n4️⃣  Testando getAtendimentosDetalhados()...\n";
    $detalhados = $relatorio->getAtendimentosDetalhados($dt_inicio, $dt_fim);
    
    if (is_array($detalhados) && count($detalhados) > 0) {
        echo "   ✅ OK - Retornou " . count($detalhados) . " registros\n";
    } else {
        echo "   ❌ FALHOU - Retornou vazio\n";
    }
    
    // Teste 5: Totais
    echo "\n5️⃣  Testando getTotaisAtendimentos()...\n";
    $totais = $relatorio->getTotaisAtendimentos($dt_inicio, $dt_fim);
    
    if ($totais && isset($totais['total_atendimentos'])) {
        echo "   ✅ OK - Total: {$totais['total_atendimentos']} atendimentos\n";
        echo "   ✅ OK - Clientes: {$totais['clientes_unicos']}\n";
        echo "   ✅ OK - Valor: R$ {$totais['valor_total']}\n";
    } else {
        echo "   ❌ FALHOU - Retornou vazio\n";
        var_dump($totais);
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "🔍 TESTANDO QUERY DIRETA NO BANCO:\n";
    echo str_repeat("=", 70) . "\n\n";
    
    // Conectar diretamente no bd_propasso
    $host = 'banco.propasso.systec.ftp.sh';
    $database = 'bd_propasso';
    $user = 'admin';
    $password = 'systec2011.';
    $port = '5432';
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "✅ Conectado diretamente em: {$database}\n\n";
    
    // Testar query simples
    $sql = "SELECT COUNT(*) FROM dm_orcamento_vendas_consolidadas";
    $count = $pdo->query($sql)->fetchColumn();
    echo "📊 Total de registros na VIEW: " . number_format($count) . "\n";
    
    // Testar com filtro de data
    $sql = "SELECT 
                DATE(dt_emi_pedido) as data,
                COUNT(DISTINCT cd_pedido) as total
            FROM dm_orcamento_vendas_consolidadas
            WHERE DATE(dt_emi_pedido) BETWEEN :dt_inicio AND :dt_fim
            GROUP BY DATE(dt_emi_pedido)
            ORDER BY data DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':dt_inicio' => $dt_inicio,
        ':dt_fim' => $dt_fim
    ]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📅 Últimos 5 dias do período {$dt_inicio} até {$dt_fim}:\n";
    foreach ($resultados as $row) {
        echo "   {$row['data']} - {$row['total']} vendas\n";
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ DIAGNÓSTICO COMPLETO!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
