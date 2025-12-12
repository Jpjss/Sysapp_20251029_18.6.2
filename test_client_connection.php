<?php
/**
 * Teste de conexão e listagem de clientes do banco do cliente
 */

// Define BASE_PATH
define('BASE_PATH', __DIR__);

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/DatabaseHelper.php';
require_once 'models/Cliente.php';

Session::start();

echo "<h1>Teste de Conexão - Banco Cliente</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #4f46e5; color: white; }
    tr:hover { background: #f9f9f9; }
    .card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>";

// Credenciais do banco do cliente
$host = '168.138.144.4';
$port = '5432';
$dbname = 'bd_agape_israel';
$user = 'admin';
$password = 'systec';

echo "<div class='card'>";
echo "<h2>1. Conectando ao Banco</h2>";
echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Database:</strong> $dbname</p>";
echo "<p><strong>Port:</strong> $port</p>";

// Conecta ao banco do cliente
$db = Database::getInstance();
$conn = $db->connect($host, $dbname, $user, $password, $port);

if (!$conn) {
    echo "<p class='error'>❌ Falha na conexão!</p>";
    exit;
}

echo "<p class='success'>✅ Conectado com sucesso!</p>";
echo "</div>";

// Cria instância do DatabaseHelper e Cliente
$helper = new DatabaseHelper();
$cliente = new Cliente();

// Verifica estrutura da tabela glb_pessoa
echo "<div class='card'>";
echo "<h2>2. Estrutura da Tabela glb_pessoa</h2>";

if (!$helper->tableExists('glb_pessoa')) {
    echo "<p class='error'>❌ Tabela glb_pessoa não existe neste banco!</p>";
    exit;
}

echo "<p class='success'>✅ Tabela glb_pessoa encontrada!</p>";

$colunas = $helper->getTableColumns('glb_pessoa');
echo "<p><strong>Colunas encontradas:</strong> " . count($colunas) . "</p>";

echo "<table>";
echo "<tr><th>Nome da Coluna</th><th>Tipo</th><th>Nullable</th></tr>";
foreach ($colunas as $nome => $info) {
    echo "<tr>";
    echo "<td><strong>$nome</strong></td>";
    echo "<td>{$info['type']}</td>";
    echo "<td>" . ($info['nullable'] ? 'Sim' : 'Não') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Testa contagem de clientes
echo "<div class='card'>";
echo "<h2>3. Contagem de Clientes</h2>";

try {
    $total = $cliente->count();
    echo "<p class='success'>✅ Total de clientes: <strong>$total</strong></p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao contar clientes: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Lista primeiros 10 clientes
echo "<div class='card'>";
echo "<h2>4. Primeiros 10 Clientes</h2>";

try {
    $clientes = $cliente->listar(10, 0);
    
    if (empty($clientes)) {
        echo "<p class='warning'>⚠️ Nenhum cliente encontrado</p>";
    } else {
        echo "<p class='success'>✅ {$total} clientes encontrados</p>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Código</th>";
        echo "<th>Nome Fantasia</th>";
        echo "<th>Razão Social</th>";
        echo "<th>CPF/CNPJ</th>";
        echo "<th>Cidade</th>";
        echo "</tr>";
        
        foreach ($clientes as $cli) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($cli['cd_pessoa'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($cli['nm_fant'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($cli['nm_razao'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($cli['cpf_cnpj'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($cli['cidade'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao listar clientes: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div>";

// Testa busca com filtro
echo "<div class='card'>";
echo "<h2>5. Teste de Busca</h2>";

try {
    $resultado = $cliente->listar(5, 0, 'JOSE');
    echo "<p class='success'>✅ Busca por 'JOSE': " . count($resultado) . " resultados</p>";
    
    if (!empty($resultado)) {
        echo "<ul>";
        foreach ($resultado as $cli) {
            echo "<li>" . htmlspecialchars($cli['nm_fant'] ?? $cli['nm_razao'] ?? '-') . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro na busca: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Detecta tipo de estrutura
echo "<div class='card'>";
echo "<h2>6. Tipo de Estrutura do Banco</h2>";

$estrutura = $helper->detectDatabaseStructure('glb_pessoa');
echo "<p><strong>Estrutura detectada:</strong> " . strtoupper($estrutura) . "</p>";

$mapping = DatabaseHelper::getClientColumnMapping();
echo "<h3>Mapeamento de Colunas Aplicado:</h3>";
echo "<table>";
echo "<tr><th>Campo Esperado</th><th>Opções de Colunas</th><th>Coluna Usada</th><th>Status</th></tr>";

foreach ($mapping as $campo => $opcoes) {
    $colunaUsada = $helper->getAvailableColumn('glb_pessoa', $opcoes);
    $status = $colunaUsada ? "<span class='success'>✅ Disponível</span>" : "<span class='error'>❌ Não encontrado</span>";
    
    echo "<tr>";
    echo "<td><strong>$campo</strong></td>";
    echo "<td>" . implode(', ', $opcoes) . "</td>";
    echo "<td>" . ($colunaUsada ?? '-') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

echo "<div class='card'>";
echo "<h2>✅ Teste Completo!</h2>";
echo "<p>O sistema agora está preparado para trabalhar com qualquer estrutura de banco de dados.</p>";
echo "<p>As queries são construídas dinamicamente baseadas nas colunas disponíveis.</p>";
echo "</div>";
?>
