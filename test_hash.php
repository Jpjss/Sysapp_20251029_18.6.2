<?php
define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

$senha = 'admin';
$hash = md5(SECURITY_SALT . $senha . SECURITY_SALT);

echo "Senha: $senha\n";
echo "Hash gerado: $hash\n";
echo "\n";

// Hash no banco
$hashBanco = '798037c15805d78a6e1c0247e7b63145';
echo "Hash no banco: $hashBanco\n";
echo "\n";

if ($hash === $hashBanco) {
    echo "✓ Hash CORRETO! Senha 'admin' funciona.\n";
} else {
    echo "✗ Hash DIFERENTE! Testando outras senhas...\n\n";
    
    // Testa outras senhas comuns
    $senhas = ['123456', 'admin123', 'Admin', 'ADMIN', ''];
    
    foreach ($senhas as $s) {
        $h = md5(SECURITY_SALT . $s . SECURITY_SALT);
        echo "Senha '$s': $h ";
        if ($h === $hashBanco) {
            echo "✓ ENCONTRADA!\n";
            break;
        } else {
            echo "\n";
        }
    }
}
