<?php
/**
 * DEBUG: Testa o que o controller está passando para a view
 */

// Simula o ambiente
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_PORT', '5432');

require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'models/Empresa.php';

$Usuario = new Usuario();
$Empresa = new Empresa();

echo "=== TESTE DE DADOS PARA O FORMULÁRIO ===\n\n";

// Buscar empresas disponíveis
echo "1. Empresas disponíveis:\n";
$empresas = $Empresa->listarTodas();

if (empty($empresas)) {
    echo "   ❌ NENHUMA EMPRESA!\n";
} else {
    echo "   ✅ " . count($empresas) . " empresas\n";
    foreach ($empresas as $emp) {
        echo "      - ID: {$emp['cd_empresa']} | {$emp['nm_empresa']}\n";
    }
}

// Buscar empresas do usuário (admin - ID 1)
echo "\n2. Empresas do usuário admin (ID 1):\n";
$cd_usuario = 1;
$empUsuario = $Usuario->getEmpresas($cd_usuario);
$empresas_usuario = [];

if (empty($empUsuario)) {
    echo "   ❌ Nenhuma empresa vinculada!\n";
} else {
    foreach ($empUsuario as $emp) {
        $empresas_usuario[] = $emp['cd_empresa'];
    }
    echo "   ✅ IDs: " . implode(', ', $empresas_usuario) . "\n";
}

// Buscar permissões do usuário
echo "\n3. Permissões do usuário admin (ID 1):\n";
$permUsuario = $Usuario->getPermissoes($cd_usuario);

echo "   Tipo retornado: " . gettype($permUsuario) . "\n";

if (is_string($permUsuario)) {
    echo "   ❌ getPermissoes retornou STRING ao invés de array!\n";
    echo "   Valor: $permUsuario\n";
    $permissoes_usuario = [];
} elseif (is_array($permUsuario)) {
    $permissoes_usuario = $permUsuario; // Já vem como array de strings
    if (empty($permissoes_usuario)) {
        echo "   ❌ Array vazio!\n";
    } else {
        echo "   ✅ Permissões: " . implode(', ', $permissoes_usuario) . "\n";
    }
} else {
    echo "   ❌ Tipo inesperado!\n";
    $permissoes_usuario = [];
}

echo "\n=== DADOS QUE SERIAM PASSADOS PARA A VIEW ===\n\n";
echo "Array empresas:\n";
var_dump($empresas);

echo "\nArray empresas_usuario:\n";
var_dump($empresas_usuario);

echo "\nArray permissoes_usuario:\n";
var_dump($permissoes_usuario);
