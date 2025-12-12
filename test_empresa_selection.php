<?php
/**
 * Debug: Teste de seleção de empresa
 */

// Carrega configurações
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

echo "<h1>Debug: Seleção de Empresa</h1>";
echo "<hr>";

// Verifica se usuário está logado
echo "<h2>1. Verificar Login</h2>";
$cd_usu = Session::read('Questionarios.cd_usu');
$nm_usu = Session::read('Questionarios.nm_usu');

if ($cd_usu) {
    echo "<p style='color: green;'>✅ Usuário logado: <strong>$nm_usu</strong> (ID: $cd_usu)</p>";
} else {
    echo "<p style='color: red;'>❌ Nenhum usuário logado!</p>";
    echo "<p><a href='usuarios/login'>Fazer login</a></p>";
    exit;
}

// Verifica empresas na sessão
echo "<h2>2. Empresas Disponíveis na Sessão</h2>";
$empresas = Session::read('Dados.database');

if (empty($empresas)) {
    echo "<p style='color: orange;'>⚠️ Nenhuma empresa na sessão 'Dados.database'</p>";
    
    // Busca empresas do banco
    echo "<h3>Buscando empresas do banco de dados...</h3>";
    $db = Database::getInstance();
    
    $sqlEmpresas = "SELECT ce.cd_empresa, ce.nome_empresa, ce.hostname_banco, ce.nome_banco, 
                           ce.usuario_banco, ce.senha_banco, ce.porta_banco
                    FROM sysapp_config_empresas ce
                    INNER JOIN sysapp_config_user_empresas cue ON ce.cd_empresa = cue.cd_empresa
                    WHERE cue.cd_usuario = $cd_usu
                    ORDER BY ce.nome_empresa";
    
    $empresas = $db->fetchAll($sqlEmpresas);
    
    if (empty($empresas)) {
        echo "<p style='color: red;'>❌ Nenhuma empresa vinculada ao usuário no banco!</p>";
        echo "<p>Execute o script de vinculação: <a href='vincular_empresas_automatico.php'>vincular_empresas_automatico.php</a></p>";
        exit;
    } else {
        echo "<p style='color: green;'>✅ Encontradas " . count($empresas) . " empresa(s) vinculadas!</p>";
        Session::write('Dados.database', $empresas);
    }
} else {
    echo "<p style='color: green;'>✅ " . count($empresas) . " empresa(s) encontradas na sessão</p>";
}

// Lista empresas
echo "<h3>Lista de Empresas:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Código</th><th>Nome</th><th>Host</th><th>Banco</th><th>Porta</th><th>Usuário</th><th>Ação</th></tr>";

foreach ($empresas as $emp) {
    $senha_decrypted = Security::decrypt($emp['senha_banco']);
    
    echo "<tr>";
    echo "<td>" . $emp['cd_empresa'] . "</td>";
    echo "<td>" . $emp['nome_empresa'] . "</td>";
    echo "<td>" . $emp['hostname_banco'] . "</td>";
    echo "<td>" . $emp['nome_banco'] . "</td>";
    echo "<td>" . $emp['porta_banco'] . "</td>";
    echo "<td>" . $emp['usuario_banco'] . "</td>";
    echo "<td><button onclick=\"selecionarEmpresa(" . $emp['cd_empresa'] . ")\">Selecionar</button></td>";
    echo "</tr>";
    
    // Teste de conexão
    $connTest = @pg_connect(
        "host={$emp['hostname_banco']} port={$emp['porta_banco']} dbname={$emp['nome_banco']} user={$emp['usuario_banco']} password=$senha_decrypted",
        PGSQL_CONNECT_FORCE_NEW
    );
    
    if ($connTest) {
        echo "<tr><td colspan='7' style='color: green; background: #e8f5e9;'>✅ Conexão testada com sucesso!</td></tr>";
        pg_close($connTest);
    } else {
        echo "<tr><td colspan='7' style='color: red; background: #ffebee;'>❌ ERRO: Não foi possível conectar! Verifique as credenciais.</td></tr>";
    }
}

echo "</table>";

// Verifica empresa atualmente selecionada
echo "<h2>3. Empresa Atual Selecionada</h2>";
$empresaAtual = Session::read('Config.empresa');
$bancoAtual = Session::read('Config.database');
$hostAtual = Session::read('Config.host');

if ($empresaAtual) {
    echo "<p style='color: green;'>✅ Empresa ativa: <strong>$empresaAtual</strong></p>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> $hostAtual</li>";
    echo "<li><strong>Banco:</strong> $bancoAtual</li>";
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhuma empresa selecionada atualmente</p>";
}

?>

<script>
function selecionarEmpresa(cd_empresa) {
    // Cria formulário e submete
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'relatorios/empresa';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'cd_empresa';
    input.value = cd_empresa;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    
    th {
        background: #3f51b5;
        color: white;
        padding: 12px;
    }
    
    td {
        padding: 10px;
    }
    
    button {
        background: #4caf50;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 4px;
    }
    
    button:hover {
        background: #45a049;
    }
</style>
