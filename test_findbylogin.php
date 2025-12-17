<?php
require 'config/database.php';
require 'models/Usuario.php';

$Usuario = new Usuario();

echo "=== TESTE findByLogin('teste') ===\n\n";

$result = $Usuario->findByLogin('teste');

echo "Resultado: ";
print_r($result);

if ($result) {
    echo "\n\nEncontrou usuário ID: " . $result['cd_usuario'] . "\n";
    
    if ($result['cd_usuario'] == 4) {
        echo "✅ CORRETO! Encontrou o usuário teste (ID 4)\n";
    } else {
        echo "❌ ERRADO! Deveria encontrar ID 4, mas encontrou ID " . $result['cd_usuario'] . "\n";
    }
}
