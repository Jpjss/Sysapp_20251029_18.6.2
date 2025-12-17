<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Atualizando senha do usuário adminagape (CD=5) ===\n\n";

$novaSenha = '12345'; // Senha que o usuário está tentando usar
$senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

echo "Nova senha: $novaSenha\n";
echo "Hash: $senhaHash\n\n";

// Atualiza senha
$senhaEscapada = $db->escape($senhaHash);
$sql = "UPDATE sysapp_config_user 
        SET ds_senha = '$senhaEscapada' 
        WHERE cd_usuario = 5";

$result = $db->query($sql);

if ($result) {
    echo "✓ Senha atualizada com sucesso!\n\n";
    
    // Verifica
    $usuario = $db->fetchOne("SELECT ds_senha FROM sysapp_config_user WHERE cd_usuario = 5");
    echo "Hash salvo no banco: {$usuario['ds_senha']}\n";
    
    $verifica = password_verify($novaSenha, $usuario['ds_senha']);
    echo "Verificação password_verify: " . ($verifica ? 'OK ✓' : 'FALHOU ✗') . "\n";
} else {
    echo "✗ Erro ao atualizar senha\n";
}
