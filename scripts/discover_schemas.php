<?php
/**
 * discover_schemas.php
 *
 * Conecta ao banco do sistema (sysapp), lê `sysapp_config_empresas` e
 * tenta conectar a cada BD de cliente para listar tabelas/colunas.
 * Gera um arquivo JSON por empresa: schema_report_{cd_empresa}.json
 *
 * Uso:
 * php scripts/discover_schemas.php --sys-dsn="pgsql:host=localhost;port=5432;dbname=sysapp" --sys-user=postgres --sys-pass=senha
 */

function parseArg($name, $default = null) {
    global $argv;
    foreach ($argv as $arg) {
        if (strpos($arg, "--$name=") === 0) {
            return substr($arg, strlen("--$name="));
        }
    }
    return $default;
}

$sysDsn = parseArg('sys-dsn', 'pgsql:host=localhost;port=5432;dbname=sysapp');
$sysUser = parseArg('sys-user', 'postgres');
$sysPass = parseArg('sys-pass', '');

// Se a senha não for passada como argumento, solicitar interativamente
if ($sysPass === null || $sysPass === '') {
    if (function_exists('sapi_windows_vt100_support') || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Em Windows não há modo simples de ocultar input portável, solicitar normalmente
        echo "Senha do usuário {$sysUser}: ";
        $sysPass = trim(fgets(STDIN));
    } else {
        // Em UNIX, tentar ocultar eco usando stty
        echo "Senha do usuário {$sysUser}: ";
        system('stty -echo');
        $sysPass = trim(fgets(STDIN));
        system('stty echo');
        echo PHP_EOL;
    }
}

echo "Conectando ao banco do sistema: $sysDsn\n";

try {
    $sysPdo = new PDO($sysDsn, $sysUser, $sysPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    fwrite(STDERR, "Erro ao conectar ao banco do sistema: " . $e->getMessage() . "\n");
    exit(1);
}

$sql = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, ds_senha, COALESCE(ds_porta,'5432') AS ds_porta FROM sysapp_config_empresas WHERE fg_ativo = 'S'";
$stmt = $sysPdo->query($sql);
$empresas = $stmt->fetchAll();

if (!$empresas) {
    echo "Nenhuma empresa ativa encontrada em sysapp_config_empresas.\n";
    exit(0);
}

foreach ($empresas as $emp) {
    $cd = $emp['cd_empresa'];
    $name = $emp['nm_empresa'];
    $host = $emp['ds_host'] ?: 'localhost';
    $db = $emp['ds_banco'];
    $user = $emp['ds_usuario'] ?: '';
    $pass = $emp['ds_senha'] ?: '';
    $port = $emp['ds_porta'] ?: '5432';

    echo "\nEmpresa [{$cd}] {$name} -> host={$host} db={$db} user={$user}\n";

    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    $report = [
        'cd_empresa' => $cd,
        'nm_empresa' => $name,
        'connected' => false,
        'error' => null,
        'tables' => []
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $report['connected'] = true;

        // listar schemas/tabelas (exclui sistemas)
        $tablesSql = "SELECT table_schema, table_name
                      FROM information_schema.tables
                      WHERE table_type = 'BASE TABLE'
                        AND table_schema NOT IN ('pg_catalog','information_schema')
                      ORDER BY table_schema, table_name";

        $tstmt = $pdo->query($tablesSql);
        $tables = $tstmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tables as $t) {
            $schema = $t['table_schema'];
            $table = $t['table_name'];

            $colsSql = "SELECT column_name, data_type, is_nullable, column_default
                        FROM information_schema.columns
                        WHERE table_schema = :schema AND table_name = :table
                        ORDER BY ordinal_position";
            $cstmt = $pdo->prepare($colsSql);
            $cstmt->execute([':schema' => $schema, ':table' => $table]);
            $cols = $cstmt->fetchAll(PDO::FETCH_ASSOC);

            $report['tables'][] = [
                'schema' => $schema,
                'table' => $table,
                'columns' => $cols
            ];
        }

    } catch (Exception $e) {
        $report['error'] = $e->getMessage();
        fwrite(STDERR, "Erro ao conectar/consultar {$dsn}: " . $e->getMessage() . "\n");
    }

    $filename = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "schema_report_{$cd}.json";
    file_put_contents($filename, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Relatório gerado: {$filename}\n";
}

echo "\nConcluído.\n";

?>
