<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Empresa.php';

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

$empresaModel = new Empresa($db);

echo "Teste do método getNextCodigo():\n";
$nextId = $empresaModel->getNextCodigo();
echo "Próximo ID disponível: $nextId\n\n";

echo "Empresas atuais no banco:\n";
$empresas = $db->fetchAll('SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas ORDER BY cd_empresa');
foreach ($empresas as $emp) {
    echo "ID {$emp['cd_empresa']}: {$emp['nm_empresa']}\n";
}
