<?php
/**
 * Debug do banco de dados do cliente
 * Testa conexão e mostra estrutura real das tabelas
 */

// Credenciais do banco do cliente (da imagem)
$host = '168.138.144.4';
$port = '5432';
$dbname = 'bd_agape_israel';
$user = 'admin';
$password = 'systec';

echo "<h1>Debug - Banco do Cliente: $dbname</h1>";
echo "<hr>";

// Teste 1: Conexão
echo "<h2>1. Testando Conexão</h2>";
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = @pg_connect($conn_string);

if (!$conn) {
    echo "<p style='color: red;'>❌ ERRO: Não foi possível conectar!</p>";
    echo "<p>Erro: " . pg_last_error() . "</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexão estabelecida com sucesso!</p>";

// Teste 2: Verificar se existe tabela glb_pessoa
echo "<h2>2. Verificando Tabela glb_pessoa</h2>";
$result = @pg_query($conn, "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'glb_pessoa') as existe");
if ($result) {
    $row = pg_fetch_assoc($result);
    if ($row['existe'] === 't') {
        echo "<p style='color: green;'>✅ Tabela glb_pessoa existe!</p>";
        
        // Teste 3: Listar todas as colunas da tabela glb_pessoa
        echo "<h2>3. Colunas da Tabela glb_pessoa</h2>";
        $sql = "SELECT column_name, data_type, character_maximum_length, is_nullable 
                FROM information_schema.columns 
                WHERE table_name = 'glb_pessoa' 
                ORDER BY ordinal_position";
        
        $colunas = pg_query($conn, $sql);
        if ($colunas) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Coluna</th><th>Tipo</th><th>Tamanho</th><th>Nullable</th></tr>";
            
            $lista_colunas = [];
            while ($col = pg_fetch_assoc($colunas)) {
                $lista_colunas[] = $col['column_name'];
                echo "<tr>";
                echo "<td><strong>{$col['column_name']}</strong></td>";
                echo "<td>{$col['data_type']}</td>";
                echo "<td>" . ($col['character_maximum_length'] ?? '-') . "</td>";
                echo "<td>" . ($col['is_nullable'] === 'YES' ? 'Sim' : 'Não') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h3>Resumo das Colunas Importantes:</h3>";
            echo "<ul>";
            
            // Verificar colunas comuns
            $colunas_importantes = [
                'cd_pessoa' => 'Código do Cliente',
                'nm_pessoa' => 'Nome Completo',
                'nm_fant' => 'Nome Fantasia',
                'nm_razao' => 'Razão Social',
                'cpf_cgc' => 'CPF/CNPJ (Propasso)',
                'nr_cpf_cnpj' => 'CPF/CNPJ (Teste)',
                'ds_endereco' => 'Endereço',
                'ds_bairro' => 'Bairro',
                'ds_cidade' => 'Cidade',
                'ds_uf' => 'UF',
                'fone' => 'Telefone',
                'email' => 'Email',
                'ds_email' => 'Email',
                'dt_nascimento' => 'Data Nascimento',
                'dt_nasc' => 'Data Nascimento'
            ];
            
            foreach ($colunas_importantes as $coluna => $descricao) {
                if (in_array($coluna, $lista_colunas)) {
                    echo "<li style='color: green;'>✅ <strong>$coluna</strong> - $descricao</li>";
                } else {
                    echo "<li style='color: orange;'>⚠️ <strong>$coluna</strong> - $descricao (NÃO EXISTE)</li>";
                }
            }
            echo "</ul>";
        }
        
        // Teste 4: Contar registros
        echo "<h2>4. Contagem de Registros</h2>";
        $count_result = pg_query($conn, "SELECT COUNT(*) as total FROM glb_pessoa");
        if ($count_result) {
            $count_row = pg_fetch_assoc($count_result);
            echo "<p>Total de clientes cadastrados: <strong>{$count_row['total']}</strong></p>";
            
            // Teste 5: Mostrar primeiros 5 registros
            if ($count_row['total'] > 0) {
                echo "<h2>5. Primeiros 5 Clientes (amostra)</h2>";
                
                // Constrói query SELECT dinâmica com as colunas que existem
                $select_cols = [];
                foreach (['cd_pessoa', 'nm_pessoa', 'nm_fant', 'cpf_cgc', 'nr_cpf_cnpj', 'fone', 'email', 'ds_email'] as $col) {
                    if (in_array($col, $lista_colunas)) {
                        $select_cols[] = $col;
                    }
                }
                
                $sql_sample = "SELECT " . implode(', ', $select_cols) . " FROM glb_pessoa LIMIT 5";
                $sample_result = pg_query($conn, $sql_sample);
                
                if ($sample_result) {
                    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                    echo "<tr>";
                    foreach ($select_cols as $col) {
                        echo "<th>$col</th>";
                    }
                    echo "</tr>";
                    
                    while ($row = pg_fetch_assoc($sample_result)) {
                        echo "<tr>";
                        foreach ($select_cols as $col) {
                            echo "<td>" . htmlspecialchars($row[$col] ?? '-') . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Tabela glb_pessoa NÃO existe neste banco!</p>";
        
        // Listar tabelas disponíveis
        echo "<h3>Tabelas Disponíveis no Banco:</h3>";
        $tables_result = pg_query($conn, "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
        if ($tables_result) {
            echo "<ul>";
            while ($table = pg_fetch_assoc($tables_result)) {
                echo "<li>{$table['tablename']}</li>";
            }
            echo "</ul>";
        }
    }
}

// Teste 6: Outras tabelas relacionadas
echo "<h2>6. Outras Tabelas Relacionadas</h2>";
$tabelas_relacionadas = ['glb_pessoa_fone', 'glb_pessoa_telefone', 'glb_pessoa_endereco', 'glb_pessoa_obs_contato', 'ped_vd', 'rc_lanc_cpl'];

foreach ($tabelas_relacionadas as $tabela) {
    $result = pg_query($conn, "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '$tabela') as existe");
    if ($result) {
        $row = pg_fetch_assoc($result);
        if ($row['existe'] === 't') {
            echo "<p style='color: green;'>✅ Tabela <strong>$tabela</strong> existe</p>";
            
            // Contar registros
            $count = pg_query($conn, "SELECT COUNT(*) as total FROM $tabela");
            if ($count) {
                $count_row = pg_fetch_assoc($count);
                echo "<p style='margin-left: 20px;'>Total de registros: {$count_row['total']}</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Tabela <strong>$tabela</strong> não existe</p>";
        }
    }
}

pg_close($conn);

echo "<hr>";
echo "<p><strong>Diagnóstico Completo!</strong></p>";
?>
