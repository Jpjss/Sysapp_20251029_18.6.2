<?php
$pdo = new PDO('pgsql:host=banco.propasso.systec.ftp.sh;dbname=bd_propasso;port=5432', 'admin', 'systec2011.');

// Verificar estrutura da tabela
$result = $pdo->query("SELECT * FROM information_schema.columns 
                       WHERE table_name='sysapp_config_empresas'
                       ORDER BY ordinal_position");
$columns = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Colunas da tabela sysapp_config_empresas:" . PHP_EOL;
foreach ($columns as $col) {
    echo "- " . $col['column_name'] . " (" . $col['data_type'] . ")" . PHP_EOL;
}

// Verificar dados
echo PHP_EOL . "Dados:" . PHP_EOL;
$result = $pdo->query("SELECT * FROM sysapp_config_empresas");
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    print_r($row);
}
