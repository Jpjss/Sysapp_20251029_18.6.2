<?php
define('BASE_PATH', dirname(__FILE__));

require BASE_PATH . '/config/database.php';
require BASE_PATH . '/models/Usuario.php';

$pdo = new PDO('pgsql:host=banco.propasso.systec.ftp.sh;dbname=bd_propasso;port=5432', 'admin', 'systec2011.');

// Vincula admin à empresa 1
$sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) VALUES (1, 1)
        ON CONFLICT DO NOTHING";
$result = $pdo->exec($sql);
echo "Resultado da vinculação: " . ($result !== false ? "OK" : "Erro") . PHP_EOL;

// Verifica vinculação
$result = $pdo->query("SELECT cd_usuario, cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = 1");
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Admin vinculado a:" . PHP_EOL;
foreach ($rows as $row) {
    echo "- Empresa: " . $row['cd_empresa'] . PHP_EOL;
}
