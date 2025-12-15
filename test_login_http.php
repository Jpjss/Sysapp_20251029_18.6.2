<?php
// Teste de login via HTTP POST
$url = 'http://localhost:8000/usuarios/login';
$data = [
    'email' => 'admin',
    'senha' => 'admin'
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($data),
        'follow_location' => false
    ]
];

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

echo "=== TESTE DE LOGIN HTTP ===\n\n";
echo "URL: $url\n";
echo "Dados: email=admin, senha=admin\n\n";

if ($result === false) {
    echo "✗ Erro ao fazer requisição\n";
    print_r($http_response_header);
} else {
    echo "✓ Requisição realizada com sucesso!\n\n";
    echo "Headers de resposta:\n";
    foreach ($http_response_header as $header) {
        echo "  $header\n";
        
        // Verifica redirecionamento
        if (stripos($header, 'Location:') === 0) {
            $location = trim(substr($header, 9));
            echo "\n✓ REDIRECIONADO PARA: $location\n";
        }
    }
    
    // Mostra parte do corpo da resposta
    if (strlen($result) < 500) {
        echo "\nResposta:\n";
        echo substr($result, 0, 500);
    }
}
