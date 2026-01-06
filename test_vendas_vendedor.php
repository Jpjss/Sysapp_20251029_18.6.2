<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TESTE VENDAS VENDEDOR ===\n\n";

// Simula ambiente
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['submit'] = 'visualizar';
$_POST['dt_inicio'] = '2025-10-01';
$_POST['dt_fim'] = '2025-10-07';
$_POST['vendedores'] = ['todos'];
$_POST['filiais'] = ['todas'];

try {
    echo "1. Incluindo bootstrap...\n";
    require_once __DIR__ . '/core/bootstrap.php';
    
    echo "2. Criando sessão fake...\n";
    Session::write('Config.host', 'banco.propasso.systec.ftp.sh');
    Session::write('Config.database', 'bd_propasso');
    Session::write('Config.user', 'usr_propasso');
    Session::write('Config.password', 'SenhaDB_propasso1401');
    Session::write('Config.porta', '5432');
    Session::write('User.cd_usuario', 1);
    Session::write('User.nome_usuario', 'Administrador');
    
    echo "3. Carregando model Relatorio...\n";
    require_once __DIR__ . '/models/Relatorio.php';
    $relatorio = new Relatorio();
    
    echo "4. Carregando model Usuario...\n";
    require_once __DIR__ . '/models/Usuario.php';
    $usuario = new Usuario();
    
    echo "5. Testando getVendedores...\n";
    $vendedores = $usuario->getVendedores();
    echo "   - Vendedores encontrados: " . count($vendedores) . "\n";
    
    echo "6. Testando getFiliais...\n";
    $filiais = $usuario->getFiliais();
    echo "   - Filiais encontradas: " . count($filiais) . "\n";
    
    echo "7. Testando getVendasPorVendedor...\n";
    $filtros = [
        'dt_inicio' => '2025-10-01',
        'dt_fim' => '2025-10-07',
        'vendedores' => ['todos'],
        'filiais' => ['todas']
    ];
    
    $resultado = $relatorio->getVendasPorVendedor($filtros);
    echo "   - Dados encontrados: " . count($resultado['dados']) . " filiais\n";
    echo "   - Total vendas: " . $resultado['totais']['total_vendas'] . "\n";
    echo "   - Valor total: R$ " . number_format($resultado['totais']['valor_total'], 2, ',', '.') . "\n";
    
    echo "\n=== TESTE CONCLUÍDO COM SUCESSO ===\n";
    
} catch (Exception $e) {
    echo "\n!!! ERRO !!!\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}
