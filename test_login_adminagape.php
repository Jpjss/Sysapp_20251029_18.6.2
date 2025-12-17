<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');

echo "=== Testando usuário adminagape ===\n\n";

// Buscar usuário
$sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email, ds_senha 
        FROM sysapp_config_user 
        WHERE ds_login = 'agape@sys.io' OR ds_email = 'agape@sys.io'";

$usuario = $db->fetchOne($sql);

if ($usuario) {
    echo "Usuário encontrado:\n";
    echo "  CD: {$usuario['cd_usuario']}\n";
    echo "  Nome: {$usuario['nm_usuario']}\n";
    echo "  Login: {$usuario['ds_login']}\n";
    echo "  Email: {$usuario['ds_email']}\n";
    echo "  Hash da senha: {$usuario['ds_senha']}\n\n";
    
    // Testar senhas comuns
    $senhas_teste = ['123', '123456', 'agape', 'admin', 'teste123'];
    
    foreach ($senhas_teste as $senha) {
        $verifica = password_verify($senha, $usuario['ds_senha']);
        echo "  password_verify('$senha', hash): " . ($verifica ? 'MATCH ✓' : 'não') . "\n";
    }
    
    // Verificar empresas vinculadas
    echo "\n=== Empresas vinculadas ===\n";
    $empresas = $db->fetchAll("SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = {$usuario['cd_usuario']}");
    echo "Total: " . count($empresas) . "\n";
    print_r($empresas);
    
    // Verificar permissões
    echo "\n=== Permissões (interfaces) ===\n";
    $permissoes = $db->fetchAll("SELECT * FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = {$usuario['cd_usuario']}");
    echo "Total: " . count($permissoes) . "\n";
    print_r($permissoes);
    
} else {
    echo "❌ Usuário NÃO encontrado!\n";
}
