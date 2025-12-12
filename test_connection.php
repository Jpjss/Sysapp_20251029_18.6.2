<?php
/**
 * Teste de Conexão com PostgreSQL
 */

echo "<h1>Teste de Conexão - SysApp</h1>";

// Configurações
$host = 'localhost';
$port = '5432';
$database = 'sysapp';
$username = 'postgres';
$password = 'postgres'; // Ajuste conforme sua senha

echo "<h2>Testando extensões PHP:</h2>";
echo "<ul>";
echo "<li>PostgreSQL (pgsql): " . (extension_loaded('pgsql') ? '<strong style="color:green">✓ Instalado</strong>' : '<strong style="color:red">✗ NÃO instalado</strong>') . "</li>";
echo "<li>PDO PostgreSQL: " . (extension_loaded('pdo_pgsql') ? '<strong style="color:green">✓ Instalado</strong>' : '<strong style="color:red">✗ NÃO instalado</strong>') . "</li>";
echo "</ul>";

if (!extension_loaded('pgsql')) {
    echo "<p style='color:red'><strong>ERRO:</strong> A extensão pgsql não está instalada no PHP!</p>";
    echo "<p>O XAMPP já vem com PHP. Você precisa habilitar a extensão pgsql no php.ini</p>";
    exit;
}

echo "<h2>Testando conexão com banco de dados:</h2>";

$conn_string = "host=$host port=$port dbname=$database user=$username password=$password";

try {
    $conn = pg_connect($conn_string);
    
    if ($conn) {
        echo "<p style='color:green'><strong>✓ Conexão estabelecida com sucesso!</strong></p>";
        
        // Testar versão do PostgreSQL
        $result = pg_query($conn, "SELECT version();");
        $row = pg_fetch_row($result);
        echo "<p><strong>Versão do PostgreSQL:</strong><br>" . $row[0] . "</p>";
        
        // Listar tabelas
        echo "<h3>Tabelas no banco 'sysapp':</h3>";
        $result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name;");
        
        if (pg_num_rows($result) > 0) {
            echo "<ul>";
            while ($row = pg_fetch_row($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:orange'>Nenhuma tabela encontrada!</p>";
        }
        
        // Verificar usuário admin
        echo "<h3>Verificando usuário admin:</h3>";
        $result = pg_query($conn, "SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user WHERE ds_login='admin';");
        
        if (pg_num_rows($result) > 0) {
            $user = pg_fetch_assoc($result);
            echo "<p style='color:green'><strong>✓ Usuário admin encontrado!</strong></p>";
            echo "<ul>";
            echo "<li><strong>ID:</strong> " . $user['cd_usuario'] . "</li>";
            echo "<li><strong>Nome:</strong> " . $user['nm_usuario'] . "</li>";
            echo "<li><strong>Login:</strong> " . $user['ds_login'] . "</li>";
            echo "</ul>";
        } else {
            echo "<p style='color:red'>✗ Usuário admin NÃO encontrado!</p>";
        }
        
        pg_close($conn);
        
        echo "<hr>";
        echo "<h2 style='color:green'>✓ Sistema pronto para uso!</h2>";
        echo "<p><a href='/Sysapp_20251029_18.6.2' style='display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Acessar SysApp</a></p>";
        
    } else {
        echo "<p style='color:red'><strong>✗ Erro ao conectar ao banco de dados!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'><strong>✗ Erro:</strong> " . $e->getMessage() . "</p>";
}
?>
