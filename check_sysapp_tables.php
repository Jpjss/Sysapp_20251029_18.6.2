<?php
/**
 * Verifica tabelas do sistema SysApp
 */

require_once 'config/database.php';

$db = Database::getInstance();

echo "<h1>Verificação de Tabelas - SysApp</h1>";
echo "<hr>";

// Verifica se a tabela sysapp_config_empresas existe
echo "<h2>1. Verificando tabela sysapp_config_empresas</h2>";

$sql = "SELECT EXISTS (
    SELECT FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name = 'sysapp_config_empresas'
)";

$result = $db->fetchOne($sql);

if ($result && $result['exists'] === 't') {
    echo "<p style='color: green;'>✅ Tabela existe!</p>";
    
    // Mostra estrutura da tabela
    echo "<h3>Estrutura da tabela:</h3>";
    $sqlColunas = "SELECT column_name, data_type, character_maximum_length, is_nullable, column_default
                   FROM information_schema.columns 
                   WHERE table_name = 'sysapp_config_empresas'
                   ORDER BY ordinal_position";
    
    $colunas = $db->fetchAll($sqlColunas);
    
    if ($colunas) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #333; color: white;'>";
        echo "<th>Coluna</th><th>Tipo</th><th>Tamanho</th><th>Nulo?</th><th>Padrão</th>";
        echo "</tr>";
        
        foreach ($colunas as $col) {
            echo "<tr>";
            echo "<td><strong>" . $col['column_name'] . "</strong></td>";
            echo "<td>" . $col['data_type'] . "</td>";
            echo "<td>" . ($col['character_maximum_length'] ?? '-') . "</td>";
            echo "<td>" . $col['is_nullable'] . "</td>";
            echo "<td>" . ($col['column_default'] ?? '-') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    // Mostra registros existentes
    echo "<h3>Registros existentes:</h3>";
    $sqlRegistros = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, ds_porta 
                     FROM sysapp_config_empresas 
                     ORDER BY cd_empresa";
    
    $registros = $db->fetchAll($sqlRegistros);
    
    if ($registros) {
        echo "<p>Total: " . count($registros) . " empresa(s)</p>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #333; color: white;'>";
        echo "<th>CD</th><th>Nome</th><th>Host</th><th>Banco</th><th>Usuário</th><th>Porta</th>";
        echo "</tr>";
        
        foreach ($registros as $reg) {
            echo "<tr>";
            echo "<td>" . $reg['cd_empresa'] . "</td>";
            echo "<td>" . $reg['nm_empresa'] . "</td>";
            echo "<td>" . $reg['ds_host'] . "</td>";
            echo "<td>" . $reg['ds_banco'] . "</td>";
            echo "<td>" . $reg['ds_usuario'] . "</td>";
            echo "<td>" . $reg['ds_porta'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>Nenhum registro encontrado.</p>";
    }
    
    // Próximo código
    echo "<h3>Próximo código disponível:</h3>";
    $sqlNext = "SELECT COALESCE(MAX(cd_empresa), 0) + 1 as proximo FROM sysapp_config_empresas";
    $next = $db->fetchOne($sqlNext);
    echo "<p style='font-size: 20px; color: blue;'><strong>" . $next['proximo'] . "</strong></p>";
    
} else {
    echo "<p style='color: red;'>❌ Tabela NÃO existe!</p>";
    echo "<p><strong>A tabela precisa ser criada. Execute o script database_schema.sql</strong></p>";
}

echo "<hr>";
echo "<h2>2. Banco de Dados Conectado</h2>";
echo "<p><strong>Banco:</strong> " . $db->getDatabase() . "</p>";
