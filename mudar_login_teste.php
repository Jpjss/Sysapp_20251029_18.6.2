<?php
require 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== ALTERANDO LOGIN DO USUÁRIO TESTE ===\n\n";

// Muda o login do usuário ID 4 para algo único
$result = pg_query($conn, "UPDATE sysapp_config_user 
                            SET ds_login = 'testeusuario', 
                                ds_email = 'testeusuario@sys.io' 
                            WHERE cd_usuario = 4");

if ($result) {
    echo "✅ Usuário ID 4 atualizado com sucesso!\n\n";
    echo "📝 NOVOS DADOS DE LOGIN:\n";
    echo "   Login: testeusuario\n";
    echo "   Email: testeusuario@sys.io\n";
    echo "   Senha: teste123\n\n";
    echo "🔑 PARA FAZER LOGIN USE:\n";
    echo "   Usuário: testeusuario\n";
    echo "   Senha: teste123\n\n";
    
    // Verifica
    $check = pg_query($conn, "SELECT cd_usuario, ds_login, ds_email FROM sysapp_config_user WHERE cd_usuario = 4");
    $user = pg_fetch_assoc($check);
    
    echo "✓ Confirmação:\n";
    echo "   ID: {$user['cd_usuario']}\n";
    echo "   Login: {$user['ds_login']}\n";
    echo "   Email: {$user['ds_email']}\n";
} else {
    echo "❌ Erro ao atualizar: " . pg_last_error($conn) . "\n";
}
