<?php
/**
 * Cria tabelas do sysapp dentro do bd_propasso
 */

echo "\n=== CRIANDO TABELAS SYSAPP NO BD_PROPASSO ===\n\n";

$host = 'banco.propasso.systec.ftp.sh';
$port = '5432';
$database = 'bd_propasso';
$user = 'admin';
$password = 'systec2011.';

echo "Conectando ao bd_propasso...\n";

$connString = "host=$host port=$port dbname=$database user=$user password=$password";
$conn = @pg_connect($connString);

if (!$conn) {
    echo "ERRO ao conectar!\n";
    echo "Erro: " . error_get_last()['message'] . "\n";
    exit(1);
}

echo "Conectado com sucesso!\n\n";

// Executa schema
echo "Executando schema do sysapp...\n";
$schema = file_get_contents('database_schema.sql');

if (!$schema) {
    echo "ERRO: Nao foi possivel ler database_schema.sql\n";
    exit(1);
}

// Executa o schema
$result = @pg_query($conn, $schema);

if (!$result) {
    $error = pg_last_error($conn);
    // Verifica se é erro de tabela já existente
    if (strpos($error, 'already exists') !== false) {
        echo "Tabelas ja existem! Continuando...\n";
    } else {
        echo "ERRO ao executar schema: $error\n";
        echo "Tentando continuar...\n";
    }
} else {
    echo "Schema executado com sucesso!\n";
}

// Verifica se as tabelas foram criadas
echo "\nVerificando tabelas criadas...\n";
$checkTables = pg_query($conn, "
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public' 
    AND table_name LIKE 'sysapp_%'
    ORDER BY table_name
");

if ($checkTables && pg_num_rows($checkTables) > 0) {
    echo "Tabelas do sysapp encontradas:\n";
    while ($row = pg_fetch_assoc($checkTables)) {
        echo "  - " . $row['table_name'] . "\n";
    }
} else {
    echo "AVISO: Nenhuma tabela sysapp encontrada!\n";
}

pg_close($conn);

// Atualiza config/database.php
echo "\nAtualizando config/database.php...\n";
$configPath = 'config/database.php';
$config = file_get_contents($configPath);

$config = preg_replace("/private \\\$host = '[^']*';/", "private \$host = '$host';", $config);
$config = preg_replace("/private \\\$port = '[^']*';/", "private \$port = '$port';", $config);
$config = preg_replace("/private \\\$database = '[^']*';/", "private \$database = '$database';", $config);
$config = preg_replace("/private \\\$username = '[^']*';/", "private \$username = '$user';", $config);
$config = preg_replace("/private \\\$password = '[^']*';/", "private \$password = '$password';", $config);

file_put_contents($configPath, $config);

echo "Configuracao atualizada!\n\n";
echo "=== CONFIGURACAO CONCLUIDA ===\n\n";
echo "O sistema esta configurado para usar:\n";
echo "Banco: $database@$host\n";
echo "As tabelas do sysapp estao dentro do bd_propasso\n";
echo "\nAgora reinicie o servidor PHP e tente cadastrar empresas!\n";
