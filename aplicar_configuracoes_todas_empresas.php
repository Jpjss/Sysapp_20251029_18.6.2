<?php
/**
 * Aplicar Configurações Automáticas em Todas as Empresas Existentes
 * 
 * Este script aplica as mesmas correções que foram feitas na Propaso
 * em todas as empresas cadastradas no sistema
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutos

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/core/DatabaseSetup.php';

echo "<html><head><meta charset='utf-8'><title>Configuração Automática de Empresas</title>";
echo "<style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        padding: 30px; 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    .container {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        max-width: 1200px;
        margin: 0 auto;
    }
    h1 { 
        color: #667eea; 
        border-bottom: 3px solid #667eea; 
        padding-bottom: 15px;
        margin-bottom: 30px;
    }
    h2 { 
        color: #764ba2; 
        margin-top: 30px;
        border-left: 4px solid #764ba2;
        padding-left: 15px;
    }
    .success { 
        background: #e8f5e9; 
        padding: 15px; 
        border-left: 4px solid #4caf50; 
        margin: 10px 0; 
        border-radius: 5px;
    }
    .error { 
        background: #ffebee; 
        padding: 15px; 
        border-left: 4px solid #f44336; 
        margin: 10px 0; 
        border-radius: 5px;
    }
    .warning { 
        background: #fff3cd; 
        padding: 15px; 
        border-left: 4px solid #f59e0b; 
        margin: 10px 0; 
        border-radius: 5px;
    }
    .info { 
        background: #e3f2fd; 
        padding: 15px; 
        border-left: 4px solid #2196f3; 
        margin: 10px 0; 
        border-radius: 5px;
    }
    .log-box {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 20px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.6;
        margin: 15px 0;
        max-height: 500px;
        overflow-y: auto;
    }
    .empresa-card {
        background: #f8f9fa;
        padding: 20px;
        margin: 15px 0;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .empresa-titulo {
        font-size: 18px;
        font-weight: bold;
        color: #495057;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-success { background: #d4edda; color: #155724; }
    .status-warning { background: #fff3cd; color: #856404; }
    .status-error { background: #f8d7da; color: #721c24; }
    .btn {
        display: inline-block;
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        margin: 10px 5px;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    .progress-bar {
        width: 100%;
        height: 30px;
        background: #e9ecef;
        border-radius: 15px;
        overflow: hidden;
        margin: 20px 0;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
</style></head><body><div class='container'>";

echo "<h1>🔧 Configuração Automática de Empresas</h1>";

echo "<div class='info'>";
echo "<strong>ℹ️ O que este script faz:</strong><br>";
echo "• Configura tabelas e interfaces do SysApp em cada banco<br>";
echo "• Cria views necessárias para relatórios<br>";
echo "• Adiciona índices para melhorar performance<br>";
echo "• Verifica estrutura das tabelas principais<br>";
echo "• Aplica as mesmas correções feitas na Propaso<br>";
echo "</div>";

// Verifica se foi confirmado
if (!isset($_GET['confirmar'])) {
    echo "<div class='warning'>";
    echo "<strong>⚠️ ATENÇÃO:</strong><br>";
    echo "Este script irá modificar o banco de dados de <strong>TODAS AS EMPRESAS</strong> cadastradas.<br>";
    echo "Certifique-se de que entende o que será feito antes de continuar.<br><br>";
    echo "<a href='?confirmar=sim' class='btn'>✅ Confirmar e Executar</a>";
    echo "<a href='/relatorios/index' class='btn' style='background: #6c757d;'>❌ Cancelar</a>";
    echo "</div>";
    echo "</div></body></html>";
    exit;
}

echo "<h2>📊 Processando Empresas...</h2>";

$startTime = microtime(true);

try {
    $result = DatabaseSetup::applyToAllExistingDatabases();
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    // Exibe resultado
    if ($result['success']) {
        echo "<div class='success'>";
        echo "<strong>✅ SUCESSO!</strong><br>";
        echo $result['message'];
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<strong>⚠️ CONCLUÍDO COM AVISOS</strong><br>";
        echo $result['message'];
        echo "</div>";
    }
    
    // Log detalhado
    if (!empty($result['log'])) {
        echo "<h2>📝 Log Detalhado</h2>";
        echo "<div class='log-box'>";
        foreach ($result['log'] as $linha) {
            // Adiciona cores ao log
            if (strpos($linha, '✅') !== false) {
                echo "<span style='color: #4caf50;'>$linha</span><br>";
            } elseif (strpos($linha, '⚠️') !== false || strpos($linha, 'ℹ️') !== false) {
                echo "<span style='color: #f59e0b;'>$linha</span><br>";
            } elseif (strpos($linha, '❌') !== false) {
                echo "<span style='color: #f44336;'>$linha</span><br>";
            } elseif (strpos($linha, '===') !== false) {
                echo "<span style='color: #2196f3; font-weight: bold;'>$linha</span><br>";
            } else {
                echo htmlspecialchars($linha) . "<br>";
            }
        }
        echo "</div>";
    }
    
    // Erros (se houver)
    if (!empty($result['errors'])) {
        echo "<h2>❌ Erros Encontrados</h2>";
        echo "<div class='error'>";
        foreach ($result['errors'] as $erro) {
            echo "• " . htmlspecialchars($erro) . "<br>";
        }
        echo "</div>";
    }
    
    // Estatísticas
    echo "<h2>📊 Estatísticas</h2>";
    echo "<div class='info'>";
    echo "<strong>Tempo de execução:</strong> {$duration} segundos<br>";
    echo "<strong>Status:</strong> " . ($result['success'] ? '✅ Sucesso total' : '⚠️ Concluído com avisos') . "<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ ERRO CRÍTICO</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    
    echo "<div class='log-box'>";
    echo "Stack trace:<br>";
    echo nl2br(htmlspecialchars($e->getTraceAsString()));
    echo "</div>";
}

echo "<br><br>";
echo "<a href='/relatorios/index' class='btn'>← Voltar ao Dashboard</a>";
echo "<a href='?confirmar=sim' class='btn' style='background: #28a745;'>🔄 Executar Novamente</a>";

echo "</div></body></html>";
