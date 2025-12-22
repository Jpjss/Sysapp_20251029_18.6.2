<?php
define('BASE_PATH', dirname(__FILE__));

require BASE_PATH . '/config/database.php';

$pdo = new PDO('pgsql:host=banco.propasso.systec.ftp.sh;dbname=bd_propasso;port=5432', 'admin', 'systec2011.');

// Verifica empresa do admin
$result = $pdo->query("SELECT cu.cd_usuario, cu.nm_usuario, cue.cd_empresa 
                       FROM sysapp_config_user cu
                       LEFT JOIN sysapp_config_user_empresas cue ON cu.cd_usuario = cue.cd_usuario
                       WHERE cu.nm_usuario = 'admin'");
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Admin - Vinculação com empresas:" . PHP_EOL;
foreach ($rows as $row) {
    if ($row['cd_empresa']) {
        echo "- CD: " . $row['cd_usuario'] . " | Nome: " . $row['nm_usuario'] . " | Empresa: " . $row['cd_empresa'] . PHP_EOL;
    } else {
        echo "- CD: " . $row['cd_usuario'] . " | Nome: " . $row['nm_usuario'] . " | Empresa: NÃO VINCULADO" . PHP_EOL;
    }
}
