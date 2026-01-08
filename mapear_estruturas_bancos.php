<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$bancos = [
    ['host' => 'banco.propasso.systec.ftp.sh', 'port' => '5432', 'database' => 'bd_propasso', 'username' => 'usr_propasso', 'password' => 'senhapropassosystec123', 'nome' => 'ProPasso'],
    ['host' => 'banco.diaazze.systec.ftp.sh', 'port' => '5432', 'database' => 'bd_diaazze', 'username' => 'usr_diaazze', 'password' => 'senhadiaazzesystec123', 'nome' => 'DiaAzze'],
    ['host' => 'banco.agape.systec.ftp.sh', 'port' => '5432', 'database' => 'bd_agape', 'username' => 'usr_agape', 'password' => 'senhaagapesystec123', 'nome' => 'Agape'],
    ['host' => 'banco.drill.systec.ftp.sh', 'port' => '5432', 'database' => 'bd_drill', 'username' => 'usr_drill', 'password' => 'senhadrillsystec123', 'nome' => 'Drill']
];

echo "=== MAPEAMENTO DE ESTRUTURAS DOS BANCOS ===\n\n";

$estruturas = [];

foreach ($bancos as $config) {
    echo "Banco: {$config['nome']} ({$config['database']})\n";
    echo str_repeat("-", 80) . "\n";
    
    $connString = "host={$config['host']} port={$config['port']} dbname={$config['database']} user={$config['username']} password={$config['password']}";
    $conn = @pg_connect($connString);
    
    if (!$conn) {
        echo "❌ Não foi possível conectar\n\n";
        continue;
    }
    
    echo "✅ Conectado\n";
    
    $estrutura = [
        'nome' => $config['nome'],
        'database' => $config['database'],
        'tabelas' => []
    ];
    
    // Verificar tabelas principais
    $tabelasParaVerificar = [
        'dm_orcamento_vendas_consolidadas',
        'dm_orcamento_vendas_consolidadas_cubo',
        'dm_produto',
        'dm_estoque_atual',
        'ped_vd',
        'ped_vd_produto_cpl_tamanho',
        'est_produto',
        'est_produto_cpl_tamanho',
        'glb_pessoa',
        'prc_filial'
    ];
    
    foreach ($tabelasParaVerificar as $tabela) {
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = '$tabela'
        )";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_row($result);
        $existe = $row[0] === 't';
        
        if ($existe) {
            // Contar registros
            $sqlCount = "SELECT COUNT(*) FROM $tabela";
            $resultCount = @pg_query($conn, $sqlCount);
            $count = $resultCount ? pg_fetch_row($resultCount)[0] : 0;
            
            $estrutura['tabelas'][$tabela] = [
                'existe' => true,
                'registros' => $count
            ];
            
            echo "  ✅ $tabela ($count registros)\n";
        } else {
            $estrutura['tabelas'][$tabela] = ['existe' => false];
        }
    }
    
    $estruturas[] = $estrutura;
    
    pg_close($conn);
    echo "\n";
}

// Salvar estruturas em arquivo JSON
file_put_contents('database_structures.json', json_encode($estruturas, JSON_PRETTY_PRINT));

echo "\n=== RESUMO ===\n";
echo "✅ Estruturas salvas em database_structures.json\n";

// Criar recomendações
echo "\n=== RECOMENDAÇÕES ===\n";
foreach ($estruturas as $est) {
    echo "{$est['nome']}:\n";
    
    $temNova = isset($est['tabelas']['dm_orcamento_vendas_consolidadas']) && 
               $est['tabelas']['dm_orcamento_vendas_consolidadas']['existe'];
    $temAntiga = isset($est['tabelas']['ped_vd']) && 
                 $est['tabelas']['ped_vd']['existe'];
    
    if ($temNova) {
        echo "  → Usar estrutura NOVA (dm_*)\n";
    } elseif ($temAntiga) {
        echo "  → Usar estrutura ANTIGA (ped_vd, est_produto)\n";
    } else {
        echo "  → ⚠️ Nenhuma estrutura conhecida encontrada\n";
    }
    echo "\n";
}
