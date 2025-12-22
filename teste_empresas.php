<?php
define('BASE_PATH', dirname(__FILE__));

require BASE_PATH . '/config/database.php';

$pdo = new PDO('pgsql:host=banco.propasso.systec.ftp.sh;dbname=bd_propasso;port=5432', 'admin', 'systec2011.');
$result = $pdo->query('SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas');
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Empresas encontradas: " . count($rows) . PHP_EOL;
foreach ($rows as $row) {
    echo "- CD: " . $row['cd_empresa'] . " | Nome: " . $row['nm_empresa'] . PHP_EOL;
}
