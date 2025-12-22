<?php
echo "<h1>Teste Simples</h1>";
echo "PHP está funcionando!<br>";

// Tenta conexão ao banco
try {
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/config/database.php';
    
    $db = Database::getInstance();
    echo "✓ Banco de dados conectado<br>";
    
    // Testa query simples
    $sql = "SELECT cd_usuario, nm_usuario, ds_login FROM sysapp_config_user LIMIT 1";
    $result = $db->fetchAll($sql);
    
    echo "Usuários no banco:<br>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "<br>";
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
