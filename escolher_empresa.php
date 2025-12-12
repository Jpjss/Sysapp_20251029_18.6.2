<?php
/**
 * Listar e Selecionar Empresa
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

// IMPORTANTE: Conecta ao banco SYSAPP (onde estão as configurações)
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

// Se recebeu ação de excluir empresa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_empresa'])) {
    $cd_empresa = (int)$_POST['excluir_empresa'];
    
    // Remove vinculações com usuários
    $db->query("DELETE FROM sysapp_config_user_empresas WHERE cd_empresa = $cd_empresa");
    
    // Remove vinculações com interfaces
    $db->query("DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_empresa = $cd_empresa");
    
    // Remove a empresa
    $db->query("DELETE FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa");
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 20px; text-align: center;'>";
    echo "✅ Empresa excluída com sucesso!";
    echo "</div>";
}

// Se recebeu código da empresa via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cd_empresa'])) {
    $cd_empresa = (int)$_POST['cd_empresa'];
    
    $empresa = $db->fetchOne("SELECT * FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa");
    
    if ($empresa) {
        $senha_decrypt = Security::decrypt($empresa['ds_senha']);
        
        Session::write('Config.database', $empresa['ds_banco']);
        Session::write('Config.databasename', $empresa['ds_banco']);
        Session::write('Config.host', $empresa['ds_host']);
        Session::write('Config.user', $empresa['ds_usuario']);
        Session::write('Config.password', $senha_decrypt);
        Session::write('Config.porta', $empresa['ds_porta']);
        Session::write('Config.empresa', $empresa['nm_empresa']);
        
        // Reconecta ao banco
        $result = $db->connect(
            $empresa['ds_host'],
            $empresa['ds_banco'],
            $empresa['ds_usuario'],
            $senha_decrypt,
            $empresa['ds_porta']
        );
        
        if ($result) {
            echo "<h1 style='color: green;'>✅ Empresa '{$empresa['nm_empresa']}' selecionada com sucesso!</h1>";
            echo "<p><a href='relatorios/index' style='padding: 15px 30px; background: #4CAF50; color: white; text-decoration: none; border-radius: 8px; display: inline-block; font-size: 18px; font-weight: bold;'>➜ Ir para Dashboard</a></p>";
            exit;
        } else {
            echo "<h1 style='color: red;'>❌ ERRO ao conectar ao banco '{$empresa['ds_banco']}'!</h1>";
            echo "<p>Verifique as credenciais no banco de dados.</p>";
        }
    }
}

// Lista todas as empresas
$empresas = $db->fetchAll("SELECT * FROM sysapp_config_empresas ORDER BY nome_empresa");

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Selecionar Empresa</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
th { background: #4CAF50; color: white; padding: 15px; text-align: left; }
td { padding: 12px 15px; border-bottom: 1px solid #ddd; }
tr:hover { background: #f9f9f9; }
.btn { background: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-weight: bold; }
.btn:hover { background: #45a049; }
.btn-delete { background: #dc3545; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-weight: bold; }
.btn-delete:hover { background: #c82333; }
.status { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
.ok { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
</style></head><body>";

echo "<h1>🏢 Selecionar Empresa</h1>";
echo "<p><strong>Usuário:</strong> " . Session::read('Questionarios.nm_usu') . "</p>";
echo "<p>Total de empresas cadastradas: <strong>" . count($empresas) . "</strong></p>";
echo "<p style='color: #dc3545;'><strong>⚠️ Use o botão vermelho para excluir empresas que não são mais necessárias</strong></p>";

if (empty($empresas)) {
    echo "<p style='color: red;'>❌ Nenhuma empresa cadastrada no sistema!</p>";
} else {
    echo "<table>";
    echo "<tr><th>Código</th><th>Nome</th><th>Host</th><th>Banco</th><th>Usuário</th><th>Status</th><th>Ações</th></tr>";
    
    foreach ($empresas as $emp) {
        $senha_decrypt = Security::decrypt($emp['ds_senha']);
        
        // Testa conexão
        $connTest = @pg_connect(
            "host={$emp['ds_host']} port={$emp['ds_porta']} dbname={$emp['ds_banco']} user={$emp['ds_usuario']} password=$senha_decrypt",
            PGSQL_CONNECT_FORCE_NEW
        );
        
        $status = $connTest ? "<span class='status ok'>✅ OK</span>" : "<span class='status error'>❌ ERRO</span>";
        if ($connTest) pg_close($connTest);
        
        echo "<tr>";
        echo "<td>{$emp['cd_empresa']}</td>";
        echo "<td><strong>{$emp['nm_empresa']}</strong></td>";
        echo "<td>{$emp['ds_host']}</td>";
        echo "<td>{$emp['ds_banco']}</td>";
        echo "<td>{$emp['ds_usuario']}</td>";
        echo "<td>$status</td>";
        echo "<td style='display: flex; gap: 10px; justify-content: center;'>";
        echo "<form method='POST' style='margin:0;'>";
        echo "<input type='hidden' name='cd_empresa' value='{$emp['cd_empresa']}'>";
        echo "<button type='submit' class='btn'>Selecionar</button>";
        echo "</form>";
        echo "<form method='POST' style='margin:0;' onsubmit='return confirm(\"Tem certeza que deseja excluir a empresa {$emp['nm_empresa']}? Esta ação não pode ser desfeita!\")'>";
        echo "<input type='hidden' name='excluir_empresa' value='{$emp['cd_empresa']}'>";
        echo "<button type='submit' class='btn-delete'>Excluir</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<br><p><a href='usuarios/logout'>← Sair</a></p>";
echo "</body></html>";
?>
