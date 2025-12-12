<?php
/**
 * Forçar seleção de empresa que funciona
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

// Verifica se usuário está logado
if (!Session::check('Questionarios.cd_usu')) {
    die('ERRO: Usuário não logado. <a href="/">Fazer login</a>');
}

$db = Database::getInstance();

// FORÇA SELEÇÃO DA EMPRESA PROPASSO (código 3)
$empresa = $db->fetchOne("SELECT * FROM sysapp_config_empresas WHERE cd_empresa = 3");

if ($empresa) {
    $senha_decrypt = Security::decrypt($empresa['senha_banco']);
    
    Session::write('Config.database', $empresa['nome_banco']);
    Session::write('Config.databasename', $empresa['nome_banco']);
    Session::write('Config.host', $empresa['hostname_banco']);
    Session::write('Config.user', $empresa['usuario_banco']);
    Session::write('Config.password', $senha_decrypt);
    Session::write('Config.porta', $empresa['porta_banco']);
    Session::write('Config.empresa', $empresa['nome_empresa']);
    
    // Reconecta ao banco
    $result = $db->connect(
        $empresa['hostname_banco'],
        $empresa['nome_banco'],
        $empresa['usuario_banco'],
        $senha_decrypt,
        $empresa['porta_banco']
    );
    
    if ($result) {
        echo "<h1 style='color: green;'>✅ Empresa Propasso selecionada com sucesso!</h1>";
        echo "<p><a href='relatorios/index' style='padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Ir para Dashboard</a></p>";
    } else {
        echo "<h1 style='color: red;'>❌ ERRO ao conectar!</h1>";
        echo "<p>Verifique as credenciais.</p>";
    }
} else {
    echo "❌ Empresa Propasso não encontrada!";
}
?>
