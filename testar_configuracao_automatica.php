<?php
/**
 * Teste Rápido - Configuração Automática
 * 
 * Testa a configuração em UMA empresa para ver se está funcionando
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/core/DatabaseSetup.php';

echo "<html><head><meta charset='utf-8'><title>Teste de Configuração</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; max-width: 900px; margin: 0 auto; }
    h1 { color: #667eea; }
    .success { background: #e8f5e9; padding: 10px; border-left: 4px solid #4caf50; margin: 10px 0; }
    .error { background: #ffebee; padding: 10px; border-left: 4px solid #f44336; margin: 10px 0; }
    .info { background: #e3f2fd; padding: 10px; border-left: 4px solid #2196f3; margin: 10px 0; }
    pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style></head><body><div class='container'>";

echo "<h1>🧪 Teste de Configuração Automática</h1>";

try {
    $db = Database::getInstance();
    
    // Busca a primeira empresa ativa
    $empresa = $db->fetchOne("
        SELECT cd_empresa, nm_empresa, hostname_banco, nome_banco, 
               usuario_banco, senha_banco, porta_banco
        FROM sysapp_config_empresas 
        WHERE fg_ativo = 'S'
        ORDER BY cd_empresa
        LIMIT 1
    ");
    
    if (!$empresa) {
        echo "<div class='error'>❌ Nenhuma empresa encontrada no sistema</div>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<div class='info'>";
    echo "<strong>Empresa selecionada para teste:</strong><br>";
    echo "ID: {$empresa['cd_empresa']}<br>";
    echo "Nome: {$empresa['nm_empresa']}<br>";
    echo "Banco: {$empresa['nome_banco']}<br>";
    echo "</div>";
    
    // Descriptografa senha
    $senha = Security::decrypt($empresa['senha_banco']);
    
    $dbConfig = [
        'host' => $empresa['hostname_banco'],
        'database' => $empresa['nome_banco'],
        'user' => $empresa['usuario_banco'],
        'password' => $senha,
        'port' => $empresa['porta_banco']
    ];
    
    echo "<h2>Executando configuração...</h2>";
    
    $result = DatabaseSetup::setupNewDatabase($dbConfig, $empresa['cd_empresa']);
    
    if ($result['success']) {
        echo "<div class='success'>";
        echo "<strong>✅ SUCESSO!</strong><br>";
        echo $result['message'];
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ ERRO</strong><br>";
        echo $result['message'];
        echo "</div>";
    }
    
    // Log
    echo "<h2>Log de Execução</h2>";
    echo "<pre>";
    foreach ($result['log'] as $linha) {
        echo htmlspecialchars($linha) . "\n";
    }
    echo "</pre>";
    
    // Erros
    if (!empty($result['errors'])) {
        echo "<h2>Erros</h2>";
        echo "<pre>";
        foreach ($result['errors'] as $erro) {
            echo htmlspecialchars($erro) . "\n";
        }
        echo "</pre>";
    }
    
    echo "<br><a href='aplicar_configuracoes_todas_empresas.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Aplicar em Todas as Empresas</a>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ ERRO:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</div></body></html>";
