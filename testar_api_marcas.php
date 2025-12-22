<?php
/**
 * Teste direto da API de marcas vendidas
 */

// Inicia output buffering para capturar erros
ob_start();

try {
    echo "<h2>Teste da API de Marcas Vendidas</h2>";
    echo "<hr>";
    
    // Simula requisição da API
    $_GET['periodo'] = '30';
    $_GET['limite'] = '10';
    
    echo "<h3>1. Carregando configurações...</h3>";
    require_once 'config/config.php';
    require_once 'config/database.php';
    require_once 'core/Session.php';
    echo "✓ Configurações carregadas<br>";
    
    echo "<h3>2. Iniciando sessão...</h3>";
    Session::start();
    echo "✓ Sessão iniciada<br>";
    
    echo "<h3>3. Verificando autenticação...</h3>";
    if (!Session::isValid()) {
        echo "❌ Usuário não autenticado<br>";
        echo "<strong>Acesse o sistema primeiro em <a href='/'>http://localhost:8000</a></strong>";
        exit;
    }
    echo "✓ Usuário autenticado<br>";
    
    echo "<h3>4. Verificando configuração de banco...</h3>";
    if (!Session::check('Config.database')) {
        echo "❌ Nenhuma empresa selecionada<br>";
        exit;
    }
    
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    echo "Host: {$host}<br>";
    echo "Database: {$database}<br>";
    echo "User: {$user}<br>";
    echo "Port: {$port}<br>";
    
    echo "<h3>5. Conectando ao banco...</h3>";
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conectado ao banco<br>";
    
    echo "<h3>6. Executando query...</h3>";
    $periodo = 30;
    $limite = 10;
    
    $sql = "
        SELECT 
            dm_produto.cd_marca,
            dm_produto.ds_marca,
            COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
            SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
            SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
        FROM dm_produto
        INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
            ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
        WHERE 1=1
            AND dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '$periodo days'
            AND dm_produto.cd_marca IS NOT NULL
            AND dm_produto.ds_marca IS NOT NULL
        GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
        ORDER BY quantidade_vendida DESC
        LIMIT $limite
    ";
    
    echo "<pre>SQL:\n" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✓ Query executada com sucesso<br>";
    echo "<strong>Total de marcas encontradas: " . count($marcas) . "</strong><br>";
    
    echo "<h3>7. Dados retornados:</h3>";
    echo "<pre>";
    print_r($marcas);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ ERRO:</h3>";
    echo "<strong>Tipo:</strong> " . get_class($e) . "<br>";
    echo "<strong>Mensagem:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Arquivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Linha:</strong> " . $e->getLine() . "<br>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

$output = ob_get_clean();
echo $output;
