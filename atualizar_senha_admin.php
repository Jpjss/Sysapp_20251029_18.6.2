<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Atualizando senha do usuário admin (CD=1) ===\n\n";

$novaSenha = 'admin';
$senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

echo "Senha: $novaSenha\n";
echo "Hash: $senhaHash\n\n";

$senhaEscapada = $db->escape($senhaHash);
$sql = "UPDATE sysapp_config_user SET ds_senha = '$senhaEscapada' WHERE cd_usuario = 1";

$result = $db->query($sql);

if ($result) {
    echo "✓ Senha atualizada com sucesso!\n\n";
    
    $usuario = $db->fetchOne("SELECT ds_senha FROM sysapp_config_user WHERE cd_usuario = 1");
    echo "Hash salvo: {$usuario['ds_senha']}\n";
    
    $verifica = password_verify($novaSenha, $usuario['ds_senha']);
    echo "Verificação: " . ($verifica ? 'OK ✓' : 'FALHOU ✗') . "\n";
} else {
    echo "✗ Erro ao atualizar\n";
}
