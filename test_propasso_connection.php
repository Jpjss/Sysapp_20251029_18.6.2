<?php
/**
 * Teste de conexão com banco Propasso
 */

$host = 'banco.propasso.systec.ftp.sh';
$port = '5432';
$dbname = 'bd_propasso';
$user = 'admin';
$password = 'systec2011.';

echo "<h1>Teste de Conexão - Banco Propasso</h1>";
echo "<hr>";

echo "<h2>Credenciais:</h2>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Port:</strong> $port</li>";
echo "<li><strong>Database:</strong> $dbname</li>";
echo "<li><strong>User:</strong> $user</li>";
echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</li>";
echo "</ul>";

echo "<h2>Teste 1: Conexão Direta</h2>";
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
echo "<p>Connection String: <code>$conn_string</code></p>";

$conn = @pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

if (!$conn) {
    echo "<p style='color: red;'><strong>❌ ERRO:</strong> Não foi possível conectar!</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p><strong>Erro PHP:</strong> " . $error['message'] . "</p>";
    }
    echo "<p><strong>Verifique:</strong></p>";
    echo "<ul>";
    echo "<li>Se o host está acessível (firewall, rede)</li>";
    echo "<li>Se as credenciais estão corretas</li>";
    echo "<li>Se o PostgreSQL está rodando</li>";
    echo "<li>Se a extensão pgsql do PHP está habilitada</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green;'><strong>✅ SUCESSO:</strong> Conexão estabelecida!</p>";
    
    echo "<h2>Teste 2: Verificar Tabela glb_pessoa</h2>";
    $result = @pg_query($conn, "SELECT COUNT(*) as total FROM glb_pessoa LIMIT 1");
    if (!$result) {
        echo "<p style='color: red;'><strong>❌ ERRO:</strong> Tabela glb_pessoa não encontrada ou erro na consulta</p>";
        echo "<p><strong>Erro PostgreSQL:</strong> " . pg_last_error($conn) . "</p>";
    } else {
        echo "<p style='color: green;'><strong>✅ SUCESSO:</strong> Tabela glb_pessoa encontrada!</p>";
        $row = pg_fetch_assoc($result);
        echo "<p>Total de registros: " . $row['total'] . "</p>";
    }
    
    pg_close($conn);
}

echo "<hr>";
echo "<h2>Teste 3: Com strtolower() (como o código atual)</h2>";

$hostLower = strtolower($host);
$dbnameLower = strtolower($dbname);
$userLower = strtolower($user);

echo "<p><strong>Após strtolower():</strong></p>";
echo "<ul>";
echo "<li><strong>Host:</strong> $hostLower</li>";
echo "<li><strong>Database:</strong> $dbnameLower</li>";
echo "<li><strong>User:</strong> $userLower</li>";
echo "</ul>";

$conn_string_lower = "host=$hostLower port=$port dbname=$dbnameLower user=$userLower password=$password";
echo "<p>Connection String: <code>$conn_string_lower</code></p>";

$conn2 = @pg_connect($conn_string_lower, PGSQL_CONNECT_FORCE_NEW);

if (!$conn2) {
    echo "<p style='color: red;'><strong>❌ ERRO:</strong> Não foi possível conectar com strtolower()!</p>";
    echo "<p><strong>Isso confirma que o problema está no uso do strtolower() no código!</strong></p>";
} else {
    echo "<p style='color: green;'><strong>✅ SUCESSO:</strong> Conexão estabelecida com strtolower()!</p>";
    pg_close($conn2);
}
