<?php
/**
 * Script para debug de empresas cadastradas vs empresas vinculadas ao usuário
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

// Inicia conexão com banco sysapp
$db = Database::getInstance();
$db->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

$cd_usuario = Session::read('Questionarios.cd_usu');

echo "<h2>Debug de Empresas Cadastradas</h2>";
echo "<hr>";

// 1. Busca TODAS as empresas cadastradas no sistema
echo "<h3>1. TODAS as Empresas Cadastradas no Sistema:</h3>";
$sql = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_porta 
        FROM sysapp_config_empresas 
        ORDER BY cd_empresa";
$todasEmpresas = $db->fetchAll($sql);

if ($todasEmpresas) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #ddd;'>";
    echo "<th>CD Empresa</th><th>Nome Empresa</th><th>Host</th><th>Database</th><th>Porta</th>";
    echo "</tr>";
    foreach ($todasEmpresas as $emp) {
        echo "<tr>";
        echo "<td>" . $emp['cd_empresa'] . "</td>";
        echo "<td>" . htmlspecialchars($emp['nm_empresa']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['ds_host']) . "</td>";
        echo "<td>" . htmlspecialchars($emp['ds_banco']) . "</td>";
        echo "<td>" . $emp['ds_porta'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Nenhuma empresa cadastrada no sistema!</p>";
}

echo "<hr>";

// 2. Usuário logado
echo "<h3>2. Usuário Logado:</h3>";
if ($cd_usuario) {
    $sqlUsuario = "SELECT cd_usuario, nome_usuario, login_usuario FROM sysapp_config_user WHERE cd_usuario = $cd_usuario";
    $usuario = $db->fetchOne($sqlUsuario);
    
    echo "<p><strong>CD Usuário:</strong> " . $cd_usuario . "</p>";
    echo "<p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome_usuario']) . "</p>";
    echo "<p><strong>Login:</strong> " . htmlspecialchars($usuario['login_usuario']) . "</p>";
} else {
    echo "<p style='color: red;'>Usuário NÃO está logado!</p>";
}

echo "<hr>";

// 3. Empresas vinculadas ao usuário
echo "<h3>3. Empresas Vinculadas ao Usuário (sysapp_config_user_empresas):</h3>";
if ($cd_usuario) {
    $sqlVinculadas = "SELECT uce.cd_empresa, ce.nm_empresa, ce.ds_host, ce.ds_banco, ce.ds_porta
                      FROM sysapp_config_user_empresas uce
                      INNER JOIN sysapp_config_empresas ce ON uce.cd_empresa = ce.cd_empresa
                      WHERE uce.cd_usuario = $cd_usuario
                      ORDER BY ce.nm_empresa";
    $empresasVinculadas = $db->fetchAll($sqlVinculadas);
    
    if ($empresasVinculadas) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #ddd;'>";
        echo "<th>CD Empresa</th><th>Nome Empresa</th><th>Host</th><th>Database</th><th>Porta</th>";
        echo "</tr>";
        foreach ($empresasVinculadas as $emp) {
            echo "<tr>";
            echo "<td>" . $emp['cd_empresa'] . "</td>";
            echo "<td>" . htmlspecialchars($emp['nm_empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($emp['ds_host']) . "</td>";
            echo "<td>" . htmlspecialchars($emp['ds_banco']) . "</td>";
            echo "<td>" . $emp['ds_porta'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Usuário NÃO tem empresas vinculadas!</p>";
    }
}

echo "<hr>";

// 4. Empresas NÃO vinculadas ao usuário
echo "<h3>4. Empresas NÃO Vinculadas ao Usuário:</h3>";
if ($cd_usuario) {
    $sqlNaoVinculadas = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_porta
                         FROM sysapp_config_empresas
                         WHERE cd_empresa NOT IN (
                             SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario
                         )
                         ORDER BY nm_empresa";
    $empresasNaoVinculadas = $db->fetchAll($sqlNaoVinculadas);
    
    if ($empresasNaoVinculadas) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #ffcccc;'>";
        echo "<th>CD Empresa</th><th>Nome Empresa</th><th>Host</th><th>Database</th><th>Porta</th>";
        echo "</tr>";
        foreach ($empresasNaoVinculadas as $emp) {
            echo "<tr>";
            echo "<td>" . $emp['cd_empresa'] . "</td>";
            echo "<td>" . htmlspecialchars($emp['nm_empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($emp['ds_host']) . "</td>";
            echo "<td>" . htmlspecialchars($emp['ds_banco']) . "</td>";
            echo "<td>" . $emp['ds_porta'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color: red; font-weight: bold;'>⚠️ Estas empresas NÃO aparecem para você porque não estão vinculadas ao seu usuário!</p>";
    } else {
        echo "<p style='color: green;'>Todas as empresas estão vinculadas ao usuário!</p>";
    }
}

echo "<hr>";

// 5. Permissões (interfaces) do usuário
echo "<h3>5. Permissões (Interfaces) do Usuário:</h3>";
if ($cd_usuario) {
    $sqlPermissoes = "SELECT DISTINCT i.cd_interface, i.nm_interface, cei.cd_empresa, ce.nm_empresa
                      FROM sysapp_config_user_empresas_interfaces cei
                      INNER JOIN sysapp_interfaces i ON cei.cd_interface = i.cd_interface
                      INNER JOIN sysapp_config_empresas ce ON cei.cd_empresa = ce.cd_empresa
                      WHERE cei.cd_usuario = $cd_usuario
                      ORDER BY ce.nm_empresa, i.nm_interface";
    $permissoes = $db->fetchAll($sqlPermissoes);
    
    if ($permissoes) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #ddd;'>";
        echo "<th>CD Empresa</th><th>Nome Empresa</th><th>CD Interface</th><th>Nome Interface</th>";
        echo "</tr>";
        foreach ($permissoes as $perm) {
            echo "<tr>";
            echo "<td>" . $perm['cd_empresa'] . "</td>";
            echo "<td>" . htmlspecialchars($perm['nm_empresa']) . "</td>";
            echo "<td>" . $perm['cd_interface'] . "</td>";
            echo "<td>" . htmlspecialchars($perm['nm_interface']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Usuário NÃO tem permissões (interfaces) configuradas!</p>";
    }
}

echo "<hr>";

// 6. Diagnóstico e solução
echo "<h3>6. 🔧 DIAGNÓSTICO E SOLUÇÃO:</h3>";
if ($cd_usuario && !empty($empresasNaoVinculadas)) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
    echo "<h4>⚠️ PROBLEMA IDENTIFICADO:</h4>";
    echo "<p>Você tem <strong>" . count($empresasNaoVinculadas) . " empresa(s)</strong> cadastrada(s) no sistema que <strong>NÃO estão vinculadas</strong> ao seu usuário.</p>";
    echo "<p>Por isso elas não aparecem na seleção de empresas após o login.</p>";
    
    echo "<h4>✅ SOLUÇÃO:</h4>";
    echo "<p>Você tem duas opções:</p>";
    echo "<ol>";
    echo "<li><strong>OPÇÃO 1:</strong> Execute o script <code>vincular_empresas.php</code> que eu vou criar para vincular automaticamente TODAS as empresas ao seu usuário.</li>";
    echo "<li><strong>OPÇÃO 2:</strong> Edite seu usuário através da tela de administração e selecione as empresas que devem ser vinculadas.</li>";
    echo "</ol>";
    
    echo "<p><a href='vincular_empresas.php' style='display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔗 Vincular TODAS as Empresas ao Meu Usuário</a></p>";
    echo "</div>";
} else if ($cd_usuario && empty($empresasNaoVinculadas)) {
    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
    echo "<h4>✅ TUDO OK!</h4>";
    echo "<p>Todas as empresas cadastradas já estão vinculadas ao seu usuário.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='relatorios/index'>← Voltar ao Dashboard</a></p>";
?>
