<?php
/**
 * RECRIAR VIEW FORÇADAMENTE
 */

try {
    $dsn = "pgsql:host=banco.propasso.systec.ftp.sh;port=5432;dbname=bd_propasso";
    $db = new PDO($dsn, 'admin', 'systec2011.', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "🔧 Recriando view...\n\n";
    
    // 1. Remover view completamente
    $db->exec("DROP VIEW IF EXISTS dm_orcamento_vendas_consolidadas CASCADE");
    echo "✅ View antiga removida\n";
    
    // 2. Criar nova view
    $db->exec("
        CREATE VIEW dm_orcamento_vendas_consolidadas AS
        SELECT 
            *,
            nm_pessoa as nm_cliente
        FROM dm_orcamento_vendas_consolidadas_cubo
    ");
    echo "✅ View recriada\n\n";
    
    // 3. Testar
    $count = $db->query("SELECT COUNT(*) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
    echo "📊 Total: " . number_format($count) . " registros\n\n";
    
    $teste = $db->query("
        SELECT cd_pedido, nm_cliente, dt_emi_pedido, vl_tot_it 
        FROM dm_orcamento_vendas_consolidadas 
        WHERE nm_cliente IS NOT NULL
        LIMIT 3
    ")->fetchAll();
    
    echo "📋 Amostra:\n";
    foreach ($teste as $row) {
        echo sprintf("  Pedido %d - %s - R$ %.2f\n", 
            $row['cd_pedido'], 
            $row['nm_cliente'], 
            $row['vl_tot_it']
        );
    }
    
    echo "\n✅ VIEW FUNCIONANDO PERFEITAMENTE!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
