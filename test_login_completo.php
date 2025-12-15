<?php
session_start();

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'models/Usuario.php';

echo "=== SIMULAÇÃO COMPLETA DE LOGIN ===\n\n";

$_POST['email'] = 'admin';
$_POST['senha'] = 'admin';

$email = $_POST['email'];
$senha = $_POST['senha'];

echo "1. Tentando logar com:\n";
echo "   Email/Login: $email\n";
echo "   Senha: $senha\n\n";

$Usuario = new Usuario();

echo "2. Buscando usuário com findByLogin()...\n";
$configUser = $Usuario->findByLogin($email);

if ($configUser) {
    echo "   ✓ Usuário encontrado!\n";
    echo "   CD_USUARIO: {$configUser['cd_usuario']}\n\n";
} else {
    echo "   ✗ ERRO: Usuário não encontrado!\n";
    echo "   A query findByLogin() retornou vazio\n";
    exit;
}

$cd_usuario = $configUser['cd_usuario'];

echo "3. Buscando dados completos com findForAuth()...\n";
$usuario = $Usuario->findForAuth($cd_usuario);

if ($usuario) {
    echo "   ✓ Dados do usuário carregados!\n";
    echo "   Nome: {$usuario['nome_usuario']}\n";
    echo "   Senha no DB: {$usuario['senha_usuario']}\n\n";
} else {
    echo "   ✗ ERRO: findForAuth() não retornou dados\n";
    exit;
}

echo "4. Verificando senha...\n";
echo "   Senha digitada: $senha\n";
echo "   Senha no banco: {$usuario['senha_usuario']}\n\n";

$senhaCorreta = false;

// Texto plano
if ($senha === $usuario['senha_usuario']) {
    echo "   ✓ Senha CORRETA (texto plano)\n";
    $senhaCorreta = true;
}
// MD5
elseif (md5($senha) === $usuario['senha_usuario']) {
    echo "   ✓ Senha CORRETA (MD5)\n";
    $senhaCorreta = true;
}
// SHA1 com SALT
elseif (sha1(SECURITY_SALT . $senha) === $usuario['senha_usuario']) {
    echo "   ✓ Senha CORRETA (SHA1+SALT)\n";
    $senhaCorreta = true;
}
else {
    echo "   ✗ Senha INCORRETA\n";
}

if (!$senhaCorreta) {
    echo "\n=== LOGIN FALHARIA AQUI ===\n";
    exit;
}

echo "\n5. Buscando empresas do usuário...\n";
$empresas = $Usuario->getEmpresas($cd_usuario);

if (empty($empresas)) {
    echo "   ✗ ERRO: Usuário sem empresas!\n";
    exit;
} else {
    echo "   ✓ Empresas encontradas: " . count($empresas) . "\n";
    foreach ($empresas as $emp) {
        echo "     - Empresa ID: {$emp['cd_empresa']}\n";
    }
}

echo "\n=== LOGIN SERIA BEM-SUCEDIDO! ===\n";
