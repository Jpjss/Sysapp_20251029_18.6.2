<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão PostgreSQL</h1>";

// Teste 1: Verificar se extensão pgsql está carregada
echo "<h2>1. Extensão PostgreSQL</h2>";
if (extension_loaded('pgsql')) {
    echo "✅ Extensão pgsql está carregada<br>";
} else {
    echo "❌ Extensão pgsql NÃO está carregada<br>";
}

// Teste 2: Tentar conectar ao banco
echo "<h2>2. Teste de Conexão</h2>";
$host = 'localhost';
$port = '5432';
$dbname = 'sysapp';
$user = 'postgres';
$password = 'systec';

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
echo "String de conexão: $conn_string<br><br>";

$conn = @pg_connect($conn_string);

if ($conn) {
    echo "✅ <strong>Conexão bem-sucedida!</strong><br><br>";
    
    // Teste 3: Verificar tabelas
    echo "<h2>3. Tabelas no banco 'sysapp'</h2>";
    $result = pg_query($conn, "SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
    
    if ($result) {
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            echo "<li>" . $row['tablename'] . "</li>";
        }
        echo "</ul>";
    }
    
    // Teste 4: Buscar usuário admin
    echo "<h2>4. Buscar usuário 'admin' na tabela sysapp_config_user</h2>";
    $result = pg_query($conn, "SELECT * FROM sysapp_config_user WHERE LOWER(ds_login) = 'admin' OR LOWER(nm_usuario) = 'admin' LIMIT 1");
    
    if ($result) {
        $user_data = pg_fetch_assoc($result);
        if ($user_data) {
            echo "<strong>✅ Usuário encontrado!</strong><pre>";
            print_r($user_data);
            echo "</pre>";
        } else {
            echo "❌ Usuário 'admin' não encontrado<br>";
            echo "<br><strong>Listando todos os usuários:</strong><br>";
            $result2 = pg_query($conn, "SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user LIMIT 5");
            if ($result2) {
                echo "<pre>";
                while ($row = pg_fetch_assoc($result2)) {
                    print_r($row);
                }
                echo "</pre>";
            }
        }
    } else {
        echo "❌ Erro ao executar query: " . pg_last_error($conn) . "<br>";
    }
    
    // Teste 5: Verificar se existe view vw_login
    echo "<h2>5. Views disponíveis</h2>";
    $result = pg_query($conn, "SELECT viewname FROM pg_views WHERE schemaname = 'public' ORDER BY viewname");
    if ($result) {
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            echo "<li>" . $row['viewname'] . "</li>";
        }
        echo "</ul>";
    }
    
    pg_close($conn);
} else {
    echo "❌ <strong>Erro ao conectar:</strong><br>";
    echo pg_last_error() . "<br>";
}
?>
