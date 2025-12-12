<?php
/**
 * Selecionar Empresa Rapidamente
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

// Verifica se usuário está logado
if (!Session::check('Questionarios.cd_usu')) {
    die('ERRO: Usuário não logado. <a href="usuarios/login">Fazer login</a>');
}

$cd_usuario = Session::read('Questionarios.cd_usu');
$db = Database::getInstance();

// Pega código da empresa via GET
$cd_empresa = isset($_GET['cd']) ? (int)$_GET['cd'] : 0;

if ($cd_empresa > 0) {
    // Busca dados da empresa
    $sql = "SELECT ce.cd_empresa, ce.nome_empresa, ce.hostname_banco, ce.nome_banco, 
                   ce.usuario_banco, ce.senha_banco, ce.porta_banco
            FROM sysapp_config_empresas ce
            INNER JOIN sysapp_config_user_empresas cue ON ce.cd_empresa = cue.cd_empresa
            WHERE cue.cd_usuario = $cd_usuario AND ce.cd_empresa = $cd_empresa";
    
    $empresa = $db->fetchOne($sql);
    
    if ($empresa) {
        // Configura empresa na sessão
        $senha_decrypt = Security::decrypt($empresa['senha_banco']);
        
        Session::write('Config.database', $empresa['nome_banco']);
        Session::write('Config.databasename', $empresa['nome_banco']);
        Session::write('Config.host', $empresa['hostname_banco']);
        Session::write('Config.user', $empresa['usuario_banco']);
        Session::write('Config.password', $senha_decrypt);
        Session::write('Config.porta', $empresa['porta_banco']);
        Session::write('Config.empresa', $empresa['nome_empresa']);
        
        // Reconecta ao banco da empresa
        $result = $db->connect(
            $empresa['hostname_banco'],
            $empresa['nome_banco'],
            $empresa['usuario_banco'],
            $senha_decrypt,
            $empresa['porta_banco']
        );
        
        if ($result) {
            echo "✅ Empresa <strong>{$empresa['nome_empresa']}</strong> selecionada com sucesso!<br>";
            echo "<a href='relatorios/index'>Ir para Dashboard</a>";
        } else {
            echo "❌ ERRO ao conectar ao banco da empresa {$empresa['nome_empresa']}!<br>";
            echo "Verifique as credenciais no banco de dados.";
        }
    } else {
        echo "❌ Empresa não encontrada ou você não tem permissão!";
    }
} else {
    // Lista empresas disponíveis
    echo "<h1>Selecionar Empresa</h1>";
    echo "<p>Clique na empresa desejada:</p>";
    echo "<ul style='list-style: none; padding: 0;'>";
    
    $empresas = $db->fetchAll("
        SELECT ce.cd_empresa, ce.nome_empresa, ce.hostname_banco, ce.nome_banco
        FROM sysapp_config_empresas ce
        INNER JOIN sysapp_config_user_empresas cue ON ce.cd_empresa = cue.cd_empresa
        WHERE cue.cd_usuario = $cd_usuario
        ORDER BY ce.nome_empresa
    ");
    
    foreach ($empresas as $emp) {
        echo "<li style='margin: 10px 0;'>";
        echo "<a href='selecionar_empresa.php?cd={$emp['cd_empresa']}' style='display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>";
        echo "🏢 {$emp['nome_empresa']} ({$emp['nome_banco']})";
        echo "</a>";
        echo "</li>";
    }
    
    echo "</ul>";
}
?>
