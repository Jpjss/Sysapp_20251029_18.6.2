<?php
/**
 * Lista todas as tabelas do Propasso e verifica quais têm dados
 */

require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', 5432);

echo "=== TABELAS COM DADOS NO PROPASSO ===\n\n";

// Lista todas as tabelas
$sql = "SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename";

$tabelas = $db->fetchAll($sql);

$tabelasComDados = [];

foreach ($tabelas as $tabela) {
    $nome = $tabela['tablename'];
    
    // Ignora tabelas do sistema
    if (strpos($nome, 'pg_') === 0 || strpos($nome, 'sql_') === 0) {
        continue;
    }
    
    // Conta registros
    $count = $db->fetchOne("SELECT COUNT(*) as total FROM \"$nome\"");
    $total = $count ? (int)$count['total'] : 0;
    
    if ($total > 0) {
        $tabelasComDados[] = [
            'nome' => $nome,
            'total' => $total
        ];
    }
}

// Ordena por número de registros (decrescente)
usort($tabelasComDados, function($a, $b) {
    return $b['total'] - $a['total'];
});

echo "Tabelas com dados (ordenadas por quantidade):\n\n";
foreach ($tabelasComDados as $tab) {
    printf("%-50s %s registros\n", $tab['nome'], number_format($tab['total'], 0, ',', '.'));
}

echo "\n\n=== VERIFICANDO TABELAS RELEVANTES ===\n\n";

// Verifica algumas tabelas específicas
$tabelasRelevantes = [
    'glb_pessoa',
    'glb_questionario',
    'glb_questionario_resposta',
    'rc_lanc', // Pode ter dados de compras
    'rc_lanc_cpl', // Detalhes de compras
    'vw_rc_lanc_cpl_detalhes', // View de detalhes
];

foreach ($tabelasRelevantes as $nome) {
    echo "--- $nome ---\n";
    
    $count = $db->fetchOne("SELECT COUNT(*) as total FROM \"$nome\"");
    $total = $count ? (int)$count['total'] : 0;
    
    echo "Total: $total registros\n";
    
    if ($total > 0 && $total <= 5) {
        $registros = $db->fetchAll("SELECT * FROM \"$nome\" LIMIT 3");
        if (!empty($registros)) {
            echo "Campos: " . implode(', ', array_keys($registros[0])) . "\n";
        }
    } elseif ($total > 0) {
        $registro = $db->fetchOne("SELECT * FROM \"$nome\" LIMIT 1");
        if ($registro) {
            echo "Campos: " . implode(', ', array_keys($registro)) . "\n";
        }
    }
    
    echo "\n";
}

echo "=== FIM ===\n";
