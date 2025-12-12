<?php
/**
 * Setup do banco sysapp no servidor remoto Propasso
 */

echo "\n=== CONFIGURACAO DO SYSAPP NO SERVIDOR REMOTO ===\n\n";

$host = 'banco.propasso.systec.ftp.sh';
$port = '5432';
$user = 'admin';
$password = 'systec2011.';

echo "Conectando ao servidor remoto...\n";
echo "Host: $host\n";
echo "Porta: $port\n";
echo "Usuario: $user\n\n";

// Conecta ao banco postgres para criar o sysapp
$connString = "host=$host port=$port dbname=postgres user=$user password=$password";
$conn = @pg_connect($connString);

if (!$conn) {
    echo "ERRO: Nao foi possivel conectar ao banco postgres!\n";
    echo "Erro: " . error_get_last()['message'] . "\n";
    exit(1);
}

echo "Conectado ao banco postgres!\n\n";

// Verifica se banco sysapp existe
echo "Verificando se banco 'sysapp' existe...\n";
$result = pg_query($conn, "SELECT 1 FROM pg_database WHERE datname='sysapp'");
$exists = pg_num_rows($result) > 0;

if ($exists) {
    echo "Banco 'sysapp' ja existe!\n";
    echo "Deseja recriar as tabelas? (isso vai APAGAR dados existentes) (S/N): ";
    $resposta = trim(fgets(STDIN));
    
    if (strtoupper($resposta) !== 'S') {
        echo "Pulando criacao do banco...\n";
        $createTables = false;
    } else {
        $createTables = true;
    }
} else {
    echo "Banco 'sysapp' NAO existe\n";
    echo "Criando banco 'sysapp'...\n";
    
    if (!pg_query($conn, "CREATE DATABASE sysapp")) {
        echo "ERRO ao criar banco: " . pg_last_error($conn) . "\n";
        exit(1);
    }
    
    echo "Banco criado com sucesso!\n";
    $createTables = true;
}

pg_close($conn);

if ($createTables) {
    // Conecta ao banco sysapp
    echo "\nConectando ao banco sysapp...\n";
    $connString = "host=$host port=$port dbname=sysapp user=$user password=$password";
    $conn = @pg_connect($connString);

    if (!$conn) {
        echo "ERRO ao conectar ao banco sysapp!\n";
        echo "Erro: " . error_get_last()['message'] . "\n";
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
        // Não sai, pode ser que algumas tabelas já existam
        echo "Continuando...\n";
    } else {
        echo "Schema executado com sucesso!\n";
    }

    pg_close($conn);
}

// Atualiza config/database.php
echo "\nAtualizando config/database.php...\n";
$configPath = 'config/database.php';
$config = file_get_contents($configPath);

$config = preg_replace("/private \\\$host = '[^']*';/", "private \$host = '$host';", $config);
$config = preg_replace("/private \\\$port = '[^']*';/", "private \$port = '$port';", $config);
$config = preg_replace("/private \\\$database = '[^']*';/", "private \$database = 'sysapp';", $config);
$config = preg_replace("/private \\\$username = '[^']*';/", "private \$username = '$user';", $config);
$config = preg_replace("/private \\\$password = '[^']*';/", "private \$password = '$password';", $config);

file_put_contents($configPath, $config);

echo "Configuracao atualizada!\n\n";
echo "=== CONFIGURACAO CONCLUIDA COM SUCESSO ===\n\n";
echo "O sistema agora esta configurado para usar o banco remoto!\n";
echo "Banco do sistema: sysapp@$host\n";
echo "Agora voce pode cadastrar bancos de empresas (como bd_propasso) no sistema!\n";
