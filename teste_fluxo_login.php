<?php
// Teste simulando o que o método getEmpresasInfo faz
error_reporting(E_ALL);

// Simulando dados que deveriam vir do banco
$cd_usuario = 1;
$cd_empresas = '1';

// Simular resposta que o banco deveria retornar
$infoDb = [
    [
        'cd_empresa' => 1,
        'nome_empresa' => 'Empresa Padrão',
        'hostname_banco' => 'localhost',
        'nome_banco' => 'sysapp',
        'usuario_banco' => 'postgres',
        'senha_banco' => 'postgres',  // Criptografada, mas simulando
        'porta_banco' => '5432'
    ]
];

// Teste do código do login
echo "Count de empresas: " . count($infoDb) . "\n";

if (count($infoDb) > 1) {
    echo "Redirecionaria para: relatorios/empresa\n";
} elseif (count($infoDb) === 1) {
    echo "Redirecionaria para: relatorios/index\n";
    $empresa = $infoDb[0];
    echo "Config.database: " . $empresa['nome_banco'] . "\n";
    echo "Config.host: " . $empresa['hostname_banco'] . "\n";
    echo "Config.user: " . $empresa['usuario_banco'] . "\n";
} else {
    echo "Redirecionaria para: relatorios/index (sem empresas)\n";
}
