<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Usuario.php';

$db = new Database();
$conn = $db->getConnection();

echo "=== Verificando Usuário ID 4 ===\n";
$stmt = $conn->prepare("SELECT cd_usuario, nm_usuario, ds_login, ds_email FROM t_usuario WHERE cd_usuario = 4");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($user);

echo "\n=== Testando Usuario::findByLogin('testeusuario') ===\n";
$usuarioModel = new Usuario();
$found = $usuarioModel->findByLogin('testeusuario');
if ($found) {
    echo "Encontrado via Model: " . $found['nm_usuario'] . " (ID: " . $found['cd_usuario'] . ")\n";
} else {
    echo "NÃO encontrado via Model.\n";
}
