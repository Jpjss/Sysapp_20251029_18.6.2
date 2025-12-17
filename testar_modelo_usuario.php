<?php
require 'config/database.php';
require 'models/Usuario.php';

$Usuario = new Usuario();

echo "=== TESTANDO Usuario->findByLogin('teste') ===\n\n";

$result = $Usuario->findByLogin('teste');

echo "Resultado:\n";
print_r($result);

if ($result) {
    echo "\nEncontrou ID: " . $result['cd_usuario'] . "\n";
    
    // Agora busca dados completos
    echo "\n=== Buscando dados completos ===\n\n";
    $usuario = $Usuario->findForAuth($result['cd_usuario']);
    print_r($usuario);
}
