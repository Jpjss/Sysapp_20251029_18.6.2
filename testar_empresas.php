<?php
require_once 'config/database.php';
require_once 'models/Empresa.php';

$Empresa = new Empresa();

echo "Testando listarTodas():\n\n";
$empresas = $Empresa->listarTodas();

if (empty($empresas)) {
    echo "❌ NENHUMA EMPRESA ENCONTRADA!\n";
} else {
    echo "✅ Empresas encontradas: " . count($empresas) . "\n\n";
    foreach ($empresas as $emp) {
        echo "ID: {$emp['cd_empresa']} - {$emp['nm_empresa']}\n";
        echo "  Host: {$emp['ds_host']}\n";
        echo "  DB: {$emp['ds_banco']}\n\n";
    }
}

echo "\nTestando count():\n";
echo "Total: " . $Empresa->count() . "\n";
echo "Ativas: " . $Empresa->countAtivas() . "\n";
