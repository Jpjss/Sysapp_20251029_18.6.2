<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TESTE CONTROLLER ===\n\n";

define('BASE_PATH', __DIR__);
define('BASE_URL', '/');

try {
    echo "1. Carregando Session...\n";
    require_once BASE_PATH . '/core/Session.php';
    Session::start();
    
    echo "2. Configurando sessão...\n";
    Session::write('Config.host', 'banco.propasso.systec.ftp.sh');
    Session::write('Config.database', 'bd_propasso');
    Session::write('Config.user', 'usr_propasso');
    Session::write('Config.password', 'SenhaDB_propasso1401');
    Session::write('Config.porta', '5432');
    Session::write('User.cd_usuario', 1);
    Session::write('User.nome_usuario', 'Administrador');
    
    echo "3. Carregando Database...\n";
    require_once BASE_PATH . '/config/database.php';
    
    echo "4. Carregando Controller base...\n";
    require_once BASE_PATH . '/core/Controller.php';
    
    echo "5. Instanciando Usuario...\n";
    require_once BASE_PATH . '/models/Usuario.php';
    $usuario = new Usuario();
    echo "   Usuario OK\n";
    
    echo "6. Instanciando Relatorio...\n";
    require_once BASE_PATH . '/models/Relatorio.php';
    $relatorio = new Relatorio();
    echo "   Relatorio OK\n";
    
    echo "7. Carregando RelatoriosController...\n";
    require_once BASE_PATH . '/controllers/RelatoriosController.php';
    echo "   Controller carregado\n";
    
    echo "8. Instanciando RelatoriosController...\n";
    $controller = new RelatoriosController();
    echo "   Controller instanciado OK\n";
    
    echo "9. Verificando método vendas_vendedor...\n";
    if (method_exists($controller, 'vendas_vendedor')) {
        echo "   Método existe!\n";
    } else {
        echo "   ERRO: Método não existe!\n";
    }
    
    echo "\n=== TESTE CONCLUÍDO COM SUCESSO ===\n";
    
} catch (Exception $e) {
    echo "\n!!! ERRO !!!\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "\n!!! ERRO FATAL !!!\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}
