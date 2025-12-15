<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

// Simula login
$_SESSION['Dados']['id_usuario'] = 1;
$_SESSION['Questionarios']['nm_usu'] = 'Admin';

echo "=== TESTANDO API DE EMPRESAS ===\n\n";

// Lista empresas
$db = Database::getInstance();
$db->connect();

$sql = "SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_porta, fg_ativo 
        FROM sysapp_config_empresas 
        WHERE fg_ativo = 'S' 
        ORDER BY nm_empresa";

$result = pg_query($db->getConnection(), $sql);

$empresas = [];
while ($row = pg_fetch_assoc($result)) {
    $empresas[] = [
        'id' => (int)$row['cd_empresa'],
        'nome' => $row['nm_empresa'],
        'host' => $row['ds_host'],
        'banco' => $row['ds_banco'],
        'porta' => $row['ds_porta']
    ];
}

echo "Empresas encontradas: " . count($empresas) . "\n\n";
foreach ($empresas as $emp) {
    echo "ID: {$emp['id']}\n";
    echo "Nome: {$emp['nome']}\n";
    echo "Banco: {$emp['banco']}\n";
    echo "---\n";
}

// Testa seleção
echo "\n\n=== TESTANDO SELEÇÃO DE EMPRESA ===\n\n";
$empresaId = 1;

$sql = "SELECT * FROM sysapp_config_empresas WHERE cd_empresa = $1";
$result = pg_query_params($db->getConnection(), $sql, [$empresaId]);
$empresa = pg_fetch_assoc($result);

if ($empresa) {
    $_SESSION['Config']['cd_empresa'] = $empresa['cd_empresa'];
    $_SESSION['Config']['empresa'] = $empresa['nm_empresa'];
    $_SESSION['Config']['host'] = $empresa['ds_host'];
    $_SESSION['Config']['database'] = $empresa['ds_banco'];
    $_SESSION['Config']['user'] = $empresa['ds_usuario'];
    $_SESSION['Config']['password'] = $empresa['ds_senha'];
    $_SESSION['Config']['porta'] = $empresa['ds_porta'];
    
    echo "✓ Empresa selecionada: {$empresa['nm_empresa']}\n";
    echo "Banco: {$empresa['ds_banco']}\n";
    
    // Testa conexão com o banco da empresa
    echo "\n=== TESTANDO CONEXÃO COM BANCO DA EMPRESA ===\n\n";
    
    $db2 = Database::getInstance();
    $db2->connect(
        $empresa['ds_host'],
        $empresa['ds_banco'],
        $empresa['ds_usuario'],
        $empresa['ds_senha'],
        $empresa['ds_porta']
    );
    
    $conn2 = $db2->getConnection();
    if ($conn2) {
        echo "✓ Conectado ao banco: {$empresa['ds_banco']}\n\n";
        
        // Verifica tabelas disponíveis
        $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename IN ('prc_pessoa', 'rc_lanc_cpl', 'glb_questionarios') ORDER BY tablename";
        $result = pg_query($conn2, $sql);
        
        echo "Tabelas encontradas:\n";
        while ($row = pg_fetch_assoc($result)) {
            echo "- " . $row['tablename'] . "\n";
        }
    } else {
        echo "✗ Falha ao conectar\n";
    }
} else {
    echo "✗ Empresa não encontrada\n";
}
