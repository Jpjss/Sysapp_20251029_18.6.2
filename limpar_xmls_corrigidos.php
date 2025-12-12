
<?php
/**
 * Script para limpar XMLs corrigidos do diretório temporário
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Diretórios de XMLs
$xmlTempDir = 'C:/systec/xmls corrigidos/';
$xmlTestDir = __DIR__ . '/public/test_xmls/';

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Limpeza de XMLs Corrigidos</title>";
echo "<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }
    .container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        overflow: hidden;
    }
    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .header h1 { font-size: 28px; margin-bottom: 10px; }
    .header p { opacity: 0.9; }
    .content {
        padding: 30px;
    }
    .directory-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .directory-section h2 {
        color: #333;
        font-size: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .directory-section h2::before {
        content: '📁';
        font-size: 24px;
    }
    .file-list {
        list-style: none;
        margin: 15px 0;
    }
    .file-item {
        background: white;
        padding: 10px 15px;
        margin-bottom: 8px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 3px solid #667eea;
    }
    .file-name { 
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #333;
    }
    .file-size {
        color: #666;
        font-size: 12px;
    }
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }
    .stat-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border: 2px solid #667eea;
    }
    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #667eea;
    }
    .stat-label {
        color: #666;
        font-size: 13px;
        margin-top: 5px;
    }
    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    .btn {
        flex: 1;
        min-width: 200px;
        padding: 15px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }
    .btn-delete {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
    }
    .btn-all {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .btn-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(250, 112, 154, 0.4);
    }
    .btn-back {
        background: #6c757d;
        color: white;
    }
    .btn-back:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }
    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    .empty-state::before {
        content: '📭';
        font-size: 60px;
        display: block;
        margin-bottom: 15px;
    }
</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🗑️ Limpeza de XMLs Corrigidos</h1>";
echo "<p>Gerencie os arquivos XML processados</p>";
echo "</div>";
echo "<div class='content'>";

// Verifica se foi solicitada exclusão
$action = $_GET['action'] ?? '';

if ($action === 'delete_temp') {
    echo "<div class='alert alert-success'>";
    echo "<strong>✅ Exclusão em andamento...</strong><br>";
    
    $files = glob($xmlTempDir . '*.xml');
    $deleted = 0;
    
    foreach ($files as $file) {
        if (unlink($file)) {
            $deleted++;
            echo "✓ " . basename($file) . " excluído<br>";
        }
    }
    
    echo "<br><strong>Total: $deleted arquivo(s) excluído(s) com sucesso!</strong>";
    echo "</div>";
    
} elseif ($action === 'delete_all') {
    echo "<div class='alert alert-success'>";
    echo "<strong>✅ Exclusão completa em andamento...</strong><br><br>";
    
    // Limpa C:\systec\xmls corrigidos
    $tempFiles = glob($xmlTempDir . '*.xml');
    $tempDeleted = 0;
    
    if (!empty($tempFiles)) {
        echo "<br><strong>📁 C:\\systec\\xmls corrigidos\\</strong><br>";
        foreach ($tempFiles as $file) {
            if (unlink($file)) {
                $tempDeleted++;
                echo "✓ " . basename($file) . "<br>";
            }
        }
    }
    
    // Limpa test_xmls
    $testFiles = glob($xmlTestDir . '*.xml');
    $testDeleted = 0;
    
    if (!empty($testFiles)) {
        echo "<br><strong>📁 test_xmls/</strong><br>";
        foreach ($testFiles as $file) {
            if (unlink($file)) {
                $testDeleted++;
                echo "✓ " . basename($file) . "<br>";
            }
        }
    }
    
    $totalDeleted = $tempDeleted + $testDeleted;
    echo "<br><strong>Total: $totalDeleted arquivo(s) excluído(s) com sucesso!</strong>";
    echo "</div>";
}

// Lista arquivos em C:\systec\xmls corrigidos
echo "<div class='directory-section'>";
echo "<h2>XMLs Corrigidos (C:\\systec\\xmls corrigidos\\)</h2>";

$tempFiles = glob($xmlTempDir . '*.xml');

if (!empty($tempFiles)) {
    $totalSize = 0;
    
    echo "<div class='stats'>";
    echo "<div class='stat-box'>";
    echo "<div class='stat-value'>" . count($tempFiles) . "</div>";
    echo "<div class='stat-label'>Arquivos</div>";
    echo "</div>";
    
    foreach ($tempFiles as $file) {
        $totalSize += filesize($file);
    }
    
    echo "<div class='stat-box'>";
    echo "<div class='stat-value'>" . number_format($totalSize / 1024 / 1024, 2) . " MB</div>";
    echo "<div class='stat-label'>Tamanho Total</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<ul class='file-list'>";
    foreach ($tempFiles as $file) {
        $size = filesize($file);
        $sizeStr = $size < 1024 ? $size . ' B' : 
                   ($size < 1048576 ? number_format($size / 1024, 2) . ' KB' : 
                   number_format($size / 1048576, 2) . ' MB');
        
        echo "<li class='file-item'>";
        echo "<span class='file-name'>" . basename($file) . "</span>";
        echo "<span class='file-size'>" . $sizeStr . "</span>";
        echo "</li>";
    }
    echo "</ul>";
    
    echo "<a href='?action=delete_temp' class='btn btn-delete' onclick=\"return confirm('⚠️ Tem certeza que deseja excluir todos os XMLs da pasta C:\\\\systec\\\\xmls corrigidos?');\">🗑️ Excluir XMLs Corrigidos</a>";
    
} else {
    echo "<div class='empty-state'>";
    echo "<p>Nenhum arquivo XML encontrado nesta pasta</p>";
    echo "</div>";
}

echo "</div>";

// Lista arquivos em test_xmls
echo "<div class='directory-section'>";
echo "<h2>XMLs de Teste (test_xmls/)</h2>";

$testFiles = glob($xmlTestDir . '*.xml');

if (!empty($testFiles)) {
    $totalSize = 0;
    
    echo "<div class='stats'>";
    echo "<div class='stat-box'>";
    echo "<div class='stat-value'>" . count($testFiles) . "</div>";
    echo "<div class='stat-label'>Arquivos</div>";
    echo "</div>";
    
    foreach ($testFiles as $file) {
        $totalSize += filesize($file);
    }
    
    echo "<div class='stat-box'>";
    echo "<div class='stat-value'>" . number_format($totalSize / 1024 / 1024, 2) . " MB</div>";
    echo "<div class='stat-label'>Tamanho Total</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<ul class='file-list'>";
    foreach ($testFiles as $file) {
        $size = filesize($file);
        $sizeStr = $size < 1024 ? $size . ' B' : 
                   ($size < 1048576 ? number_format($size / 1024, 2) . ' KB' : 
                   number_format($size / 1048576, 2) . ' MB');
        
        echo "<li class='file-item'>";
        echo "<span class='file-name'>" . basename($file) . "</span>";
        echo "<span class='file-size'>" . $sizeStr . "</span>";
        echo "</li>";
    }
    echo "</ul>";
    
} else {
    echo "<div class='empty-state'>";
    echo "<p>Nenhum arquivo XML encontrado nesta pasta</p>";
    echo "</div>";
}

echo "</div>";

// Informações gerais
$totalFiles = count($tempFiles) + count($testFiles);

if ($totalFiles > 0) {
    echo "<div class='alert alert-info'>";
    echo "<strong>ℹ️ Informação:</strong><br>";
    echo "Total de <strong>$totalFiles arquivo(s) XML</strong> encontrado(s) no sistema.";
    echo "</div>";
}

// Botões de ação
echo "<div class='button-group'>";

if ($totalFiles > 0) {
    echo "<a href='?action=delete_all' class='btn btn-all' onclick=\"return confirm('⚠️⚠️⚠️ ATENÇÃO!\\n\\nIsso vai excluir TODOS os XMLs de TODAS as pastas (C:\\\\systec\\\\xmls corrigidos + test_xmls).\\n\\nDeseja continuar?');\">🔥 Excluir TODOS os XMLs</a>";
}

echo "<a href='http://localhost:8000/xml' class='btn btn-back'>← Voltar ao Sistema</a>";
echo "</div>";

echo "</div>";
echo "</div>";
echo "</body>";
echo "</html>";
