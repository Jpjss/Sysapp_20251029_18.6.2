<?php
/**
 * Script para cadastrar usuário de cliente (não-admin)
 * Usuário terá acesso apenas à sua própria empresa
 */

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== CADASTRO DE USUÁRIO CLIENTE ===\n\n";

// Exemplo: criar usuário para a empresa "Empresa Padrão" (cd_empresa = 1)
$dados_usuario = [
    'nome' => 'João Silva',
    'login' => 'joao.silva',
    'email' => 'joao@empresapadrao.com.br',
    'senha' => '123456',  // Senha inicial simples
    'cd_empresa' => 1      // Empresa Padrão
];

echo "Criando usuário:\n";
echo "  Nome: {$dados_usuario['nome']}\n";
echo "  Login: {$dados_usuario['login']}\n";
echo "  Email: {$dados_usuario['email']}\n";
echo "  Senha: {$dados_usuario['senha']}\n";
echo "  Empresa ID: {$dados_usuario['cd_empresa']}\n\n";

// 1. Cria o usuário
$sql = "INSERT INTO sysapp_config_user 
        (nm_usuario, ds_login, ds_email, ds_senha, fg_ativo) 
        VALUES ($1, $2, $3, $4, 'S') 
        RETURNING cd_usuario";

$result = pg_query_params($conn, $sql, [
    $dados_usuario['nome'],
    $dados_usuario['login'],
    $dados_usuario['email'],
    $dados_usuario['senha']
]);

if (!$result) {
    echo "✗ Erro ao criar usuário: " . pg_last_error($conn) . "\n";
    exit;
}

$user = pg_fetch_assoc($result);
$cd_usuario = $user['cd_usuario'];

echo "✓ Usuário criado! ID: $cd_usuario\n\n";

// 2. Vincula à empresa (apenas UMA empresa)
$sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
        VALUES ($1, $2)";

$result = pg_query_params($conn, $sql, [$cd_usuario, $dados_usuario['cd_empresa']]);

if ($result) {
    echo "✓ Usuário vinculado à empresa ID {$dados_usuario['cd_empresa']}\n\n";
} else {
    echo "✗ Erro ao vincular empresa: " . pg_last_error($conn) . "\n";
}

// 3. Adiciona permissões básicas (sem acesso ao admin)
$permissoes_cliente = [
    'relatorios',    // Ver dashboard e relatórios
    'clientes',      // Gerenciar clientes
    'questionarios'  // Fazer questionários
    // NÃO inclui: 'admin', 'usuarios', 'empresas'
];

echo "Cadastrando permissões (cliente comum):\n";

foreach ($permissoes_cliente as $permissao) {
    $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
            VALUES ($1, $2)
            ON CONFLICT DO NOTHING";
    
    pg_query_params($conn, $sql, [$cd_usuario, $permissao]);
    echo "  ✓ $permissao\n";
}

echo "\n=== USUÁRIO CLIENTE CRIADO COM SUCESSO! ===\n\n";

echo "Credenciais de acesso:\n";
echo "  Login: {$dados_usuario['login']}\n";
echo "  Senha: {$dados_usuario['senha']}\n";
echo "  Empresa: ID {$dados_usuario['cd_empresa']}\n\n";

echo "Diferenças do Admin:\n";
echo "  ✓ Entra direto no sistema (sem escolher empresa)\n";
echo "  ✓ Vê apenas dados da sua empresa\n";
echo "  ✗ NÃO tem acesso ao menu Admin\n";
echo "  ✗ NÃO pode cadastrar usuários\n";
echo "  ✗ NÃO pode cadastrar empresas\n";

// Lista todos os usuários e suas empresas
echo "\n\n=== RESUMO DE TODOS OS USUÁRIOS ===\n\n";

$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_email,
               COUNT(ue.cd_empresa) as qtd_empresas,
               STRING_AGG(e.nm_empresa, ', ') as empresas
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        LEFT JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
        WHERE u.fg_ativo = 'S'
        GROUP BY u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_email
        ORDER BY u.cd_usuario";

$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    echo "ID: {$row['cd_usuario']}\n";
    echo "Nome: {$row['nm_usuario']}\n";
    echo "Login: {$row['ds_login']}\n";
    echo "Email: {$row['ds_email']}\n";
    echo "Empresas: {$row['qtd_empresas']} - {$row['empresas']}\n";
    echo "Tipo: " . ($row['qtd_empresas'] > 1 ? 'ADMIN (multi-empresas)' : 'CLIENTE (empresa única)') . "\n";
    echo "---\n";
}
