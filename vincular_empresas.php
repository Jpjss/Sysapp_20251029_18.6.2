<?php
/**
 * Script para vincular TODAS as empresas cadastradas ao usuário logado
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
    die("<h2>Erro: Você precisa estar logado!</h2><p><a href='usuarios/login'>Fazer Login</a></p>");
}

echo "<h2>🔗 Vincular Empresas ao Usuário</h2>";
echo "<hr>";

// Busca usuário
$sqlUsuario = "SELECT cd_usuario, nome_usuario, login_usuario FROM sysapp_config_user WHERE cd_usuario = $cd_usuario";
$usuario = $db->fetchOne($sqlUsuario);

echo "<h3>Usuário:</h3>";
echo "<p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome_usuario']) . "</p>";
echo "<p><strong>Login:</strong> " . htmlspecialchars($usuario['login_usuario']) . "</p>";

echo "<hr>";

// Busca empresas não vinculadas
$sqlNaoVinculadas = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco
                     FROM sysapp_config_empresas
                     WHERE cd_empresa NOT IN (
                         SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario
                     )
                     ORDER BY nm_empresa";
$empresasNaoVinculadas = $db->fetchAll($sqlNaoVinculadas);

if (empty($empresasNaoVinculadas)) {
    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
    echo "<h3>✅ Tudo OK!</h3>";
    echo "<p>Todas as empresas já estão vinculadas ao seu usuário.</p>";
    echo "</div>";
    echo "<p><a href='debug_empresas.php'>← Voltar ao Debug</a> | <a href='relatorios/index'>Dashboard</a></p>";
    exit;
}

// Busca interfaces disponíveis para dar permissões
$sqlInterfaces = "SELECT cd_interface, nm_interface FROM sysapp_interfaces ORDER BY cd_interface";
$interfaces = $db->fetchAll($sqlInterfaces);

echo "<h3>Empresas que serão vinculadas:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr style='background: #ddd;'>";
echo "<th>CD Empresa</th><th>Nome Empresa</th><th>Host</th><th>Database</th>";
echo "</tr>";

foreach ($empresasNaoVinculadas as $emp) {
    echo "<tr>";
    echo "<td>" . $emp['cd_empresa'] . "</td>";
    echo "<td>" . htmlspecialchars($emp['nm_empresa']) . "</td>";
    echo "<td>" . htmlspecialchars($emp['ds_host']) . "</td>";
    echo "<td>" . htmlspecialchars($emp['ds_banco']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";

// Processa vinculação se confirmado
if (isset($_POST['confirmar']) && $_POST['confirmar'] == '1') {
    echo "<h3>🔄 Processando Vinculação...</h3>";
    
    $empresasVinculadas = 0;
    $permissoesCriadas = 0;
    $erros = [];
    
    foreach ($empresasNaoVinculadas as $emp) {
        $cd_empresa = (int)$emp['cd_empresa'];
        
        // 1. Vincula empresa ao usuário
        $sqlInsertEmpresa = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) 
                             VALUES ($cd_empresa, $cd_usuario)";
        
        if ($db->query($sqlInsertEmpresa)) {
            $empresasVinculadas++;
            echo "<p>✅ Empresa <strong>" . htmlspecialchars($emp['nm_empresa']) . "</strong> vinculada com sucesso!</p>";
            
            // 2. Dá permissões (todas as interfaces) para esta empresa
            foreach ($interfaces as $interface) {
                $cd_interface = (int)$interface['cd_interface'];
                
                $sqlInsertPermissao = "INSERT INTO sysapp_config_user_empresas_interfaces 
                                       (cd_empresa, cd_usuario, cd_interface) 
                                       VALUES ($cd_empresa, $cd_usuario, $cd_interface)";
                
                if ($db->query($sqlInsertPermissao)) {
                    $permissoesCriadas++;
                }
            }
            
            echo "<p>&nbsp;&nbsp;↳ <em>" . count($interfaces) . " permissões adicionadas</em></p>";
        } else {
            $erros[] = "Erro ao vincular empresa: " . $emp['nm_empresa'];
            echo "<p>❌ Erro ao vincular empresa <strong>" . htmlspecialchars($emp['nm_empresa']) . "</strong></p>";
        }
    }
    
    echo "<hr>";
    
    if (empty($erros)) {
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
        echo "<h3>✅ PROCESSO CONCLUÍDO COM SUCESSO!</h3>";
        echo "<ul>";
        echo "<li><strong>" . $empresasVinculadas . " empresa(s)</strong> vinculada(s) ao seu usuário</li>";
        echo "<li><strong>" . $permissoesCriadas . " permissão(ões)</strong> adicionada(s)</li>";
        echo "</ul>";
        echo "<p>Agora todas as empresas aparecerão para você após fazer login!</p>";
        echo "<p><strong>⚠️ Importante:</strong> Faça logout e login novamente para as alterações terem efeito.</p>";
        echo "</div>";
        
        echo "<p>";
        echo "<a href='usuarios/logout' style='display: inline-block; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>🚪 Fazer Logout Agora</a>";
        echo "<a href='debug_empresas.php' style='display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔍 Ver Debug</a>";
        echo "</p>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "<h3>❌ Erros durante o processo:</h3>";
        echo "<ul>";
        foreach ($erros as $erro) {
            echo "<li>" . htmlspecialchars($erro) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
} else {
    // Mostra formulário de confirmação
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<h3>⚠️ ATENÇÃO:</h3>";
    echo "<p>Esta ação irá:</p>";
    echo "<ul>";
    echo "<li>Vincular <strong>" . count($empresasNaoVinculadas) . " empresa(s)</strong> ao usuário <strong>" . htmlspecialchars($usuario['nome_usuario']) . "</strong></li>";
    echo "<li>Adicionar <strong>TODAS as permissões (interfaces)</strong> para estas empresas</li>";
    echo "</ul>";
    echo "<p><strong>Deseja continuar?</strong></p>";
    echo "</div>";
    
    echo "<form method='POST'>";
    echo "<input type='hidden' name='confirmar' value='1'>";
    echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; font-weight: bold; font-size: 16px; cursor: pointer;'>✅ SIM, Vincular Empresas</button> ";
    echo "<a href='debug_empresas.php' style='display: inline-block; background: #6c757d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>❌ Cancelar</a>";
    echo "</form>";
}

?>
