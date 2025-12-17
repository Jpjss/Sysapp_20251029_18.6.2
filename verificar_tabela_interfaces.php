<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Verificando estrutura da tabela sysapp_interfaces ===\n\n";

// Lista tabelas
$tables = $db->fetchAll("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename LIKE '%interface%'");
echo "Tabelas com 'interface' no nome:\n";
print_r($tables);

// Verifica se a tabela existe
$exists = $db->fetchOne("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'sysapp_interfaces')");
echo "\nTabela sysapp_interfaces existe? " . ($exists['exists'] === 't' ? 'SIM' : 'NÃO') . "\n";

if ($exists['exists'] === 't') {
    // Mostra estrutura
    $columns = $db->fetchAll("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'sysapp_interfaces'");
    echo "\nColunas:\n";
    print_r($columns);
}
