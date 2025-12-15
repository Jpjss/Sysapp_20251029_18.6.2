<?php
require 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Views relacionadas a login:\n\n";

$sql = "SELECT table_name FROM information_schema.views WHERE table_schema='public' AND table_name LIKE '%login%'";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "- " . $row['table_name'] . "\n";
}

echo "\n\nVerificando se vw_login existe:\n";

$sql = "SELECT viewname FROM pg_views WHERE viewname = 'vw_login'";
$result = pg_query($conn, $sql);
if (pg_num_rows($result) > 0) {
    echo "✅ vw_login existe!\n\n";
    
    echo "Definição da view:\n";
    $sql = "SELECT pg_get_viewdef('vw_login', true)";
    $result = pg_query($conn, $sql);
    $def = pg_fetch_row($result)[0];
    echo $def . "\n";
    
    echo "\n\nDados do usuário 2 via vw_login:\n";
    $sql = "SELECT * FROM vw_login WHERE cd_usuario = 2";
    $result = pg_query($conn, $sql);
    $row = pg_fetch_assoc($result);
    print_r($row);
} else {
    echo "❌ vw_login NÃO existe!\n";
}
