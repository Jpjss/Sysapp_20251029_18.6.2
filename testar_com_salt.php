<?php
define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

$hash_original = 'bba61214f102e337b6f3fb8a82692ae0';

$senhas_teste = ['...', '12345', '123456', '1234', '123', '0', 'agape', 'admin', ''];

echo "=== Testando senhas COM SALT ===\n";
echo "SALT: " . SECURITY_SALT . "\n\n";

foreach ($senhas_teste as $senha) {
    $hash_com_salt = md5($senha . SECURITY_SALT);
    $match = ($hash_com_salt === $hash_original);
    
    if ($match) {
        echo "==========================================\n";
        echo "✓✓✓ SENHA ENCONTRADA: '$senha' ✓✓✓\n";
        echo "==========================================\n";
        echo "MD5(senha + SALT) = $hash_com_salt\n\n";
    }
}

// Testar sem achar
echo "\nTestando cada senha:\n";
foreach ($senhas_teste as $senha) {
    $hash_com_salt = md5($senha . SECURITY_SALT);
    echo "'$senha' -> $hash_com_salt " . ($hash_com_salt === $hash_original ? "✓ MATCH!" : "") . "\n";
}
