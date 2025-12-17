<?php
require 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== TODOS OS USUÁRIOS NO BANCO ===\n\n";

$result = pg_query($conn, "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha, fg_ativo 
                            FROM sysapp_config_user 
                            ORDER BY cd_usuario");

while ($u = pg_fetch_assoc($result)) {
    echo "ID: {$u['cd_usuario']}\n";
    echo "  Nome: {$u['nm_usuario']}\n";
    echo "  Login: {$u['ds_login']}\n";
    echo "  Email: {$u['ds_email']}\n";
    echo "  Senha: " . substr($u['ds_senha'], 0, 30) . "...\n";
    echo "  Ativo: {$u['fg_ativo']}\n";
    echo "---\n";
}

echo "\n=== PROBLEMA IDENTIFICADO ===\n\n";

echo "Quando você digita 'teste' no login, o método findByLogin busca em 3 campos:\n";
echo "  1. ds_email\n";
echo "  2. ds_login\n";
echo "  3. nm_usuario (NOME do usuário)\n\n";

echo "Se existe um usuário com NOME = 'Teste' (ID 2), ele será encontrado ANTES\n";
echo "do usuário com LOGIN = 'teste' (ID 4)!\n\n";

echo "SOLUÇÃO: Use o EMAIL completo para fazer login: teste@sys.io\n";
echo "OU: Mude a ordem da busca para priorizar ds_login e ds_email antes de nm_usuario\n";
