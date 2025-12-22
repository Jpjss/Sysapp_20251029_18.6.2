<?php
/**
 * Wrapper do index.php com tratamento de erros
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Captura erros PHP
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #ffcccc; padding: 10px; margin: 10px; border: 1px solid red;'>";
    echo "<strong>ERRO PHP:</strong><br>";
    echo "Tipo: $errno<br>";
    echo "Mensagem: $errstr<br>";
    echo "Arquivo: $errfile (linha $errline)<br>";
    echo "</div>";
});

// Captura erros de conexão
try {
    require_once 'index.php';
} catch (Exception $e) {
    echo "<div style='background: #ffcccc; padding: 10px; margin: 10px; border: 1px solid red;'>";
    echo "<strong>EXCEÇÃO:</strong><br>";
    echo $e->getMessage() . "<br>";
    echo $e->getFile() . " (linha " . $e->getLine() . ")<br>";
    echo "</div>";
}
?>
