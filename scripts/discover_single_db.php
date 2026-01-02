<?php
// discover_single_db.php
// Uso: php scripts/discover_single_db.php --host=... --port=... --db=... --user=... --pass=...

function parseArg($name, $default = null) {
    global $argv;
    foreach ($argv as $arg) {
        if (strpos($arg, "--$name=") === 0) {
            return substr($arg, strlen("--$name="));
        }
    }
    return $default;
}

$host = parseArg('host', 'localhost');
$port = parseArg('port', '5432');
$db   = parseArg('db', 'postgres');
$user = parseArg('user', 'postgres');
$pass = parseArg('pass', '');

echo "Conectando: host={$host} port={$port} db={$db} user={$user}\n";

try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    fwrite(STDERR, "Erro ao conectar: " . $e->getMessage() . "\n");
    exit(1);
}

$report = [
    'host' => $host,
    'port' => $port,
    'db' => $db,
    'user' => $user,
    'connected' => true,
    'tables' => []
];

function utf8ize($mixed) {
    if (is_array($mixed)) {
        $res = [];
        foreach ($mixed as $k => $v) $res[$k] = utf8ize($v);
        return $res;
    } elseif (is_string($mixed)) {
        $isUtf8 = false;
        if (function_exists('mb_check_encoding')) {
            $isUtf8 = mb_check_encoding($mixed, 'UTF-8');
        } else {
            $isUtf8 = (bool) @preg_match('//u', $mixed);
        }
        if ($isUtf8) return $mixed;
        return utf8_encode($mixed);
    } else {
        return $mixed;
    }
}

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

$filename = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'schema_report_diaazze.json';
// Garantir codificação UTF-8 antes de serializar
$report = utf8ize($report);
$json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
// Imprimir JSON para debug e também salvar em arquivo
if ($json === false) {
    fwrite(STDERR, "Erro ao converter JSON: " . json_last_error_msg() . "\n");
} else {
    echo "--BEGIN-JSON--\n";
    echo $json . "\n";
    echo "--END-JSON--\n";
}

$written = @file_put_contents($filename, $json === false ? '' : $json);
if ($written === false) {
    fwrite(STDERR, "Falha ao escrever arquivo {$filename}\n");
} else {
    echo "Relatório salvo em: {$filename} ({$written} bytes)\n";
}

?>
