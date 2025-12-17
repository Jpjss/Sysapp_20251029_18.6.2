<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Inserindo Interfaces no banco bd_propasso ===\n\n";

$interfaces = [
    ['cd' => 1, 'nome' => 'Dashboard'],
    ['cd' => 2, 'nome' => 'Relatórios'],
    ['cd' => 3, 'nome' => 'Clientes'],
    ['cd' => 4, 'nome' => 'Questionários'],
    ['cd' => 5, 'nome' => 'Usuários'],
    ['cd' => 6, 'nome' => 'Configurações']
];

foreach ($interfaces as $interface) {
    $cd = $interface['cd'];
    $nome = pg_escape_string($db->getConnection(), $interface['nome']);
    
    // Verifica se já existe
    $check = $db->fetchOne("SELECT cd_interface FROM sysapp_interfaces WHERE cd_interface = $cd");
    
    if ($check) {
        echo "[$cd] {$interface['nome']} - JÁ EXISTE\n";
    } else {
        $sql = "INSERT INTO sysapp_interfaces (cd_interface, nm_interface) VALUES ($cd, '$nome')";
        if ($db->query($sql)) {
            echo "[$cd] {$interface['nome']} - INSERIDO ✓\n";
        } else {
            echo "[$cd] {$interface['nome']} - ERRO ✗\n";
        }
    }
}

echo "\n=== Verificando total ===\n";
$result = $db->fetchAll("SELECT * FROM sysapp_interfaces ORDER BY cd_interface");
echo "Total de interfaces: " . count($result) . "\n";
foreach ($result as $r) {
    echo "  - [{$r['cd_interface']}] {$r['nm_interface']}\n";
}
