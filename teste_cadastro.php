<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Empresa.php';
require_once 'core/Security.php';
require_once 'core/Session.php';

Session::start();
Session::write('Questionarios.cd_usu', 1); // Simula usuário logado

$db = Database::getInstance();
$db->connect('localhost', 'sysapp', 'postgres', 'postgres', '5432');

$empresaModel = new Empresa($db);

// Simula dados do formulário
$dados = [
    'nome_empresa' => 'Teste Empresa Nova',
    'hostname' => 'localhost',
    'nome_banco' => 'erp_cliente_exemplo',
    'usuario_banco' => 'postgres',
    'senha_banco' => Security::encrypt('postgres'),
    'porta_banco' => '5432'
];

$nextId = $empresaModel->getNextCodigo();
echo "Tentando cadastrar empresa com ID: $nextId\n";

$dados['cd_empresa'] = $nextId;
$resultado = $empresaModel->salvar($dados);

if ($resultado) {
    echo "✅ Empresa cadastrada com sucesso!\n";

    // Vincula ao usuário
    $cd_usuario = 1;
    $sqlVincular = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) VALUES ($nextId, $cd_usuario)";
    $db->query($sqlVincular);

    // Dá todas as permissões
    $sqlInterfaces = "SELECT cd_interface FROM sysapp_interfaces";
    $interfaces = $db->fetchAll($sqlInterfaces);

    if ($interfaces) {
        foreach ($interfaces as $interface) {
            $cd_interface = (int)$interface['cd_interface'];
            $sqlPermissao = "INSERT INTO sysapp_config_user_empresas_interfaces (cd_empresa, cd_usuario, cd_interface) VALUES ($nextId, $cd_usuario, $cd_interface)";
            $db->query($sqlPermissao);
        }
    }

    echo "✅ Empresa vinculada ao usuário!\n";
} else {
    echo "❌ Falha ao cadastrar empresa!\n";
}
