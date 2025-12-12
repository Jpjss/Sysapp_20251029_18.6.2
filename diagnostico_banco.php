<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

echo "<h1>Diagnóstico de Banco de Dados</h1>";

// Verifica empresas cadastradas
echo "<h2>1. Empresas Cadastradas</h2>";
$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'systec', '5432');

$empresas = $db->fetchAll("SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, ds_porta 
                           FROM sysapp_config_empresas 
                           ORDER BY cd_empresa");

if ($empresas) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>CD</th><th>Nome Empresa</th><th>Host</th><th>Banco</th><th>Usuário</th><th>Porta</th><th>Ação</th></tr>";
    foreach ($empresas as $emp) {
        echo "<tr>";
        echo "<td>{$emp['cd_empresa']}</td>";
        echo "<td>{$emp['nm_empresa']}</td>";
        echo "<td>{$emp['ds_host']}</td>";
        echo "<td>{$emp['ds_banco']}</td>";
        echo "<td>{$emp['ds_usuario']}</td>";
        echo "<td>{$emp['ds_porta']}</td>";
        echo "<td><a href='?verificar={$emp['cd_empresa']}'>Verificar Estrutura</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhuma empresa cadastrada.</p>";
}

// Se solicitado, verifica estrutura de um banco específico
if (isset($_GET['verificar'])) {
    $cd_empresa = (int)$_GET['verificar'];
    
    // Busca dados da empresa
    $empresa = $db->fetchOne("SELECT * FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa");
    
    if ($empresa) {
        echo "<hr><h2>2. Estrutura do Banco: {$empresa['nm_empresa']}</h2>";
        
        echo "<p><strong>Dados de Conexão:</strong></p>";
        echo "<ul>";
        echo "<li>Host: {$empresa['ds_host']}</li>";
        echo "<li>Banco: {$empresa['ds_banco']}</li>";
        echo "<li>Usuário: {$empresa['ds_usuario']}</li>";
        echo "<li>Porta: {$empresa['ds_porta']}</li>";
        echo "</ul>";
        
        // Tenta conectar
        $dbCliente = Database::getInstance();
        $senha = Security::decrypt($empresa['ds_senha']); // Descriptografa a senha
        
        echo "<p>Tentando conectar...</p>";
        
        $conn = @pg_connect("host={$empresa['ds_host']} port={$empresa['ds_porta']} dbname={$empresa['ds_banco']} user={$empresa['ds_usuario']} password=$senha");
        
        if ($conn) {
            echo "<p style='color:green'>✅ Conexão bem-sucedida!</p>";
            
            // Lista todas as tabelas
            echo "<h3>Tabelas Disponíveis:</h3>";
            $result = pg_query($conn, "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
            
            if ($result) {
                $tabelas = [];
                echo "<ul>";
                while ($row = pg_fetch_assoc($result)) {
                    $tabelas[] = $row['tablename'];
                    echo "<li>{$row['tablename']}</li>";
                }
                echo "</ul>";
                
                // Procura tabelas que parecem conter clientes/pessoas
                echo "<h3>Buscando Tabela de Clientes/Pessoas:</h3>";
                $tabelas_pessoa = [];
                foreach ($tabelas as $tabela) {
                    if (stripos($tabela, 'pessoa') !== false || 
                        stripos($tabela, 'cliente') !== false ||
                        stripos($tabela, 'glb_pessoa') !== false) {
                        $tabelas_pessoa[] = $tabela;
                    }
                }
                
                if (!empty($tabelas_pessoa)) {
                    echo "<p>Tabelas encontradas relacionadas a clientes/pessoas:</p>";
                    echo "<ul>";
                    foreach ($tabelas_pessoa as $tab) {
                        echo "<li><strong>$tab</strong> - <a href='?verificar=$cd_empresa&tabela=$tab'>Ver Estrutura</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p style='color:orange'>⚠️ Nenhuma tabela com nome 'pessoa' ou 'cliente' encontrada.</p>";
                    echo "<p>Listando primeiros 20 registros de algumas tabelas para identificar clientes...</p>";
                }
            }
            
            // Se solicitado, mostra estrutura de uma tabela específica
            if (isset($_GET['tabela'])) {
                $tabela = pg_escape_string($conn, $_GET['tabela']);
                
                echo "<hr><h3>Estrutura da Tabela: $tabela</h3>";
                
                // Mostra colunas
                $result = pg_query($conn, "SELECT column_name, data_type, character_maximum_length 
                                           FROM information_schema.columns 
                                           WHERE table_name = '$tabela' 
                                           ORDER BY ordinal_position");
                
                if ($result) {
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>Coluna</th><th>Tipo</th><th>Tamanho</th></tr>";
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['column_name']}</td>";
                        echo "<td>{$row['data_type']}</td>";
                        echo "<td>{$row['character_maximum_length']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                
                // Mostra primeiros 5 registros
                echo "<h4>Primeiros 5 Registros:</h4>";
                $result = pg_query($conn, "SELECT * FROM $tabela LIMIT 5");
                
                if ($result && pg_num_rows($result) > 0) {
                    echo "<pre>";
                    while ($row = pg_fetch_assoc($result)) {
                        print_r($row);
                        echo "\n---\n";
                    }
                    echo "</pre>";
                } else {
                    echo "<p>Nenhum registro encontrado ou erro ao consultar.</p>";
                }
                
                // Conta total de registros
                $result = pg_query($conn, "SELECT COUNT(*) as total FROM $tabela");
                if ($result) {
                    $row = pg_fetch_assoc($result);
                    echo "<p><strong>Total de registros:</strong> {$row['total']}</p>";
                }
            }
            
            pg_close($conn);
        } else {
            echo "<p style='color:red'>❌ Erro ao conectar ao banco!</p>";
            echo "<p>Verifique as credenciais: host={$empresa['ds_host']}, db={$empresa['ds_banco']}, user={$empresa['ds_usuario']}, port={$empresa['ds_porta']}</p>";
        }
    }
}
?>
