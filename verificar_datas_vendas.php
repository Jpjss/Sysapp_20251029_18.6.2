<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->connect();

echo "=== VERIFICANDO DATAS DAS VENDAS ===\n\n";

// 1. Ver o range de datas que existem
echo "1. Range de datas em dm_orcamento_vendas_consolidadas:\n";
$sql = "SELECT 
    MIN(dt_emi_pedido) as primeira_data,
    MAX(dt_emi_pedido) as ultima_data,
    COUNT(*) as total
FROM dm_orcamento_vendas_consolidadas";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Primeira data: {$row['primeira_data']}\n";
echo "Última data: {$row['ultima_data']}\n";
echo "Total de registros: {$row['total']}\n";

// 2. Ver vendas por dia (últimos 10 dias)
echo "\n2. Vendas por dia (últimos 10 dias):\n";
$sql = "SELECT 
    DATE(dt_emi_pedido) as data,
    COUNT(*) as total
FROM dm_orcamento_vendas_consolidadas
GROUP BY DATE(dt_emi_pedido)
ORDER BY data DESC
LIMIT 10";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "  {$row['data']}: {$row['total']} vendas\n";
}

// 3. Testar com intervalo diferente
echo "\n3. Testando vendas em janeiro/2025:\n";
$sql = "SELECT COUNT(*) as total 
FROM dm_orcamento_vendas_consolidadas 
WHERE dt_emi_pedido >= '2025-01-01'::date 
AND dt_emi_pedido < '2025-02-01'::date";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total em jan/2025: {$row['total']}\n";

// 4. Testar vendas em 2026
echo "\n4. Vendas em 2026:\n";
$sql = "SELECT COUNT(*) as total 
FROM dm_orcamento_vendas_consolidadas 
WHERE dt_emi_pedido >= '2026-01-01'::date";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
echo "Total em 2026: {$row['total']}\n";

// 5. Ver se a VIEW está OK
echo "\n5. Testando a VIEW dm_orcamento_vendas_consolidadas:\n";
$sql = "SELECT * FROM dm_orcamento_vendas_consolidadas LIMIT 3";
$result = pg_query($conn, $sql);
if ($result && pg_num_rows($result) > 0) {
    echo "✅ VIEW retorna dados\n";
    $row = pg_fetch_assoc($result);
    echo "Exemplo de registro:\n";
    echo "  - cd_pedido: {$row['cd_pedido']}\n";
    echo "  - dt_emi_pedido: {$row['dt_emi_pedido']}\n";
    echo "  - nm_cliente: {$row['nm_cliente']}\n";
} else {
    echo "❌ VIEW não retorna dados!\n";
}

pg_close($conn);
