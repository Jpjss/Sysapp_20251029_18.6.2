<?php
/**
 * Script de teste para diagnosticar problemas no dashboard de marcas
 */

// Configurar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><meta charset='utf-8'><title>Teste API Dashboard</title></head><body>";
echo "<h1>🔍 Diagnóstico Dashboard de Marcas</h1>";

// Importar dependências
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

echo "<h2>1. Verificação de Sessão</h2>";
echo "<pre>";
echo "Session Valid: " . (Session::isValid() ? "✅ SIM" : "❌ NÃO") . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Has Config.database: " . (Session::check('Config.database') ? "✅ SIM" : "❌ NÃO") . "\n";

if (Session::check('Config.database')) {
    echo "\nConfiguração do Banco:\n";
    echo "- Database: " . Session::read('Config.database') . "\n";
    echo "- Host: " . Session::read('Config.host') . "\n";
    echo "- Porta: " . Session::read('Config.porta') . "\n";
    echo "- User: " . Session::read('Config.user') . "\n";
} else {
    echo "\n⚠️ PROBLEMA: Nenhuma empresa selecionada na sessão!\n";
    echo "\nSoluções possíveis:\n";
    echo "1. Faça login no sistema\n";
    echo "2. Selecione uma empresa em /relatorios/empresa\n";
    echo "</pre>";
    echo "<a href='/usuarios/login'>➜ Fazer Login</a> | ";
    echo "<a href='/relatorios/empresa'>➜ Selecionar Empresa</a>";
    echo "</body></html>";
    exit;
}
echo "</pre>";

// Testar conexão com banco
echo "<h2>2. Teste de Conexão com Banco</h2>";
echo "<pre>";

try {
    $host = Session::read('Config.host');
    $database = Session::read('Config.database');
    $user = Session::read('Config.user');
    $password = Session::read('Config.password');
    $port = Session::read('Config.porta');
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexão estabelecida com sucesso!\n";
    
    // Verificar se as tabelas existem
    echo "\n<h3>3. Verificação de Tabelas</h3>";
    
    $tabelas = [
        'dm_produto' => 'Tabela de Produtos',
        'dm_orcamento_vendas_consolidadas' => 'Tabela de Vendas Consolidadas'
    ];
    
    foreach ($tabelas as $tabela => $descricao) {
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = '$tabela'
        )";
        $stmt = $conn->query($sql);
        $exists = $stmt->fetchColumn();
        
        if ($exists === 't' || $exists === true) {
            echo "✅ $descricao ($tabela) - EXISTE\n";
        } else {
            echo "❌ $descricao ($tabela) - NÃO EXISTE\n";
        }
    }
    
    // Testar query de marcas vendidas
    echo "\n<h3>4. Teste de Query - Marcas Vendidas (últimos 30 dias)</h3>";
    
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
        WHERE dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '$periodo days'
            AND dm_produto.cd_marca IS NOT NULL
            AND dm_produto.ds_marca IS NOT NULL
        GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
        ORDER BY quantidade_vendida DESC
        LIMIT $limite
    ";
    
    echo "Executando query...\n";
    $stmt = $conn->query($sql);
    $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Query executada com sucesso!\n";
    echo "Total de marcas retornadas: " . count($marcas) . "\n\n";
    
    if (count($marcas) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #667eea; color: white;'>
                <th>Código</th>
                <th>Marca</th>
                <th>Total Vendas</th>
                <th>Qtde Vendida</th>
                <th>Valor Total (R$)</th>
              </tr>";
        
        foreach ($marcas as $marca) {
            echo "<tr>";
            echo "<td>{$marca['cd_marca']}</td>";
            echo "<td>{$marca['ds_marca']}</td>";
            echo "<td>" . number_format($marca['total_vendas'], 0, ',', '.') . "</td>";
            echo "<td>" . number_format($marca['quantidade_vendida'], 0, ',', '.') . "</td>";
            echo "<td>R$ " . number_format($marca['valor_total'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "⚠️ Nenhuma marca encontrada nos últimos 30 dias.\n";
        echo "Isso pode significar que:\n";
        echo "- Não há vendas registradas no período\n";
        echo "- Os produtos não têm marcas associadas\n";
        echo "- A estrutura das tabelas está diferente do esperado\n";
    }
    
    // Teste adicional: verificar se há vendas no banco
    echo "\n<h3>5. Verificação de Dados</h3>";
    
    $sqlTotalVendas = "SELECT COUNT(*) as total FROM dm_orcamento_vendas_consolidadas WHERE dt_emi_pedido >= CURRENT_DATE - INTERVAL '30 days'";
    $stmt = $conn->query($sqlTotalVendas);
    $totalVendas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total de vendas nos últimos 30 dias: " . number_format($totalVendas['total'], 0, ',', '.') . "\n";
    
    if ($totalVendas['total'] == 0) {
        echo "\n⚠️ ATENÇÃO: Não há vendas registradas nos últimos 30 dias!\n";
    }
    
    // Verificar produtos com marca
    $sqlProdutosComMarca = "SELECT COUNT(*) as total FROM dm_produto WHERE cd_marca IS NOT NULL AND ds_marca IS NOT NULL";
    $stmt = $conn->query($sqlProdutosComMarca);
    $produtosComMarca = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total de produtos com marca: " . number_format($produtosComMarca['total'], 0, ',', '.') . "\n";
    
    if ($produtosComMarca['total'] == 0) {
        echo "\n⚠️ ATENÇÃO: Nenhum produto tem marca associada!\n";
    }
    
    echo "\n<h3>✅ Diagnóstico concluído!</h3>";
    
    if (count($marcas) > 0) {
        echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<strong>✅ Tudo funcionando corretamente!</strong><br>";
        echo "Os dados estão sendo carregados com sucesso.<br>";
        echo "Se o dashboard não está exibindo os dados, o problema pode estar no frontend (JavaScript).<br><br>";
        echo "<strong>Sugestões:</strong><br>";
        echo "1. Abra o Console do Navegador (F12) e verifique erros<br>";
        echo "2. Vá para a aba Network e veja se as requisições para /api/marcas_vendas.php estão retornando 200 OK<br>";
        echo "3. Verifique se há erros de CORS ou problemas de autenticação<br>";
        echo "</div>";
        
        echo "<a href='/marcasvendas/dashboard' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>➜ Abrir Dashboard</a>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<strong>⚠️ Dados insuficientes</strong><br>";
        echo "O banco está acessível, mas não há dados de vendas para exibir.<br>";
        echo "Possíveis causas:<br>";
        echo "- Não há vendas nos últimos 30 dias<br>";
        echo "- Produtos não têm marcas associadas<br>";
        echo "- Período selecionado sem movimentação<br>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao banco:\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "\nVerifique:\n";
    echo "- Credenciais do banco de dados\n";
    echo "- Se o PostgreSQL está rodando\n";
    echo "- Se a empresa selecionada tem configuração correta\n";
} catch (Exception $e) {
    echo "❌ Erro geral:\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "</body></html>";
