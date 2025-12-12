<?php
/**
 * Remove TODAS as empresas do sistema
 * USE COM CUIDADO!
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

// Verifica se usuário está logado
if (!Session::check('Questionarios.cd_usu')) {
    die('ERRO: Usuário não logado. <a href="/">Fazer login</a>');
}

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Limpar Empresas</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #dc3545; }
.warning { background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; }
.btn-danger { background: #dc3545; color: white; border: none; padding: 15px 30px; cursor: pointer; border-radius: 5px; font-weight: bold; font-size: 16px; }
.btn-danger:hover { background: #c82333; }
.btn-cancel { background: #6c757d; color: white; border: none; padding: 15px 30px; cursor: pointer; border-radius: 5px; font-weight: bold; font-size: 16px; margin-left: 10px; }
.btn-cancel:hover { background: #5a6268; }
.success { background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px; color: #155724; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>⚠️ Limpar Todas as Empresas</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    // Busca total antes de deletar
    $total = $db->fetchOne("SELECT COUNT(*) as total FROM sysapp_config_empresas");
    $qtd = $total['total'];
    
    // Remove vinculações com usuários
    $db->query("DELETE FROM sysapp_config_user_empresas");
    
    // Remove vinculações com interfaces
    $db->query("DELETE FROM sysapp_config_user_empresas_interfaces");
    
    // Remove todas as empresas
    $db->query("DELETE FROM sysapp_config_empresas");
    
    // Limpa configuração da sessão
    Session::delete('Config.database');
    Session::delete('Config.databasename');
    Session::delete('Config.host');
    Session::delete('Config.user');
    Session::delete('Config.password');
    Session::delete('Config.porta');
    Session::delete('Config.empresa');
    Session::delete('Dados.database');
    
    echo "<div class='success'>";
    echo "<h2>✅ Empresas Removidas com Sucesso!</h2>";
    echo "<p>Total de empresas excluídas: <strong>$qtd</strong></p>";
    echo "<p>Todas as empresas foram removidas do sistema.</p>";
    echo "<p>Você pode agora cadastrar novas empresas manualmente através do sistema.</p>";
    echo "</div>";
    
    echo "<p><a href='escolher_empresa.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Ver Lista de Empresas</a></p>";
    echo "<p><a href='relatorios/index' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Ir para Dashboard</a></p>";
} else {
    // Conta empresas
    $total = $db->fetchOne("SELECT COUNT(*) as total FROM sysapp_config_empresas");
    $qtd = $total['total'];
    
    echo "<div class='warning'>";
    echo "<h3>⚠️ ATENÇÃO: Esta ação é IRREVERSÍVEL!</h3>";
    echo "<p>Você está prestes a <strong>EXCLUIR TODAS AS $qtd EMPRESA(S)</strong> cadastradas no sistema.</p>";
    echo "<p><strong>Esta ação irá:</strong></p>";
    echo "<ul>";
    echo "<li>Remover todas as empresas do banco de dados</li>";
    echo "<li>Remover todas as vinculações de usuários com empresas</li>";
    echo "<li>Remover todas as permissões de acesso</li>";
    echo "<li>Limpar configurações de sessão</li>";
    echo "</ul>";
    echo "<p style='color: #dc3545; font-weight: bold;'>⚠️ Após esta ação, você precisará cadastrar todas as empresas novamente manualmente!</p>";
    echo "</div>";
    
    if ($qtd > 0) {
        echo "<h3>Empresas que serão removidas:</h3>";
        echo "<ul>";
        
        $empresas = $db->fetchAll("SELECT cd_empresa, nm_empresa, ds_banco FROM sysapp_config_empresas ORDER BY nm_empresa");
        foreach ($empresas as $emp) {
            echo "<li><strong>[{$emp['cd_empresa']}]</strong> {$emp['nm_empresa']} ({$emp['ds_banco']})</li>";
        }
        
        echo "</ul>";
        
        echo "<form method='POST' style='margin-top: 30px;'>";
        echo "<p><strong>Tem certeza que deseja continuar?</strong></p>";
        echo "<button type='submit' name='confirmar' value='1' class='btn-danger' onclick='return confirm(\"CONFIRMAÇÃO FINAL: Tem CERTEZA ABSOLUTA que deseja excluir TODAS as $qtd empresas? Esta ação NÃO PODE ser desfeita!\")'>🗑️ SIM, Excluir Todas as Empresas</button>";
        echo "<a href='escolher_empresa.php' class='btn-cancel'>Cancelar</a>";
        echo "</form>";
    } else {
        echo "<p style='color: #28a745; font-weight: bold;'>✅ Não há empresas cadastradas no sistema.</p>";
        echo "<p><a href='escolher_empresa.php'>Voltar</a></p>";
    }
}

echo "</div>";
echo "</body></html>";
?>
