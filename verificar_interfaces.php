<?php
/**
 * Script para verificar e popular tabela sysapp_interfaces
 */

require_once 'config/config.php';
require_once 'config/database.php';

// Inicia conexão com banco sysapp
$db = Database::getInstance();
$db->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

echo "<h2>🔍 Verificação da Tabela sysapp_interfaces</h2>";
echo "<hr>";

// 1. Verifica se a tabela existe
echo "<h3>1. Verificando se a tabela existe...</h3>";
$sqlCheck = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name = 'sysapp_interfaces'
) as existe";

$result = $db->fetchOne($sqlCheck);

if ($result['existe'] == 't') {
    echo "<p style='color: green;'>✅ Tabela <strong>sysapp_interfaces</strong> existe!</p>";
    
    // 2. Verifica estrutura da tabela
    echo "<h3>2. Estrutura da Tabela:</h3>";
    $sqlStructure = "SELECT column_name, data_type, is_nullable 
                     FROM information_schema.columns 
                     WHERE table_name = 'sysapp_interfaces' 
                     ORDER BY ordinal_position";
    $columns = $db->fetchAll($sqlStructure);
    
    if ($columns) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #ddd;'><th>Coluna</th><th>Tipo</th><th>Nullable</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . $col['column_name'] . "</td>";
            echo "<td>" . $col['data_type'] . "</td>";
            echo "<td>" . $col['is_nullable'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 3. Verifica dados existentes
    echo "<h3>3. Dados Existentes:</h3>";
    $sqlData = "SELECT * FROM sysapp_interfaces ORDER BY cd_interface";
    $interfaces = $db->fetchAll($sqlData);
    
    if ($interfaces) {
        echo "<p style='color: green;'>✅ Encontradas <strong>" . count($interfaces) . " interface(s)</strong>:</p>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #ddd;'><th>CD Interface</th><th>Nome Interface</th></tr>";
        foreach ($interfaces as $int) {
            echo "<tr>";
            echo "<td>" . $int['cd_interface'] . "</td>";
            echo "<td>" . htmlspecialchars($int['nm_interface']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ <strong>PROBLEMA ENCONTRADO:</strong> Tabela existe mas está VAZIA!</p>";
        echo "<h3>4. 🔧 SOLUÇÃO: Popular a Tabela</h3>";
        echo "<p>Vou inserir as interfaces padrão do sistema...</p>";
        
        $interfacesPadrao = [
            ['cd' => 1, 'nome' => 'Dashboard'],
            ['cd' => 2, 'nome' => 'Relatórios'],
            ['cd' => 3, 'nome' => 'Clientes'],
            ['cd' => 4, 'nome' => 'Questionários'],
            ['cd' => 5, 'nome' => 'Usuários'],
            ['cd' => 6, 'nome' => 'Configurações']
        ];
        
        $inseridos = 0;
        foreach ($interfacesPadrao as $interface) {
            $cd = $interface['cd'];
            $nome = $interface['nome'];
            
            $sqlInsert = "INSERT INTO sysapp_interfaces (cd_interface, nm_interface) 
                         VALUES ($cd, '$nome')";
            
            if ($db->query($sqlInsert)) {
                $inseridos++;
                echo "<p>✅ Interface <strong>$nome</strong> inserida com sucesso!</p>";
            } else {
                echo "<p>❌ Erro ao inserir interface <strong>$nome</strong></p>";
            }
        }
        
        if ($inseridos > 0) {
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
            echo "<h3>✅ SUCESSO!</h3>";
            echo "<p><strong>$inseridos interface(s)</strong> foram inseridas na tabela!</p>";
            echo "<p>Agora você pode criar usuários e atribuir permissões.</p>";
            echo "</div>";
        }
    }
    
} else {
    echo "<p style='color: red;'>❌ Tabela <strong>sysapp_interfaces</strong> NÃO existe!</p>";
    echo "<h3>3. 🔧 SOLUÇÃO: Criar a Tabela</h3>";
    
    $sqlCreate = "CREATE TABLE sysapp_interfaces (
        cd_interface INTEGER NOT NULL PRIMARY KEY,
        nm_interface VARCHAR(100) NOT NULL
    )";
    
    if ($db->query($sqlCreate)) {
        echo "<p style='color: green;'>✅ Tabela <strong>sysapp_interfaces</strong> criada com sucesso!</p>";
        
        echo "<p>Agora vou popular com as interfaces padrão...</p>";
        
        $interfacesPadrao = [
            ['cd' => 1, 'nome' => 'Dashboard'],
            ['cd' => 2, 'nome' => 'Relatórios'],
            ['cd' => 3, 'nome' => 'Clientes'],
            ['cd' => 4, 'nome' => 'Questionários'],
            ['cd' => 5, 'nome' => 'Usuários'],
            ['cd' => 6, 'nome' => 'Configurações']
        ];
        
        $inseridos = 0;
        foreach ($interfacesPadrao as $interface) {
            $cd = $interface['cd'];
            $nome = $interface['nome'];
            
            $sqlInsert = "INSERT INTO sysapp_interfaces (cd_interface, nm_interface) 
                         VALUES ($cd, '$nome')";
            
            if ($db->query($sqlInsert)) {
                $inseridos++;
                echo "<p>✅ Interface <strong>$nome</strong> inserida!</p>";
            }
        }
        
        if ($inseridos > 0) {
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
            echo "<h3>✅ TUDO PRONTO!</h3>";
            echo "<p>Tabela criada e populada com <strong>$inseridos interface(s)</strong>!</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar a tabela!</p>";
    }
}

echo "<hr>";
echo "<p><a href='usuarios/novo'>➕ Criar Novo Usuário</a> | <a href='relatorios/index'>📊 Dashboard</a></p>";
?>
