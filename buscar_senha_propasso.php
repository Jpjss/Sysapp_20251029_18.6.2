<?php
/**
 * Busca senha do banco Propasso
 */

require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'core/Security.php';

Session::start();

$db = Database::getInstance();

// Conecta ao banco sysapp
$connected = $db->connect('localhost', 'sysapp', 'postgres', 'systec', 5432);

if (!$connected) {
    die("❌ Erro ao conectar ao banco sysapp\n");
}

echo "✅ Conectado ao banco sysapp\n\n";

// Busca dados do Propasso
$sql = "SELECT * FROM sysapp_config_empresas WHERE cd_empresa = 3";
$empresa = $db->fetchOne($sql);

if ($empresa) {
    echo "=== DADOS DO PROPASSO ===\n";
    echo "Nome: {$empresa['nm_empresa']}\n";
    echo "Host: {$empresa['ds_host']}\n";
    echo "Banco: {$empresa['ds_banco']}\n";
    echo "Usuário: {$empresa['ds_usuario']}\n";
    echo "Porta: {$empresa['ds_porta']}\n";
    echo "Senha Criptografada: {$empresa['ds_senha']}\n";
    
    $senha_descriptografada = Security::decrypt($empresa['ds_senha']);
    echo "\n✅ Senha Descriptografada: $senha_descriptografada\n";
} else {
    echo "❌ Empresa Propasso não encontrada\n";
}
