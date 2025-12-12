<?php
/**
 * Atualiza a lista de empresas na sessão do usuário
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'models/Usuario.php';

Session::start();

if (!Session::check('Questionarios.cd_usu')) {
    die('ERRO: Usuário não logado. <a href="/">Fazer login</a>');
}

$cd_usuario = Session::read('Questionarios.cd_usu');
$nm_usuario = Session::read('Questionarios.nm_usu');

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

$usuarioModel = new Usuario($db);

// Busca empresas vinculadas ao usuário
$empresas = $db->fetchAll("SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario");

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Atualizar Empresas na Sessão</title>";
echo "<style>body { font-family: Arial; max-width: 800px; margin: 40px auto; padding: 20px; } 
      .success { background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; color: #155724; }
      .info { background: #d1ecf1; padding: 20px; border-radius: 8px; margin: 20px 0; color: #0c5460; }
      table { width: 100%; border-collapse: collapse; margin: 20px 0; }
      th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
      th { background: #f2f2f2; }
      .btn { padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px 10px 0; }
      .btn-blue { background: #007bff; }
      </style></head><body>";

echo "<h1>🔄 Atualizar Empresas na Sessão</h1>";
echo "<p><strong>Usuário:</strong> $nm_usuario (ID: $cd_usuario)</p>";

if (empty($empresas)) {
    echo "<p style='color: red;'>❌ Nenhuma empresa vinculada ao usuário!</p>";
} else {
    $cd_empresas = [];
    foreach ($empresas as $emp) {
        $cd_empresas[] = $emp['cd_empresa'];
    }
    $cd_empresas_str = implode(',', $cd_empresas);
    
    // Busca informações das empresas
    $infoDb = $usuarioModel->getEmpresasInfo($cd_usuario, $cd_empresas_str);
    
    echo "<div class='info'>";
    echo "<strong>📋 Empresas vinculadas ao usuário:</strong>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Banco</th><th>Host</th></tr>";
    
    foreach ($infoDb as $emp) {
        echo "<tr>";
        echo "<td>{$emp['cd_empresa']}</td>";
        echo "<td>{$emp['nome_empresa']}</td>";
        echo "<td>{$emp['nome_banco']}</td>";
        echo "<td>{$emp['hostname_banco']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "</div>";
    
    // Atualiza a sessão
    Session::write('Dados.database', $infoDb);
    
    echo "<div class='success'>";
    echo "<h3>✅ Sessão atualizada com sucesso!</h3>";
    echo "<p>Total de empresas na sessão: <strong>" . count($infoDb) . "</strong></p>";
    echo "<p>Agora a tela de seleção de empresas mostrará todas as empresas cadastradas.</p>";
    echo "</div>";
}

echo "<p>";
echo "<a href='relatorios/empresa' class='btn btn-blue'>📋 Ir para Seleção de Empresa</a>";
echo "<a href='relatorios/index' class='btn'>🏠 Ir para Dashboard</a>";
echo "</p>";

echo "</body></html>";
