<?php
require_once 'config/database.php';
require_once 'models/Empresa.php';

$Empresa = new Empresa();

echo "=== TESTANDO Empresa->listarTodas() ===\n\n";

$empresas = $Empresa->listarTodas();

if (empty($empresas)) {
    echo "❌ NENHUMA EMPRESA ATIVA\n";
} else {
    echo "Total: " . count($empresas) . " empresas\n\n";
    
    foreach ($empresas as $emp) {
        echo "CD: " . $emp['cd_empresa'] . "\n";
        echo "nm_empresa: '" . ($emp['nm_empresa'] ?? 'NULL') . "'\n";
        echo "ds_host: '" . ($emp['ds_host'] ?? 'NULL') . "'\n";
        echo "ds_banco: '" . ($emp['ds_banco'] ?? 'NULL') . "'\n";
        echo "---\n";
    }
}
