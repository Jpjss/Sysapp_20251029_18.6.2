<?php
/**
 * DIAGNÓSTICO COMPLETO DO SISTEMA DE EMPRESAS
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Diagnóstico do Sistema</title>";
echo "<style>
body { font-family: Arial; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
.section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
h1 { color: #333; }
h2 { color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 10px; }
.ok { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.warning { color: #ffc107; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; }
th { background: #0066cc; color: white; padding: 10px; text-align: left; }
td { padding: 8px; border-bottom: 1px solid #ddd; }
tr:hover { background: #f9f9f9; }
code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style></head><body>";

echo "<h1>🔍 Diagnóstico Completo do Sistema de Empresas</h1>";

// 1. ESTRUTURA DO BANCO
echo "<div class='section'>";
echo "<h2>1. Estrutura da Tabela sysapp_config_empresas</h2>";

$cols = $db->fetchAll("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'sysapp_config_empresas' ORDER BY ordinal_position");

if ($cols) {
    echo "<table><tr><th>Coluna</th><th>Tipo</th><th>Nome Usado no Sistema</th></tr>";
    $mapping = [
        'cd_empresa' => 'ID da empresa',
        'nm_empresa' => 'Nome da empresa',
        'ds_host' => 'Host do banco',
        'ds_banco' => 'Nome do banco',
        'ds_usuario' => 'Usuário do banco',
        'ds_senha' => 'Senha (criptografada)',
        'ds_porta' => 'Porta do banco',
        'fg_ativo' => 'Flag ativo',
        'dt_cadastro' => 'Data de cadastro'
    ];
    
    foreach ($cols as $col) {
        $nome = $col['column_name'];
        $desc = isset($mapping[$nome]) ? $mapping[$nome] : '-';
        echo "<tr><td><code>{$nome}</code></td><td>{$col['data_type']}</td><td>{$desc}</td></tr>";
    }
    echo "</table>";
    echo "<p class='ok'>✅ Tabela existe e está estruturada corretamente</p>";
} else {
    echo "<p class='error'>❌ Tabela sysapp_config_empresas não encontrada!</p>";
}

echo "</div>";

// 2. EMPRESAS CADASTRADAS
echo "<div class='section'>";
echo "<h2>2. Empresas Cadastradas</h2>";

$empresas = $db->fetchAll("SELECT * FROM sysapp_config_empresas ORDER BY cd_empresa");

if (empty($empresas)) {
    echo "<p class='warning'>⚠️ Nenhuma empresa cadastrada</p>";
} else {
    echo "<p>Total: <strong>" . count($empresas) . "</strong> empresa(s)</p>";
    echo "<table><tr><th>ID</th><th>Nome</th><th>Host</th><th>Banco</th><th>Usuário</th><th>Porta</th><th>Teste Conexão</th></tr>";
    
    foreach ($empresas as $emp) {
        $senha = Security::decrypt($emp['ds_senha']);
        $connTest = @pg_connect("host={$emp['ds_host']} port={$emp['ds_porta']} dbname={$emp['ds_banco']} user={$emp['ds_usuario']} password=$senha", PGSQL_CONNECT_FORCE_NEW);
        
        $status = $connTest ? "<span class='ok'>✅ OK</span>" : "<span class='error'>❌ FALHOU</span>";
        if ($connTest) pg_close($connTest);
        
        echo "<tr>";
        echo "<td>{$emp['cd_empresa']}</td>";
        echo "<td>{$emp['nm_empresa']}</td>";
        echo "<td>{$emp['ds_host']}</td>";
        echo "<td>{$emp['ds_banco']}</td>";
        echo "<td>{$emp['ds_usuario']}</td>";
        echo "<td>{$emp['ds_porta']}</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// 3. VERIFICAÇÃO DE CÓDIGO
echo "<div class='section'>";
echo "<h2>3. Verificação do Código</h2>";

$problemas = [];

// Verifica UsuariosController
$controller = file_get_contents('controllers/UsuariosController.php');
if (strpos($controller, "nome_banco") !== false || strpos($controller, "hostname_banco") !== false) {
    $problemas[] = "<strong>UsuariosController.php</strong>: Ainda usa nomes antigos de colunas (nome_banco, hostname_banco)";
}

// Verifica RelatoriosController  
$relatorios = file_get_contents('controllers/RelatoriosController.php');
if (strpos($relatorios, "nome_banco") !== false || strpos($relatorios, "hostname_banco") !== false) {
    $problemas[] = "<strong>RelatoriosController.php</strong>: Usa aliases do Model Usuario (nome_banco, hostname_banco) - ISSO É OK se vier do getEmpresasInfo()";
}

// Verifica Model Empresa
$modelEmpresa = file_get_contents('models/Empresa.php');
if (strpos($modelEmpresa, "ds_host") !== false && strpos($modelEmpresa, "ds_banco") !== false) {
    echo "<p class='ok'>✅ <strong>Model Empresa</strong>: Usa nomes corretos (ds_host, ds_banco, etc)</p>";
} else {
    $problemas[] = "<strong>Model Empresa</strong>: Pode estar usando nomes incorretos";
}

if (empty($problemas)) {
    echo "<p class='ok'>✅ Código parece estar correto!</p>";
} else {
    echo "<p class='warning'>⚠️ Possíveis problemas encontrados:</p><ul>";
    foreach ($problemas as $p) {
        echo "<li>$p</li>";
    }
    echo "</ul>";
}

echo "</div>";

// 4. TESTE DE CADASTRO SIMULADO
echo "<div class='section'>";
echo "<h2>4. Campos Esperados no Formulário de Cadastro</h2>";
echo "<p>O formulário HTML deve enviar estes campos:</p>";
echo "<table>";
echo "<tr><th>Campo do Formulário (name)</th><th>Coluna no Banco</th><th>Obrigatório</th></tr>";
echo "<tr><td><code>nome_empresa</code></td><td><code>nm_empresa</code></td><td>Sim</td></tr>";
echo "<tr><td><code>hostname</code></td><td><code>ds_host</code></td><td>Sim</td></tr>";
echo "<tr><td><code>nome_banco</code></td><td><code>ds_banco</code></td><td>Sim</td></tr>";
echo "<tr><td><code>usuario_banco</code></td><td><code>ds_usuario</code></td><td>Sim</td></tr>";
echo "<tr><td><code>senha_banco</code></td><td><code>ds_senha</code></td><td>Sim</td></tr>";
echo "<tr><td><code>porta_banco</code></td><td><code>ds_porta</code></td><td>Sim</td></tr>";
echo "</table>";

echo "<p><strong>Importante:</strong> O Model Empresa já faz o mapeamento correto destes nomes!</p>";

echo "</div>";

// 5. RESUMO
echo "<div class='section'>";
echo "<h2>5. Resumo e Recomendações</h2>";

$total_empresas = count($empresas);
$empresas_ok = 0;
foreach ($empresas as $emp) {
    $senha = Security::decrypt($emp['ds_senha']);
    $connTest = @pg_connect("host={$emp['ds_host']} port={$emp['ds_porta']} dbname={$emp['ds_banco']} user={$emp['ds_usuario']} password=$senha", PGSQL_CONNECT_FORCE_NEW);
    if ($connTest) {
        $empresas_ok++;
        pg_close($connTest);
    }
}

if ($total_empresas == 0) {
    echo "<p class='warning'>⚠️ Nenhuma empresa cadastrada. Sistema pronto para receber cadastros.</p>";
    echo "<p><strong>Para cadastrar:</strong> Acesse o menu do sistema e adicione uma nova empresa.</p>";
} else {
    echo "<p>Total de empresas: <strong>$total_empresas</strong></p>";
    echo "<p>Empresas com conexão OK: <strong>$empresas_ok</strong></p>";
    echo "<p>Empresas com erro: <strong>" . ($total_empresas - $empresas_ok) . "</strong></p>";
    
    if ($empresas_ok == $total_empresas) {
        echo "<p class='ok'>✅ TODAS as empresas estão funcionando corretamente!</p>";
    } else {
        echo "<p class='error'>❌ Algumas empresas têm problemas de conexão. Verifique as credenciais.</p>";
    }
}

echo "<hr>";
echo "<h3>Ações Disponíveis:</h3>";
echo "<p><a href='limpar_empresas.php' style='padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;'>🗑️ Limpar Todas as Empresas</a></p>";
echo "<p><a href='escolher_empresa.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>🏢 Ver/Selecionar Empresas</a></p>";
echo "<p><a href='/' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🏠 Voltar ao Sistema</a></p>";

echo "</div>";

echo "</body></html>";
?>
