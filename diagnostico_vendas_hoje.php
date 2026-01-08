<?php
/**
 * Diagnóstico: Por que Vendas Hoje está zerado?
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

echo "<html><head><meta charset='utf-8'><title>Diagnóstico Vendas Hoje</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h1 { color: #333; }
    h2 { color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
    .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0; }
    .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0; }
    .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #f59e0b; margin: 10px 0; }
    .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 10px 0; }
    pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; background: white; }
    th { background: #667eea; color: white; padding: 10px; text-align: left; }
    td { padding: 8px; border: 1px solid #ddd; }
    tr:nth-child(even) { background: #f8f9fa; }
</style></head><body>";

echo "<h1>🔍 Diagnóstico: Vendas Hoje</h1>";

// Verificar sessão
if (!Session::isValid()) {
    echo "<div class='error'>❌ Sessão inválida. <a href='/usuarios/login'>Fazer login</a></div>";
    echo "</body></html>";
    exit;
}

if (!Session::check('Config.database')) {
    echo "<div class='error'>❌ Nenhuma empresa selecionada. <a href='/relatorios/empresa'>Selecionar empresa</a></div>";
    echo "</body></html>";
    exit;
}

echo "<div class='success'>✅ Sessão válida e empresa selecionada: <strong>" . Session::read('Config.database') . "</strong></div>";

// Conectar ao banco
try {
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ Conectado ao banco de dados</div>";
    
    // Data atual do servidor PostgreSQL
    echo "<h2>1. Data Atual do Servidor PostgreSQL</h2>";
    $sql = "SELECT 
                CURRENT_DATE as data_atual,
                CURRENT_TIMESTAMP as timestamp_atual,
                NOW() as now,
                CURRENT_TIME as hora_atual";
    $stmt = $conn->query($sql);
    $dateInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Informação</th><th>Valor</th></tr>";
    echo "<tr><td>CURRENT_DATE</td><td><strong>{$dateInfo['data_atual']}</strong></td></tr>";
    echo "<tr><td>CURRENT_TIMESTAMP</td><td><strong>{$dateInfo['timestamp_atual']}</strong></td></tr>";
    echo "<tr><td>NOW()</td><td><strong>{$dateInfo['now']}</strong></td></tr>";
    echo "<tr><td>CURRENT_TIME</td><td><strong>{$dateInfo['hora_atual']}</strong></td></tr>";
    echo "</table>";
    
    $dataAtual = $dateInfo['data_atual'];
    
    // Verificar última venda registrada
    echo "<h2>2. Últimas 10 Vendas Registradas</h2>";
    $sql = "SELECT 
                cd_pedido,
                cd_pessoa,
                dt_emi_pedido,
                DATE(dt_emi_pedido) as data_venda,
                COALESCE(vl_tot_it - vl_devol_proporcional, 0)::NUMERIC(14,2) as valor
            FROM dm_orcamento_vendas_consolidadas
            ORDER BY dt_emi_pedido DESC
            LIMIT 10";
    
    $stmt = $conn->query($sql);
    $ultimasVendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($ultimasVendas) {
        echo "<table>";
        echo "<tr><th>Pedido</th><th>Cliente</th><th>Data/Hora Emissão</th><th>Data</th><th>Valor</th></tr>";
        foreach ($ultimasVendas as $venda) {
            $destaque = ($venda['data_venda'] === $dataAtual) ? " style='background: #e8f5e9; font-weight: bold;'" : "";
            echo "<tr{$destaque}>";
            echo "<td>{$venda['cd_pedido']}</td>";
            echo "<td>{$venda['cd_pessoa']}</td>";
            echo "<td>{$venda['dt_emi_pedido']}</td>";
            echo "<td>{$venda['data_venda']}</td>";
            echo "<td>R$ " . number_format($venda['valor'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='warning'>⚠️ Nenhuma venda encontrada na tabela</div>";
    }
    
    // Query atual do sistema (a que está com problema)
    echo "<h2>3. Query Atual do Sistema (Vendas Hoje)</h2>";
    $sql = "SELECT COUNT(DISTINCT cd_pedido) as total, 
                   COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_hoje 
            FROM dm_orcamento_vendas_consolidadas 
            WHERE dt_emi_pedido >= CURRENT_DATE 
            AND dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'";
    
    echo "<pre>$sql</pre>";
    
    $stmt = $conn->query($sql);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<div class='info'>";
    echo "<strong>Resultado:</strong><br>";
    echo "Total de vendas: <strong>" . $resultado['total'] . "</strong><br>";
    echo "Valor total: <strong>R$ " . number_format($resultado['valor_hoje'], 2, ',', '.') . "</strong>";
    echo "</div>";
    
    if ($resultado['total'] == 0) {
        echo "<div class='warning'>";
        echo "⚠️ <strong>PROBLEMA IDENTIFICADO:</strong> A query retorna 0 vendas para hoje.<br><br>";
        echo "<strong>Possíveis causas:</strong><br>";
        echo "1. Realmente não há vendas hoje ({$dataAtual})<br>";
        echo "2. As vendas estão com data diferente de hoje<br>";
        echo "3. Problema com fuso horário / timestamp<br>";
        echo "</div>";
    }
    
    // Teste: Contar vendas por data (últimos 30 dias)
    echo "<h2>4. Vendas por Data (Últimos 30 Dias)</h2>";
    $sql = "SELECT 
                DATE(dt_emi_pedido) as data,
                COUNT(DISTINCT cd_pedido) as total_vendas,
                COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
            FROM dm_orcamento_vendas_consolidadas
            WHERE dt_emi_pedido >= CURRENT_DATE - INTERVAL '30 days'
            GROUP BY DATE(dt_emi_pedido)
            ORDER BY DATE(dt_emi_pedido) DESC
            LIMIT 15";
    
    $stmt = $conn->query($sql);
    $vendasPorData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($vendasPorData) {
        echo "<table>";
        echo "<tr><th>Data</th><th>Total Vendas</th><th>Valor Total</th><th>Status</th></tr>";
        foreach ($vendasPorData as $dia) {
            $isHoje = ($dia['data'] === $dataAtual);
            $destaque = $isHoje ? " style='background: #e8f5e9; font-weight: bold;'" : "";
            $status = $isHoje ? "✅ HOJE" : "";
            
            echo "<tr{$destaque}>";
            echo "<td>{$dia['data']}</td>";
            echo "<td>" . number_format($dia['total_vendas'], 0, ',', '.') . "</td>";
            echo "<td>R$ " . number_format($dia['valor_total'], 2, ',', '.') . "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='warning'>⚠️ Nenhuma venda nos últimos 30 dias</div>";
    }
    
    // Verificar se há vendas "hoje" com outras condições
    echo "<h2>5. Testes Alternativos de Query</h2>";
    
    // Teste 1: Apenas com DATE()
    echo "<h3>Teste 1: Usando apenas DATE(dt_emi_pedido) = CURRENT_DATE</h3>";
    $sql = "SELECT COUNT(DISTINCT cd_pedido) as total,
                   COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor
            FROM dm_orcamento_vendas_consolidadas 
            WHERE DATE(dt_emi_pedido) = CURRENT_DATE";
    
    echo "<pre>$sql</pre>";
    $stmt = $conn->query($sql);
    $teste1 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<div class='info'>Resultado: <strong>{$teste1['total']}</strong> vendas | R$ " . number_format($teste1['valor'], 2, ',', '.') . "</div>";
    
    // Teste 2: Com cast explícito
    echo "<h3>Teste 2: Com cast para DATE</h3>";
    $sql = "SELECT COUNT(DISTINCT cd_pedido) as total,
                   COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor
            FROM dm_orcamento_vendas_consolidadas 
            WHERE dt_emi_pedido::DATE = CURRENT_DATE";
    
    echo "<pre>$sql</pre>";
    $stmt = $conn->query($sql);
    $teste2 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<div class='info'>Resultado: <strong>{$teste2['total']}</strong> vendas | R$ " . number_format($teste2['valor'], 2, ',', '.') . "</div>";
    
    // Teste 3: Últimas 24 horas
    echo "<h3>Teste 3: Últimas 24 horas</h3>";
    $sql = "SELECT COUNT(DISTINCT cd_pedido) as total,
                   COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor
            FROM dm_orcamento_vendas_consolidadas 
            WHERE dt_emi_pedido >= NOW() - INTERVAL '24 hours'";
    
    echo "<pre>$sql</pre>";
    $stmt = $conn->query($sql);
    $teste3 = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<div class='info'>Resultado: <strong>{$teste3['total']}</strong> vendas | R$ " . number_format($teste3['valor'], 2, ',', '.') . "</div>";
    
    // CONCLUSÃO
    echo "<h2>📊 Conclusão</h2>";
    
    if ($resultado['total'] == 0 && $teste1['total'] == 0 && $teste2['total'] == 0 && $teste3['total'] == 0) {
        echo "<div class='warning'>";
        echo "<strong>🔍 DIAGNÓSTICO:</strong> Não há vendas registradas hoje ({$dataAtual})<br><br>";
        echo "<strong>Última venda:</strong> " . ($ultimasVendas[0]['data_venda'] ?? 'N/A') . "<br>";
        echo "<strong>Isso é normal se:</strong><br>";
        echo "- O sistema não teve vendas hoje<br>";
        echo "- É início de expediente<br>";
        echo "- O banco de dados está sendo alimentado com dados históricos<br>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<strong>✅ SOLUÇÃO ENCONTRADA!</strong><br><br>";
        echo "Uma das queries alternativas encontrou vendas hoje.<br>";
        echo "A query do sistema precisa ser ajustada para usar a condição que funcionou.";
        echo "</div>";
    }
    
    echo "<br><a href='/relatorios/index' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>← Voltar ao Dashboard</a>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "❌ Erro de conexão com banco de dados:<br>";
    echo "<strong>Mensagem:</strong> " . $e->getMessage();
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ Erro geral:<br>";
    echo "<strong>Mensagem:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</body></html>";
