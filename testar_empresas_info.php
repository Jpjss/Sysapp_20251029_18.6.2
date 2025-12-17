<?php
require 'config/database.php';
require_once 'models/Usuario.php';

$Usuario = new Usuario();
$cd_usuario = 4;

echo "Testando getEmpresasInfo() para usuário 'teste' (ID: 4):\n\n";

// Busca empresas
$empresas = $Usuario->getEmpresas($cd_usuario);
echo "Empresas retornadas por getEmpresas():\n";
print_r($empresas);

$cd_empresas = [];
foreach ($empresas as $emp) {
    $cd_empresas[] = $emp['cd_empresa'];
}
$cd_empresas_str = implode(',', $cd_empresas);

echo "\nIDs das empresas: $cd_empresas_str\n\n";

// Tenta buscar info
echo "Chamando getEmpresasInfo($cd_usuario, '$cd_empresas_str'):\n\n";
$infoDb = $Usuario->getEmpresasInfo($cd_usuario, $cd_empresas_str);

if (empty($infoDb)) {
    echo "❌ VAZIO! Nenhuma info retornada!\n";
} else {
    echo "✅ Info retornada: " . count($infoDb) . " empresas\n\n";
    print_r($infoDb);
}
