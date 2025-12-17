<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Corrigindo usuário diaazze ===\n\n";

// Buscar usuário
$usuario = $db->fetchOne("SELECT * FROM sysapp_config_user WHERE ds_login = 'diaazze@sys.io' OR ds_email = 'diaazze@sys.io'");

if (!$usuario) {
    echo "❌ Usuário não encontrado!\n";
    exit;
}

$cd_usuario = $usuario['cd_usuario'];
echo "CD Usuário: $cd_usuario\n";
echo "Nome: {$usuario['nm_usuario']}\n";
echo "Login: {$usuario['ds_login']}\n\n";

// 1. Atualizar senha para password_hash
echo "=== 1. Atualizando senha ===\n";
$novaSenha = '123';
$senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
$senhaEscapada = $db->escape($senhaHash);

$sql = "UPDATE sysapp_config_user SET ds_senha = '$senhaEscapada' WHERE cd_usuario = $cd_usuario";
$db->query($sql);
echo "✓ Senha atualizada para: $novaSenha\n\n";

// 2. Adicionar permissões na tabela sysapp_config_user_interfaces
echo "=== 2. Adicionando permissões ===\n";
$interfaces = ['admin', 'clientes', 'questionarios', 'usuarios', 'relatorios'];

foreach ($interfaces as $interface) {
    $existe = $db->fetchOne("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario AND nm_interface = '$interface'");
    
    if (!$existe) {
        $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) VALUES ($cd_usuario, '$interface')";
        $result = $db->query($sql);
        echo ($result ? "✓" : "✗") . " $interface\n";
    } else {
        echo "- $interface (já existe)\n";
    }
}

echo "\n=== Verificação final ===\n";
$permissoes = $db->fetchAll("SELECT * FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario");
echo "Total de permissões: " . count($permissoes) . "\n";

$verifica_senha = $db->fetchOne("SELECT ds_senha FROM sysapp_config_user WHERE cd_usuario = $cd_usuario");
$teste = password_verify('123', $verifica_senha['ds_senha']);
echo "Senha '123' válida: " . ($teste ? "✓ SIM" : "✗ NÃO") . "\n";

echo "\n✅ Usuário corrigido! Use: diaazze@sys.io / 123\n";
