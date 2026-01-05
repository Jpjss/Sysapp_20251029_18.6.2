<?php
/**
 * Diagnóstico de Conexão com Bancos dos Clientes
 * Verifica se o sistema consegue conectar aos bancos de dados dos clientes
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Conexão - Clientes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        h2 {
            color: #667eea;
            margin-top: 30px;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.9em;
        }
        .btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Conexão com Bancos dos Clientes</h1>
        
        <?php
        echo "<div class='info'>";
        echo "<strong>📅 Data/Hora:</strong> " . date('d/m/Y H:i:s') . "<br>";
        echo "<strong>🌐 Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "<br>";
        echo "<strong>🔑 Usuário Logado:</strong> " . (Session::check('Questionarios.cd_usu') ? Session::read('Questionarios.cd_usu') : 'NÃO LOGADO') . "<br>";
        echo "<strong>🏢 Empresa Selecionada:</strong> " . (Session::check('Config.database') ? Session::read('Config.database') : 'NENHUMA') . "<br>";
        echo "</div>";
        
        // 1. Verificar se usuário está logado
        echo "<h2>1️⃣ Status da Sessão</h2>";
        if (Session::isValid()) {
            echo "<div class='status success'>✅ Usuário está logado</div>";
            echo "<pre>";
            echo "cd_usu: " . Session::read('Questionarios.cd_usu') . "\n";
            echo "login: " . Session::read('Questionarios.login') . "\n";
            echo "</pre>";
        } else {
            echo "<div class='status error'>❌ Usuário NÃO está logado. Faça login primeiro.</div>";
            echo "<a href='index.php?url=usuarios/login' class='btn'>Fazer Login</a>";
            exit;
        }
        
        // 2. Conectar ao banco principal (SysApp)
        echo "<h2>2️⃣ Conexão com Banco Principal (SysApp)</h2>";
        $db = Database::getInstance();
        $conn = $db->connect();
        
        if ($conn) {
            echo "<div class='status success'>✅ Conectado ao banco SysApp com sucesso!</div>";
            echo "<pre>";
            echo "Host: banco.propasso.systec.ftp.sh\n";
            echo "Banco: bd_propasso\n";
            echo "Porta: 5432\n";
            echo "</pre>";
        } else {
            echo "<div class='status error'>❌ ERRO: Não foi possível conectar ao banco SysApp</div>";
            exit;
        }
        
        // 3. Buscar todas as empresas cadastradas
        echo "<h2>3️⃣ Empresas Cadastradas no Sistema</h2>";
        
        $sql_empresas = "
            SELECT 
                cd_empresa,
                nm_empresa as nome_empresa,
                ds_host,
                ds_banco,
                ds_usuario as ds_usuario_banco,
                ds_senha as ds_senha_banco,
                ds_porta as porta,
                COALESCE(fg_ativo, 'S') as ativa
            FROM sysapp_config_empresas
            WHERE COALESCE(fg_ativo, 'S') = 'S'
            ORDER BY nm_empresa
        ";
        
        $result = pg_query($conn, $sql_empresas);
        
        if (!$result) {
            echo "<div class='status error'>❌ ERRO ao buscar empresas: " . pg_last_error($conn) . "</div>";
            exit;
        }
        
        $empresas = pg_fetch_all($result);
        
        if (!$empresas) {
            echo "<div class='status warning'>⚠️ Nenhuma empresa ativa encontrada no sistema</div>";
            exit;
        }
        
        echo "<div class='status info'>📊 Total de empresas ativas: <strong>" . count($empresas) . "</strong></div>";
        
        // 4. Testar conexão com cada empresa
        echo "<h2>4️⃣ Teste de Conexão com Bancos dos Clientes</h2>";
        
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th>Empresa</th>";
        echo "<th>Host</th>";
        echo "<th>Banco</th>";
        echo "<th>Porta</th>";
        echo "<th>Status Conexão</th>";
        echo "<th>Tabela dm_produto</th>";
        echo "<th>Tabela dm_orcamento_vendas_consolidadas</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $total_sucesso = 0;
        $total_erro = 0;
        $problemas = [];
        
        foreach ($empresas as $index => $empresa) {
            $numero = $index + 1;
            $cd_empresa = $empresa['cd_empresa'];
            $nome = $empresa['nome_empresa'];
            $host = $empresa['ds_host'];
            $banco = $empresa['ds_banco'];
            $usuario = $empresa['ds_usuario_banco'];
            $senha = $empresa['ds_senha_banco'];
            $porta = $empresa['porta'];
            
            echo "<tr>";
            echo "<td>{$numero}</td>";
            echo "<td><strong>{$nome}</strong></td>";
            echo "<td>{$host}</td>";
            echo "<td>{$banco}</td>";
            echo "<td>{$porta}</td>";
            
            // Tentar conectar ao banco do cliente
            try {
                $dsn = "pgsql:host={$host};port={$porta};dbname={$banco}";
                $conn_cliente = new PDO($dsn, $usuario, $senha, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5
                ]);
                
                echo "<td><span class='badge badge-success'>✅ CONECTADO</span></td>";
                $total_sucesso++;
                
                // Verificar se tabela dm_produto existe
                $sql_check_produto = "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = 'dm_produto'
                )";
                $stmt = $conn_cliente->query($sql_check_produto);
                $exists_produto = $stmt->fetchColumn();
                
                if ($exists_produto) {
                    // Contar registros
                    $count_produto = $conn_cliente->query("SELECT COUNT(*) FROM dm_produto")->fetchColumn();
                    echo "<td><span class='badge badge-success'>✅ OK ({$count_produto} registros)</span></td>";
                } else {
                    echo "<td><span class='badge badge-danger'>❌ NÃO EXISTE</span></td>";
                    $problemas[] = "{$nome}: Tabela dm_produto não existe";
                }
                
                // Verificar se tabela dm_orcamento_vendas_consolidadas existe
                $sql_check_vendas = "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = 'dm_orcamento_vendas_consolidadas'
                )";
                $stmt = $conn_cliente->query($sql_check_vendas);
                $exists_vendas = $stmt->fetchColumn();
                
                if ($exists_vendas) {
                    // Contar registros
                    $count_vendas = $conn_cliente->query("SELECT COUNT(*) FROM dm_orcamento_vendas_consolidadas")->fetchColumn();
                    echo "<td><span class='badge badge-success'>✅ OK ({$count_vendas} registros)</span></td>";
                } else {
                    echo "<td><span class='badge badge-danger'>❌ NÃO EXISTE</span></td>";
                    $problemas[] = "{$nome}: Tabela dm_orcamento_vendas_consolidadas não existe";
                }
                
                $conn_cliente = null;
                
            } catch (PDOException $e) {
                echo "<td><span class='badge badge-danger'>❌ ERRO</span></td>";
                echo "<td colspan='2'><small>{$e->getMessage()}</small></td>";
                $total_erro++;
                $problemas[] = "{$nome}: Erro de conexão - " . $e->getMessage();
            }
            
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        
        // 5. Resumo
        echo "<h2>5️⃣ Resumo do Diagnóstico</h2>";
        
        $total_empresas = count($empresas);
        $percentual_sucesso = round(($total_sucesso / $total_empresas) * 100, 2);
        
        echo "<div class='status " . ($total_erro == 0 ? 'success' : 'warning') . "'>";
        echo "<strong>📊 Estatísticas:</strong><br>";
        echo "Total de empresas testadas: {$total_empresas}<br>";
        echo "Conexões bem-sucedidas: {$total_sucesso} ({$percentual_sucesso}%)<br>";
        echo "Conexões com erro: {$total_erro}<br>";
        echo "</div>";
        
        // 6. Problemas detectados
        if (count($problemas) > 0) {
            echo "<h2>⚠️ Problemas Detectados</h2>";
            echo "<div class='status error'>";
            echo "<ul>";
            foreach ($problemas as $problema) {
                echo "<li>{$problema}</li>";
            }
            echo "</ul>";
            echo "</div>";
            
            echo "<div class='status info'>";
            echo "<strong>💡 Recomendações:</strong><br>";
            echo "1. Verifique as credenciais de conexão das empresas com erro<br>";
            echo "2. Certifique-se de que os bancos de dados dos clientes estão online<br>";
            echo "3. Verifique as tabelas necessárias (dm_produto e dm_orcamento_vendas_consolidadas)<br>";
            echo "4. Confirme as regras de firewall e acesso remoto aos servidores<br>";
            echo "</div>";
        } else {
            echo "<div class='status success'>";
            echo "✅ <strong>Tudo OK!</strong> Todas as empresas estão com as conexões funcionando corretamente.";
            echo "</div>";
        }
        
        // 7. Teste com empresa da sessão
        if (Session::check('Config.database')) {
            echo "<h2>6️⃣ Teste com Empresa da Sessão Atual</h2>";
            
            $host_sessao = Session::read('Config.host');
            $db_sessao = Session::read('Config.database');
            $user_sessao = Session::read('Config.user');
            $pass_sessao = Session::read('Config.password');
            $port_sessao = Session::read('Config.porta');
            
            echo "<div class='info'>";
            echo "<strong>Host:</strong> {$host_sessao}<br>";
            echo "<strong>Banco:</strong> {$db_sessao}<br>";
            echo "<strong>Porta:</strong> {$port_sessao}<br>";
            echo "</div>";
            
            try {
                $dsn = "pgsql:host={$host_sessao};port={$port_sessao};dbname={$db_sessao}";
                $conn_sessao = new PDO($dsn, $user_sessao, $pass_sessao, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5
                ]);
                
                echo "<div class='status success'>✅ Conexão com banco da sessão OK!</div>";
                
                // Testar query de vendas
                echo "<h3>Teste de Query de Vendas (Últimos 30 dias)</h3>";
                
                $sql_test = "
                    SELECT 
                        dm_produto.cd_marca,
                        dm_produto.ds_marca,
                        COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
                        SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
                        SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
                    FROM dm_produto
                    INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
                        ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
                    WHERE dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL '30 days'
                        AND dm_produto.cd_marca IS NOT NULL
                        AND dm_produto.ds_marca IS NOT NULL
                    GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
                    ORDER BY quantidade_vendida DESC
                    LIMIT 10
                ";
                
                $stmt = $conn_sessao->query($sql_test);
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($resultado) > 0) {
                    echo "<div class='status success'>✅ Query executada com sucesso! Encontradas " . count($resultado) . " marcas.</div>";
                    
                    echo "<table>";
                    echo "<thead><tr><th>Código</th><th>Marca</th><th>Vendas</th><th>Quantidade</th><th>Valor Total</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($resultado as $marca) {
                        echo "<tr>";
                        echo "<td>{$marca['cd_marca']}</td>";
                        echo "<td>{$marca['ds_marca']}</td>";
                        echo "<td>{$marca['total_vendas']}</td>";
                        echo "<td>" . number_format($marca['quantidade_vendida'], 0, ',', '.') . "</td>";
                        echo "<td>R$ " . number_format($marca['valor_total'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<div class='status warning'>⚠️ Nenhuma venda encontrada nos últimos 30 dias</div>";
                }
                
            } catch (PDOException $e) {
                echo "<div class='status error'>❌ ERRO ao conectar: " . $e->getMessage() . "</div>";
            }
        }
        
        ?>
        
        <div style="margin-top: 30px;">
            <a href="index.php" class="btn">« Voltar ao Sistema</a>
            <a href="?refresh=1" class="btn">🔄 Atualizar Diagnóstico</a>
        </div>
    </div>
</body>
</html>
