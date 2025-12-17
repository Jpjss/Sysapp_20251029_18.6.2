<?php
// Descobrir qual senha gera o MD5 bba61214f102e337b6f3fb8a82692ae0
$hash_original = 'bba61214f102e337b6f3fb8a82692ae0';

$senhas_teste = [
    '...',
    '12345',
    '123456',
    '1234',
    '123',
    '0',
    '000',
    'agape',
    'admin',
    ''
];

echo "=== Testando senhas contra MD5: $hash_original ===\n\n";

foreach ($senhas_teste as $senha) {
    $md5 = md5($senha);
    $match = ($md5 === $hash_original);
    echo "Senha: '$senha'\n";
    echo "  MD5: $md5\n";
    echo "  Match: " . ($match ? "✓ SIM!" : "não") . "\n\n";
    
    if ($match) {
        echo "==========================================\n";
        echo "SENHA ENCONTRADA: '$senha'\n";
        echo "==========================================\n";
    }
}
