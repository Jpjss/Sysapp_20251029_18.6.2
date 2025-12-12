<?php
/**
 * Excluir empresas específicas com erro
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Excluir Empresas com Erro</title>";
echo "<style>
body { font-family: Arial; max-width: 800px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #dc3545; }
.success { background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px; color: #155724; margin: 20px 0; }
.info { background: #d1ecf1; border: 2px solid #0c5460; padding: 20px; border-radius: 8px; color: #0c5460; margin: 20px 0; }
ul { line-height: 2; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🗑️ Excluir Empresas com Erro</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    // IDs das empresas com erro: 1 (Padrão), 4 (A Favorita), 5 (140.238.186.83)
    $empresas_excluir = [1, 4, 5];
    
    $excluidas = 0;
    foreach ($empresas_excluir as $cd_empresa) {
        // Remove vinculações
        $db->query("DELETE FROM sysapp_config_user_empresas WHERE cd_empresa = $cd_empresa");
        $db->query("DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_empresa = $cd_empresa");
        
        // Remove empresa
        $result = $db->query("DELETE FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa");
        if ($result) $excluidas++;
    }
    
    echo "<div class='success'>";
    echo "<h2>✅ Empresas Excluídas com Sucesso!</h2>";
    echo "<p>Total de empresas excluídas: <strong>$excluidas</strong></p>";
    echo "<p>As empresas com erro foram removidas. Sobraram apenas as que funcionam corretamente.</p>";
    echo "</div>";
    
    echo "<p><a href='diagnostico_empresas.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ver Diagnóstico</a></p>";
    echo "<p><a href='relatorios/empresa' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Selecionar Empresa</a></p>";
} else {
    echo "<div class='info'>";
    echo "<p>Este script vai excluir as seguintes empresas COM ERRO de conexão:</p>";
    echo "<ul>";
    echo "<li><strong>ID 1</strong> - Empresa Padrão (localhost/sysapp)</li>";
    echo "<li><strong>ID 4</strong> - A Favorita (banco.afavorita.systec.ftp.sh/bd_afavorita)</li>";
    echo "<li><strong>ID 5</strong> - 140.238.186.83 (bd.duilson)</li>";
    echo "</ul>";
    echo "<p><strong>Serão mantidas:</strong></p>";
    echo "<ul>";
    echo "<li><strong>ID 2</strong> - Empresa Exemplo LTDA ✅</li>";
    echo "<li><strong>ID 3</strong> - Propasso ✅</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<form method='POST'>";
    echo "<p><strong>Confirma a exclusão das 3 empresas com erro?</strong></p>";
    echo "<button type='submit' name='confirmar' value='1' style='padding: 15px 30px; background: #dc3545; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px;'>🗑️ SIM, Excluir as 3 Empresas com Erro</button>";
    echo " <a href='diagnostico_empresas.php' style='padding: 15px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Cancelar</a>";
    echo "</form>";
}

echo "</div>";
echo "</body></html>";
?>
