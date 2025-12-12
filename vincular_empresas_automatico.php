<?php
/**
 * Script para vincular automaticamente TODAS as empresas não vinculadas ao usuário logado
 * Executa sem confirmação
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

// Inicia conexão com banco sysapp
$db = Database::getInstance();
$db->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

$cd_usuario = Session::read('Questionarios.cd_usu');

if (!$cd_usuario) {
    die("ERRO: Usuário não está logado!");
}

// Busca empresas não vinculadas
$sqlNaoVinculadas = "SELECT cd_empresa, nm_empresa
                     FROM sysapp_config_empresas
                     WHERE cd_empresa NOT IN (
                         SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario
                     )
                     ORDER BY nm_empresa";
$empresasNaoVinculadas = $db->fetchAll($sqlNaoVinculadas);

if (empty($empresasNaoVinculadas)) {
    echo "OK: Todas as empresas já estão vinculadas!";
    exit;
}

// Busca interfaces disponíveis
$sqlInterfaces = "SELECT cd_interface FROM sysapp_interfaces";
$interfaces = $db->fetchAll($sqlInterfaces);

$empresasVinculadas = 0;
$permissoesCriadas = 0;

foreach ($empresasNaoVinculadas as $emp) {
    $cd_empresa = (int)$emp['cd_empresa'];
    
    // Vincula empresa ao usuário
    $sqlInsertEmpresa = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) 
                         VALUES ($cd_empresa, $cd_usuario)";
    
    if ($db->query($sqlInsertEmpresa)) {
        $empresasVinculadas++;
        
        // Dá permissões para esta empresa
        if ($interfaces) {
            foreach ($interfaces as $interface) {
                $cd_interface = (int)$interface['cd_interface'];
                
                $sqlInsertPermissao = "INSERT INTO sysapp_config_user_empresas_interfaces 
                                       (cd_empresa, cd_usuario, cd_interface) 
                                       VALUES ($cd_empresa, $cd_usuario, $cd_interface)";
                
                if ($db->query($sqlInsertPermissao)) {
                    $permissoesCriadas++;
                }
            }
        }
    }
}

echo "SUCESSO: $empresasVinculadas empresa(s) vinculada(s) com $permissoesCriadas permissão(ões)!";
?>
