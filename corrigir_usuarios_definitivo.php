<?php
/**
 * CORREÇÃO DEFINITIVA DE TODOS OS USUÁRIOS
 */

require_once 'config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== CORREÇÃO DEFINITIVA DO SISTEMA ===\n\n";

// 1. Pega a primeira empresa ativa
$sql = "SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas WHERE fg_ativo = 'S' ORDER BY cd_empresa LIMIT 1";
$result = pg_query($conn, $sql);
$empresaPadrao = pg_fetch_assoc($result);

echo "Empresa padrão: {$empresaPadrao['nm_empresa']} (ID: {$empresaPadrao['cd_empresa']})\n\n";

// 2. ATIVA todos os usuários
echo "1. Ativando todos os usuários...\n";
$sql = "UPDATE sysapp_config_user SET fg_ativo = 'S'";
pg_query($conn, $sql);
echo "   ✅ Todos os usuários ativados\n\n";

// 3. Vincula TODOS os usuários à empresa padrão (se não tiver)
echo "2. Vinculando usuários sem empresa...\n";

$sql = "SELECT u.cd_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        WHERE ue.cd_empresa IS NULL";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    $sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
            VALUES ({$row['cd_usuario']}, {$empresaPadrao['cd_empresa']})
            ON CONFLICT (cd_usuario, cd_empresa) DO NOTHING";
    pg_query($conn, $sql);
    echo "   ✅ Vinculou '{$row['ds_login']}' à '{$empresaPadrao['nm_empresa']}'\n";
}

// 4. Dá permissões básicas para quem não tem
echo "\n3. Adicionando permissões...\n";

$permissoesPadrao = ['relatorios', 'clientes', 'questionarios'];

$sql = "SELECT u.cd_usuario, u.ds_login
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_interfaces ui ON u.cd_usuario = ui.cd_usuario
        WHERE ui.nm_interface IS NULL";
$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    foreach ($permissoesPadrao as $perm) {
        $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
                VALUES ({$row['cd_usuario']}, '$perm')
                ON CONFLICT (cd_usuario, nm_interface) DO NOTHING";
        pg_query($conn, $sql);
    }
    echo "   ✅ Permissões básicas para '{$row['ds_login']}'\n";
}

// 5. Configura senha simples para diaazze
echo "\n4. Resetando senha do usuário diaazze...\n";
$sql = "UPDATE sysapp_config_user SET ds_senha = 'diaazze123' WHERE ds_login = 'diaazze'";
pg_query($conn, $sql);
echo "   ✅ Senha alterada para: diaazze123\n";

// 6. Mostra resumo final
echo "\n" . str_repeat("=", 60) . "\n";
echo "CREDENCIAIS FINAIS:\n";
echo str_repeat("=", 60) . "\n\n";

$sql = "SELECT u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_senha, u.fg_ativo,
               STRING_AGG(DISTINCT e.nm_empresa, ', ') as empresas,
               STRING_AGG(DISTINCT ui.nm_interface, ', ') as permissoes
        FROM sysapp_config_user u
        LEFT JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
        LEFT JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
        LEFT JOIN sysapp_config_user_interfaces ui ON u.cd_usuario = ui.cd_usuario
        GROUP BY u.cd_usuario, u.nm_usuario, u.ds_login, u.ds_senha, u.fg_ativo
        ORDER BY u.cd_usuario";

$result = pg_query($conn, $sql);

while ($row = pg_fetch_assoc($result)) {
    $status = $row['fg_ativo'] === 'S' ? '✅' : '❌';
    echo "┌─────────────────────────────────────────────────────────\n";
    echo "│ $status {$row['nm_usuario']}\n";
    echo "├─────────────────────────────────────────────────────────\n";
    echo "│  Login: {$row['ds_login']}\n";
    echo "│  Senha: {$row['ds_senha']}\n";
    echo "│  Empresas: {$row['empresas']}\n";
    echo "│  Permissões: {$row['permissoes']}\n";
    echo "└─────────────────────────────────────────────────────────\n\n";
}

echo "=== SISTEMA CORRIGIDO! ===\n";
echo "\nAgora feche o navegador e faça login:\n";
echo "• admin / admin\n";
echo "• diaazze / diaazze123\n";
echo "• joao.silva / 123456\n";
