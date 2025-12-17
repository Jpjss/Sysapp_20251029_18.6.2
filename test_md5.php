<?php
// Testando MD5
$hash = 'bba61214f102e337b6f3fb8a82692ae0';

$senhas = ['123', '123456', 'agape', 'admin', 'teste123', '...', '000', '0'];

foreach ($senhas as $senha) {
    $md5 = md5($senha);
    if ($md5 === $hash) {
        echo "✓ SENHA ENCONTRADA: '$senha'\n";
        echo "  MD5: $md5\n";
    }
}

// Gerar hash correto
echo "\n=== Hash correto para a senha '...' ===\n";
echo password_hash('...', PASSWORD_DEFAULT) . "\n";
