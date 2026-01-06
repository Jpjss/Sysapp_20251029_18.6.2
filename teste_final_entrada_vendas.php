<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar sessão
$_SESSION['Config'] = [
    'host' => 'banco.propasso.systec.ftp.sh',
    'port' => '5432',
    'database' => 'bd_propasso',
    'username' => 'usr_propasso',
    'password' => 'senhapropassosystec123'
];

require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'models/Relatorio.php';

echo "=== TESTE FINAL: getEntradaVendas() ===\n\n";

$relatorio = new Relatorio();

$filtros = [
    'venda_dt_inicio' => '2025-10-01',
    'venda_dt_fim' => '2025-10-07',
    'entrada_dt_inicio' => '2025-10-01',
    'entrada_dt_fim' => '2025-10-07',
    'filiais' => ['todas'],
    'est_positivo' => true,
    'est_zerado' => false,
    'est_negativo' => false
];

echo "Chamando getEntradaVendas()...\n";
$resultado = $relatorio->getEntradaVendas($filtros);

echo "\n✅ RESULTADO:\n";
echo "Filiais: " . count($resultado['dados']) . "\n";

if (!empty($resultado['dados'])) {
    $totalMarcas = 0;
    foreach ($resultado['dados'] as $filial => $marcas) {
        $totalMarcas += count($marcas);
    }
    echo "Total de marcas: $totalMarcas\n\n";
    
    // Mostrar primeira filial
    foreach ($resultado['dados'] as $filial => $marcas) {
        echo "Filial: $filial (" . count($marcas) . " marcas)\n";
        $count = 0;
        foreach ($marcas as $marca) {
            if ($count++ >= 5) break;
            echo sprintf("  - %s: Vendas=%d, R$ %.2f\n",
                $marca['nm_marca'],
                $marca['qtde_vendida'],
                $marca['valor_vendido']
            );
        }
        break;
    }
    
    echo "\nTOTAIS:\n";
    echo sprintf("  Vendas: %d unidades\n", $resultado['totais']['qtde_vendida']);
    echo sprintf("  Valor: R$ %.2f\n", $resultado['totais']['valor_vendido']);
} else {
    echo "❌ VAZIO!\n";
}
