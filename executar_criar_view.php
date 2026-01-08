<?php
/**
 * EXECUTAR SCRIPT SQL - CRIAR VIEW DE COMPATIBILIDADE
 */

echo "🔧 CRIANDO VIEW DE COMPATIBILIDADE NO BANCO\n\n";

try {
    $host = 'banco.propasso.systec.ftp.sh';
    $database = 'bd_propasso';
    $user = 'admin';
    $password = 'systec2011.';
    $port = '5432';
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $db = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conectado ao banco: {$database}\n\n";
    
    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/criar_view_compatibilidade.sql');
    
    echo "📄 Executando script SQL...\n\n";
    echo str_repeat("=", 70) . "\n";
    
    // Dividir por comandos (separados por ponto-e-vírgula)
    $comandos = explode(';', $sql);
    
    $sucessos = 0;
    $erros = 0;
    
    foreach ($comandos as $i => $comando) {
        $comando = trim($comando);
        
        // Pular comandos vazios e comentários
        if (empty($comando) || strpos($comando, '--') === 0) {
            continue;
        }
        
        // Pular comandos SELECT de verificação
        if (stripos($comando, 'SELECT') === 0) {
            continue;
        }
        
        try {
            echo "\n[" . ($i + 1) . "] Executando comando...\n";
            $db->exec($comando);
            echo "✅ OK\n";
            $sucessos++;
        } catch (Exception $e) {
            echo "❌ ERRO: " . $e->getMessage() . "\n";
            $erros++;
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "\n📊 RESULTADO:\n";
    echo "  ✅ Sucessos: {$sucessos}\n";
    echo "  ❌ Erros: {$erros}\n\n";
    
    // Testar a view
    echo str_repeat("=", 70) . "\n";
    echo "🧪 TESTANDO A VIEW:\n";
    echo str_repeat("=", 70) . "\n\n";
    
    $total = $db->query("SELECT COUNT(*) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
    echo "✅ Total de registros na view: " . number_format($total) . "\n\n";
    
    $amostra = $db->query("
        SELECT 
            cd_pedido, 
            cd_pessoa, 
            nm_cliente, 
            dt_emi_pedido, 
            vl_tot_it 
        FROM dm_orcamento_vendas_consolidadas 
        LIMIT 5
    ")->fetchAll();
    
    echo "📋 Amostra de dados:\n";
    echo str_repeat("-", 70) . "\n";
    foreach ($amostra as $row) {
        echo sprintf("Pedido: %d | Cliente: %s | Data: %s | Valor: R$ %.2f\n",
            $row['cd_pedido'],
            $row['nm_cliente'],
            $row['dt_emi_pedido'],
            $row['vl_tot_it']
        );
    }
    
    echo "\n✅ VIEW CRIADA E FUNCIONANDO PERFEITAMENTE!\n\n";
    echo "🎉 Agora os relatórios devem funcionar!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO FATAL: " . $e->getMessage() . "\n";
}
