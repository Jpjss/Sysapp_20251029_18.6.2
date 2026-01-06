<?php
/**
 * CONECTAR NO BANCO REAL DO ERP - PROPASSO
 */

echo "🔍 CONECTANDO NO BANCO REAL DO ERP\n\n";

try {
    $host = 'banco.propasso.systec.ftp.sh';
    $database = 'bd_propasso';
    $user = 'admin';
    $password = 'systec2011.';
    $port = '5432';
    
    echo "Tentando conexão...\n";
    echo "  Host: {$host}\n";
    echo "  Banco: {$database}\n";
    echo "  Porta: {$port}\n\n";
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $db = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "✅ CONECTADO COM SUCESSO!\n\n";
    
    // Listar schemas
    echo str_repeat("=", 70) . "\n";
    echo "SCHEMAS DISPONÍVEIS:\n";
    echo str_repeat("=", 70) . "\n";
    
    $schemas = $db->query("
        SELECT 
            schema_name,
            (SELECT COUNT(*) 
             FROM information_schema.tables 
             WHERE table_schema = schema_name) as total_tabelas
        FROM information_schema.schemata 
        WHERE schema_name NOT IN ('information_schema', 'pg_catalog', 'pg_toast')
        ORDER BY schema_name
    ")->fetchAll();
    
    foreach ($schemas as $schema) {
        echo sprintf("  • %-30s (%d tabelas)\n", $schema['schema_name'], $schema['total_tabelas']);
    }
    
    // Listar tabelas do schema public
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "TABELAS NO SCHEMA PUBLIC:\n";
    echo str_repeat("=", 70) . "\n";
    
    $tabelas = $db->query("
        SELECT tablename
        FROM pg_tables 
        WHERE schemaname = 'public'
        ORDER BY tablename
    ")->fetchAll();
    
    $tabelasVendas = [];
    $tabelasProdutos = [];
    $tabelasDM = [];
    
    foreach ($tabelas as $tabela) {
        $nome = $tabela['tablename'];
        
        // Categorizar
        if (strpos($nome, 'dm_') === 0) {
            $tabelasDM[] = $nome;
        } elseif (preg_match('/(ped|vd|venda|nf|orcamento)/i', $nome)) {
            $tabelasVendas[] = $nome;
        } elseif (preg_match('/(prod|item|est_)/i', $nome)) {
            $tabelasProdutos[] = $nome;
        }
    }
    
    echo "\n📊 TABELAS DM_* (Data Marts/Consolidadas):\n";
    echo str_repeat("-", 70) . "\n";
    if (empty($tabelasDM)) {
        echo "  (nenhuma)\n";
    } else {
        foreach ($tabelasDM as $t) {
            echo "  ✅ {$t}\n";
        }
    }
    
    echo "\n💰 TABELAS DE VENDAS:\n";
    echo str_repeat("-", 70) . "\n";
    if (empty($tabelasVendas)) {
        echo "  (nenhuma)\n";
    } else {
        foreach ($tabelasVendas as $t) {
            $count = $db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
            echo sprintf("  • %-50s %s registros\n", $t, number_format($count));
        }
    }
    
    echo "\n📦 TABELAS DE PRODUTOS:\n";
    echo str_repeat("-", 70) . "\n";
    if (empty($tabelasProdutos)) {
        echo "  (nenhuma)\n";
    } else {
        foreach ($tabelasProdutos as $t) {
            $count = $db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
            echo sprintf("  • %-50s %s registros\n", $t, number_format($count));
        }
    }
    
    // Se achou tabelas DM, verificar estrutura
    if (!empty($tabelasDM)) {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "ESTRUTURA DAS TABELAS DM_*:\n";
        echo str_repeat("=", 70) . "\n";
        
        foreach ($tabelasDM as $tabela) {
            echo "\n📋 Tabela: {$tabela}\n";
            echo str_repeat("-", 70) . "\n";
            
            $colunas = $db->query("
                SELECT column_name, data_type
                FROM information_schema.columns
                WHERE table_name = '{$tabela}'
                ORDER BY ordinal_position
            ")->fetchAll();
            
            foreach ($colunas as $col) {
                echo sprintf("  %-40s %s\n", $col['column_name'], $col['data_type']);
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nPossíveis causas:\n";
    echo "  • Servidor não acessível\n";
    echo "  • Credenciais incorretas\n";
    echo "  • Firewall bloqueando\n";
    echo "  • VPN necessária\n";
}
