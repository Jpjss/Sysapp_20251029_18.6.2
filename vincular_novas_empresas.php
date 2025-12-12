<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

echo "=== Verificando tabela sysapp_config_user_empresas ===\n";
$result = $db->fetchAll('SELECT * FROM sysapp_config_user_empresas');
print_r($result);

echo "\n=== Verificando tabela sysapp_usuarios ===\n";
$usuarios = $db->fetchAll('SELECT cd_usu, nm_usu FROM sysapp_usuarios');
print_r($usuarios);

// Vincular empresas 4 e 5 ao usuário 1 (Administrador)
echo "\n=== VINCULANDO EMPRESAS AO ADMINISTRADOR ===\n";

$cd_usuario = 1;
$empresas_vincular = [4, 5]; // Teste Empresa Nova e Agape

foreach ($empresas_vincular as $cd_empresa) {
    // Verifica se já existe vinculação
    $existe = $db->fetchOne("SELECT 1 FROM sysapp_config_user_empresas WHERE cd_empresa = $cd_empresa AND cd_usuario = $cd_usuario");
    
    if (!$existe) {
        $sql = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) VALUES ($cd_empresa, $cd_usuario)";
        $result = $db->query($sql);
        if ($result) {
            echo "✅ Empresa $cd_empresa vinculada ao usuário $cd_usuario\n";
            
            // Também dá permissões nas interfaces
            $interfaces = $db->fetchAll("SELECT cd_interface FROM sysapp_interfaces");
            foreach ($interfaces as $interface) {
                $cd_interface = (int)$interface['cd_interface'];
                $existePermissao = $db->fetchOne("SELECT 1 FROM sysapp_config_user_empresas_interfaces WHERE cd_empresa = $cd_empresa AND cd_usuario = $cd_usuario AND cd_interface = $cd_interface");
                if (!$existePermissao) {
                    $db->query("INSERT INTO sysapp_config_user_empresas_interfaces (cd_empresa, cd_usuario, cd_interface) VALUES ($cd_empresa, $cd_usuario, $cd_interface)");
                }
            }
            echo "   → Permissões de interfaces concedidas!\n";
        } else {
            echo "❌ Falha ao vincular empresa $cd_empresa\n";
        }
    } else {
        echo "⏭️ Empresa $cd_empresa já está vinculada ao usuário $cd_usuario\n";
    }
}

echo "\n=== VERIFICAÇÃO FINAL ===\n";
$vinculos = $db->fetchAll('SELECT * FROM sysapp_config_user_empresas ORDER BY cd_usuario, cd_empresa');
print_r($vinculos);
