<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sessão
session_start();

require_once 'config/core.php';
require_once 'config/database.php';
require_once 'models/Relatorio.php';

echo "=== TESTE: ENTRADA X VENDAS (CORRIGIDO) ===\n\n";

// Simular sessão
$_SESSION['Config'] = [
    'host' => 'banco.propasso.systec.ftp.sh',
    'port' => '5432',
    'database' => 'bd_propasso',
    'username' => 'usr_propasso',
    'password' => 'senhapropassosystec123'
];

$relatorio = new Relatorio();

// Filtros com período que tem dados (outubro/2025)
$filtros = [
    'venda_dt_inicio' => '2025-10-01',
    'venda_dt_fim' => '2025-10-07',
    'entrada_dt_inicio' => '2025-10-01',
    'entrada_dt_fim' => '2025-10-07',
    'filiais' => ['todas'],
    'est_positivo' => true,
    'est_zerado' => true,
    'est_negativo' => true
];

echo "Período: {$filtros['venda_dt_inicio']} a {$filtros['venda_dt_fim']}\n";
echo "Buscando dados...\n\n";

$resultado = $relatorio->getEntradaVendas($filtros);

echo "=== RESULTADO ===\n";
echo "Filiais encontradas: " . count($resultado['dados']) . "\n";
echo "\n";

if (!empty($resultado['dados'])) {
    // Mostrar primeiras 3 filiais
    $count = 0;
    foreach ($resultado['dados'] as $nmFilial => $marcas) {
        if ($count >= 3) break;
        echo "Filial: $nmFilial (" . count($marcas) . " marcas)\n";
        
        // Mostrar primeiras 3 marcas de cada filial
        $countMarcas = 0;
        foreach ($marcas as $marca) {
            if ($countMarcas >= 3) break;
            echo sprintf("  - %s: Estoque=%d, Vendas=%d, R$ %.2f\n",
                $marca['nm_marca'],
                $marca['estoque_atual'],
                $marca['qtde_vendida'],
                $marca['valor_vendido']
            );
            $countMarcas++;
        }
        echo "\n";
        $count++;
    }
}

echo "=== TOTAIS GERAIS ===\n";
echo sprintf("Estoque Atual: %d unidades\n", $resultado['totais']['estoque_atual']);
echo sprintf("Quantidade Vendida: %d unidades\n", $resultado['totais']['qtde_vendida']);
echo sprintf("Valor de Vendas: R$ %.2f\n", $resultado['totais']['valor_vendido']);
echo sprintf("Valor de Estoque: R$ %.2f\n", $resultado['totais']['valor_estoque']);
