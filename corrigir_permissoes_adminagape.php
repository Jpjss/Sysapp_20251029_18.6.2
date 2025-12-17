<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Verificando permissões do usuário adminagape (CD=5) ===\n\n";

// Verificar permissões na tabela sysapp_config_user_empresas_interfaces
$permissoes = $db->fetchAll("SELECT * FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = 5");
echo "Permissões em sysapp_config_user_empresas_interfaces: " . count($permissoes) . "\n";
print_r($permissoes);

// Verificar na outra tabela
$permissoes2 = $db->fetchAll("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = 5");
echo "\nPermissões em sysapp_config_user_interfaces: " . count($permissoes2) . "\n";
print_r($permissoes2);

echo "\n=== Adicionando permissões em sysapp_config_user_interfaces ===\n";

// Interfaces disponíveis
$interfaces = ['admin', 'clientes', 'questionarios', 'usuarios', 'relatorios'];

foreach ($interfaces as $interface) {
    // Verificar se já existe
    $existe = $db->fetchOne("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = 5 AND nm_interface = '$interface'");
    
    if (!$existe) {
        $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) VALUES (5, '$interface')";
        $result = $db->query($sql);
        echo ($result ? "✓" : "✗") . " Inserido: $interface\n";
    } else {
        echo "- Já existe: $interface\n";
    }
}

echo "\n=== Verificando novamente ===\n";
$permissoes_final = $db->fetchAll("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = 5");
echo "Total de permissões: " . count($permissoes_final) . "\n";
print_r($permissoes_final);
