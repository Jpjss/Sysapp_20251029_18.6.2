<?php
/**
 * Setup do banco de dados local SysApp
 */

echo "\n=== CONFIGURACAO DO BANCO LOCAL SYSAPP ===\n\n";

// Solicita credenciais
echo "Digite as credenciais do PostgreSQL LOCAL:\n";
echo "Host (pressione Enter para 'localhost'): ";
$host = trim(fgets(STDIN));
if (empty($host)) $host = 'localhost';

echo "Porta (pressione Enter para '5432'): ";
$port = trim(fgets(STDIN));
if (empty($port)) $port = '5432';

echo "Usuario (pressione Enter para 'postgres'): ";
$user = trim(fgets(STDIN));
if (empty($user)) $user = 'postgres';

echo "Senha do usuario $user: ";
$password = trim(fgets(STDIN));

echo "\nTestando conexao com PostgreSQL...\n";

// Testa conexão com banco postgres
$connString = "host=$host port=$port dbname=postgres user=$user password=$password";
$conn = @pg_connect($connString);

if (!$conn) {
    echo "ERRO: Nao foi possivel conectar!\n";
    echo "Erro: " . error_get_last()['message'] . "\n";
    echo "Verifique as credenciais e tente novamente.\n";
    exit(1);
}

echo "Conexao bem-sucedida!\n\n";

// Verifica se banco sysapp existe
echo "Verificando se banco 'sysapp' existe...\n";
$result = pg_query($conn, "SELECT 1 FROM pg_database WHERE datname='sysapp'");
$exists = pg_num_rows($result) > 0;

if ($exists) {
    echo "Banco 'sysapp' ja existe!\n";
} else {
    echo "Banco 'sysapp' NAO existe\n";
    echo "Deseja criar o banco 'sysapp'? (S/N): ";
    $resposta = trim(fgets(STDIN));
    
    if (strtoupper($resposta) !== 'S') {
        echo "Operacao cancelada\n";
        exit(1);
    }
    
    echo "Criando banco 'sysapp'...\n";
    if (!pg_query($conn, "CREATE DATABASE sysapp")) {
        echo "ERRO ao criar banco: " . pg_last_error($conn) . "\n";
        exit(1);
    }
    echo "Banco criado com sucesso!\n";
}

pg_close($conn);

// Conecta ao banco sysapp
echo "\nConectando ao banco sysapp...\n";
$connString = "host=$host port=$port dbname=sysapp user=$user password=$password";
$conn = @pg_connect($connString);

if (!$conn) {
    echo "ERRO ao conectar ao banco sysapp!\n";
    exit(1);
}

echo "Conectado!\n\n";

// Executa schema
echo "Executando schema do banco...\n";
$schema = file_get_contents('database_schema.sql');

if (!$schema) {
    echo "ERRO: Nao foi possivel ler database_schema.sql\n";
    exit(1);
}

// Executa o schema
if (!pg_query($conn, $schema)) {
    echo "ERRO ao executar schema: " . pg_last_error($conn) . "\n";
    exit(1);
}

echo "Schema executado com sucesso!\n\n";

pg_close($conn);

// Atualiza config/database.php
echo "Atualizando config/database.php...\n";
$configPath = 'config/database.php';
$config = file_get_contents($configPath);

$config = preg_replace("/private \\\$host = '[^']*';/", "private \$host = '$host';", $config);
$config = preg_replace("/private \\\$port = '[^']*';/", "private \$port = '$port';", $config);
$config = preg_replace("/private \\\$username = '[^']*';/", "private \$username = '$user';", $config);
$config = preg_replace("/private \\\$password = '[^']*';/", "private \$password = '$password';", $config);

file_put_contents($configPath, $config);

echo "Configuracao atualizada!\n\n";
echo "=== CONFIGURACAO CONCLUIDA COM SUCESSO ===\n\n";
echo "Agora voce pode cadastrar bancos de empresas no sistema!\n";
