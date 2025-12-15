<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect();

echo "Atualizando senha da empresa padrão...\n";

$sql = "UPDATE sysapp_config_empresas SET ds_senha = $1 WHERE cd_empresa = 1";
pg_query_params($db->getConnection(), $sql, ['postgres']);

echo "✓ Empresa atualizada!\n\n";

// Verifica
$sql = "SELECT cd_empresa, nm_empresa, ds_usuario, ds_senha FROM sysapp_config_empresas WHERE cd_empresa = 1";
$result = pg_query($db->getConnection(), $sql);
$row = pg_fetch_assoc($result);

echo "Empresa: {$row['nm_empresa']}\n";
echo "Usuário: {$row['ds_usuario']}\n";
echo "Senha: {$row['ds_senha']}\n";
