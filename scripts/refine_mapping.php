<?php
// refine_mapping.php
// Refina o mapping_diaazze.json usando heurísticas (PKs, possíveis FKs)

$mapFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mapping_diaazze.json';
$schemaFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'schema_report_diaazze.json';
if (!file_exists($mapFile) || !file_exists($schemaFile)) {
    fwrite(STDERR, "Arquivos necessários não encontrados (mapping_diaazze.json, schema_report_diaazze.json).\n");
    exit(1);
}

$map = json_decode(file_get_contents($mapFile), true);
$schema = json_decode(file_get_contents($schemaFile), true);
$tables = $schema['tables'] ?? [];

function find_table_obj($tables, $name) {
    foreach ($tables as $t) if ($t['table'] === $name) return $t;
    return null;
}

// heurística: PK provável = coluna com nextval() em column_default ou coluna iniciada por cd_
function detect_pk($tableObj) {
    $candidates = [];
    foreach ($tableObj['columns'] as $col) {
        $cn = $col['column_name'];
        $def = $col['column_default'] ?? '';
        if (stripos($def, 'nextval(') !== false) return $cn;
        if (stripos($cn, 'cd_') === 0) $candidates[] = $cn;
    }
    return $candidates[0] ?? null;
}

// heurística simples para FK: column names containing cd_ + other table name piece
function detect_possible_fks($tableObj, $tables) {
    $res = [];
    $allNames = array_map(function($t){return $t['table'];}, $tables);
    foreach ($tableObj['columns'] as $col) {
        $cn = $col['column_name'];
        if (preg_match('/cd_([a-z0-9_]+)/i', $cn, $m)) {
            $part = $m[1];
            foreach ($allNames as $tn) {
                if (stripos($tn, $part) !== false && $tn !== $tableObj['table']) {
                    $res[$cn] = $tn;
                    break;
                }
            }
        }
    }
    return $res;
}

$refined = $map;
foreach ($map['mapping'] as $key => $info) {
    $table = $info['table'] ?? null;
    if (!$table) continue;
    $tobj = find_table_obj($tables, $table);
    if (!$tobj) continue;
    $pk = detect_pk($tobj);
    $fks = detect_possible_fks($tobj, $tables);
    $refined['mapping'][$key]['detected_pk'] = $pk;
    $refined['mapping'][$key]['possible_fks'] = $fks;
}

$outFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mapping_diaazze_refined.json';
file_put_contents($outFile, json_encode($refined, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Refinamento salvo em: {$outFile}\n";

?>
