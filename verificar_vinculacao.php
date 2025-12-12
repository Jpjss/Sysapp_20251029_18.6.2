<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

echo "=== EMPRESAS CADASTRADAS ===\n";
$empresas = $db->fetchAll('SELECT * FROM sysapp_config_empresas ORDER BY cd_empresa');
foreach ($empresas as $emp) {
    echo "ID {$emp['cd_empresa']}: {$emp['nm_empresa']} ({$emp['ds_banco']})\n";
}

echo "\n=== VINCULAÇÕES USUÁRIO-EMPRESA ===\n";
$vinculos = $db->fetchAll('SELECT ue.*, e.nm_empresa, u.nm_usu 
    FROM sysapp_config_user_empresas ue 
    LEFT JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
    LEFT JOIN sysapp_usuarios u ON ue.cd_usuario = u.cd_usu
    ORDER BY ue.cd_usuario, ue.cd_empresa');

if (empty($vinculos)) {
    echo "Nenhuma vinculação encontrada!\n";
} else {
    foreach ($vinculos as $v) {
        echo "Usuário {$v['cd_usuario']} ({$v['nm_usu']}) -> Empresa {$v['cd_empresa']} ({$v['nm_empresa']})\n";
    }
}

echo "\n=== EMPRESAS SEM VINCULAÇÃO ===\n";
$semVinculo = $db->fetchAll('SELECT e.* FROM sysapp_config_empresas e 
    WHERE NOT EXISTS (SELECT 1 FROM sysapp_config_user_empresas ue WHERE ue.cd_empresa = e.cd_empresa)');

if (empty($semVinculo)) {
    echo "Todas as empresas estão vinculadas!\n";
} else {
    foreach ($semVinculo as $emp) {
        echo "⚠️ Empresa {$emp['cd_empresa']} ({$emp['nm_empresa']}) NÃO está vinculada a nenhum usuário!\n";
    }
}
