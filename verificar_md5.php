<?php
$senhas = [
    'diaazze123',
    'admin',
    '123456'
];

echo "MD5 das senhas:\n\n";
foreach ($senhas as $senha) {
    echo "$senha => " . md5($senha) . "\n";
}

echo "\n\nVerificando no banco:\n\n";

require_once 'config/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT ds_login, ds_senha FROM sysapp_config_user ORDER BY cd_usuario";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "{$row['ds_login']}: {$row['ds_senha']}\n";
}
