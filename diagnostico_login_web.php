<?php
// diagnostico_login_web.php
// Acessível via navegador para testar o ambiente real do servidor web

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Login (Ambiente Web)</h1>";
echo "<pre>";

// 1. Verificar Caminhos
echo "<h2>1. Caminhos e Arquivos</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Current Dir: " . __DIR__ . "\n";

$files = [
    'config/config.php',
    'config/database.php',
    'models/Usuario.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    echo "Arquivo $file: " . (file_exists($path) ? "EXISTE" : "NÃO EXISTE") . "\n";
    if (file_exists($path)) {
        echo "  - Tamanho: " . filesize($path) . " bytes\n";
        echo "  - Modificado: " . date("Y-m-d H:i:s", filemtime($path)) . "\n";
    }
}

// 2. Carregar Dependências
echo "\n<h2>2. Carregando Classes</h2>";
try {
    require_once 'config/config.php';
    echo "config.php carregado.\n";
    
    require_once 'config/database.php';
    echo "database.php carregado.\n";
    
    require_once 'models/Usuario.php';
    echo "Usuario.php carregado.\n";
} catch (Exception $e) {
    echo "ERRO ao carregar arquivos: " . $e->getMessage() . "\n";
}

// 3. Testar Conexão com Banco
echo "\n<h2>3. Teste de Conexão</h2>";
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "Conexão obtida com sucesso.\n";
        echo "Status da conexão: " . pg_connection_status($conn) . "\n";
        echo "Host: " . pg_host($conn) . "\n";
        echo "DB: " . pg_dbname($conn) . "\n";
        echo "Port: " . pg_port($conn) . "\n";
    } else {
        echo "FALHA ao obter conexão.\n";
    }
} catch (Exception $e) {
    echo "EXCEÇÃO na conexão: " . $e->getMessage() . "\n";
}

// 4. Testar Busca de Usuário
echo "\n<h2>4. Teste de Busca (Usuario::findByLogin)</h2>";
$loginTeste = 'testeusuario';
echo "Buscando login: '$loginTeste'\n";

try {
    $usuarioModel = new Usuario();
    
    // Dump da query que seria executada (simulação manual)
    $loginEscaped = $db->escape(strtolower($loginTeste));
    $sql = "SELECT cd_usuario, nm_usuario, ds_login, ds_email 
            FROM sysapp_config_user 
            WHERE LOWER(ds_login) = '$loginEscaped' 
               OR LOWER(ds_email) = '$loginEscaped'";
    echo "SQL Manual: $sql\n";
    
    $manualResult = $db->fetchOne($sql);
    echo "Resultado SQL Manual: " . print_r($manualResult, true) . "\n";
    
    // Teste do método real
    echo "Chamando Usuario::findByLogin('$loginTeste')...\n";
    $result = $usuarioModel->findByLogin($loginTeste);
    echo "Resultado do Método: " . print_r($result, true) . "\n";
    
} catch (Exception $e) {
    echo "ERRO durante busca: " . $e->getMessage() . "\n";
}

echo "</pre>";
