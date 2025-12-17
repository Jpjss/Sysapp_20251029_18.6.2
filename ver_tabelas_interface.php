<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

$tabelas = ['sysapp_config_user_interfaces', 'sysapp_config_user_empresas_interfaces', 'sysapp_controle_interface'];

foreach ($tabelas as $tabela) {
    echo "\n=== Tabela: $tabela ===\n";
    
    // Estrutura
    $columns = $db->fetchAll("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$tabela'");
    echo "Colunas:\n";
    foreach ($columns as $col) {
        echo "  - {$col['column_name']} ({$col['data_type']})\n";
    }
    
    // Dados
    $count = $db->fetchOne("SELECT COUNT(*) as total FROM $tabela");
    echo "Total de registros: {$count['total']}\n";
    
    if ($count['total'] > 0 && $count['total'] <= 10) {
        $dados = $db->fetchAll("SELECT * FROM $tabela LIMIT 10");
        echo "Dados:\n";
        print_r($dados);
    }
}
