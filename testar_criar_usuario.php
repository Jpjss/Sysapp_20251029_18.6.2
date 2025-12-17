<?php
/**
 * TESTE: Criar um novo usuário completo
 */

define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_PORT', '5432');

require_once 'config/database.php';
require_once 'models/Usuario.php';

$Usuario = new Usuario();

echo "=== TESTE DE CRIAÇÃO DE USUÁRIO ===\n\n";

// Dados do novo usuário
$dados = [
    'nome_usuario' => 'Teste Usuario',
    'ds_login' => 'teste',
    'ds_email' => 'teste@sys.io',
    'senha_usuario' => 'teste123',
    'fg_ativo' => 'S'
];

echo "1. Criando usuário...\n";
$cd_usuario = $Usuario->create($dados);

if ($cd_usuario) {
    echo "   ✅ Usuário criado! ID: $cd_usuario\n\n";
    
    echo "2. Vinculando à empresa padrão (ID: 1)...\n";
    $Usuario->atualizarEmpresas($cd_usuario, [1]);
    echo "   ✅ Empresa vinculada\n\n";
    
    echo "3. Adicionando permissões...\n";
    $Usuario->atualizarPermissoes($cd_usuario, ['relatorios', 'clientes']);
    echo "   ✅ Permissões adicionadas\n\n";
    
    echo "4. Verificando dados finais...\n";
    $user = $Usuario->findById($cd_usuario);
    print_r($user);
    
    echo "\n5. Empresas:\n";
    $empresas = $Usuario->getEmpresas($cd_usuario);
    print_r($empresas);
    
    echo "\n6. Permissões:\n";
    $perms = $Usuario->getPermissoes($cd_usuario);
    print_r($perms);
    
    echo "\n\n=== CREDENCIAIS PARA LOGIN ===\n";
    echo "Login: teste\n";
    echo "Senha: teste123\n";
    
} else {
    echo "   ❌ ERRO ao criar usuário!\n";
}
