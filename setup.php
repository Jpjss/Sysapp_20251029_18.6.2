<?php
/**
 * Script de setup rápido - Cria usuário admin e configura tudo
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Security.php';

$db = Database::getInstance();
$response = [];

echo "<html><head><meta charset='UTF-8'><title>Setup SysApp</title></head><body>";
echo "<style>body { font-family: Arial; max-width: 800px; margin: 50px auto; } .ok { color: green; } .err { color: red; } .info { color: blue; }</style>";
echo "<h1>Setup do Sistema SysApp</h1>";

// 1. Criar usuário admin
echo "<h2>1. Usuário Admin</h2>";

$result = $db->fetchOne("SELECT COUNT(*) as total FROM sysapp_config_user WHERE ds_login = 'admin'");
$admin_existe = (int)$result['total'] > 0;

if ($admin_existe) {
    echo "<p class='ok'>✅ Usuário admin já existe</p>";
} else {
    $senha_hash = md5('admin123' . SECURITY_SALT);
    $sql = "INSERT INTO sysapp_config_user (cd_usuario, nm_usuario, ds_login, ds_senha, ds_email, fg_ativo) 
            VALUES (1, 'Administrador', 'admin', '$senha_hash', 'admin@sysapp.com', 'S')";
    
    if ($db->query($sql)) {
        echo "<p class='ok'>✅ Usuário admin criado com sucesso!</p>";
        echo "<p class='info'>Login: <strong>admin</strong><br>Senha: <strong>admin123</strong></p>";
        $admin_existe = true;
    } else {
        echo "<p class='err'>❌ Erro ao criar usuário admin</p>";
        echo "<pre>" . pg_last_error($db->getConnection()) . "</pre>";
    }
}

// 2. Listar todas as empresas
echo "<h2>2. Empresas no Sistema</h2>";

$empresas = $db->fetchAll("SELECT cd_empresa, nm_empresa FROM sysapp_config_empresas LIMIT 10");

if (empty($empresas)) {
    echo "<p class='err'>⚠️ Nenhuma empresa encontrada no banco</p>";
} else {
    echo "<p class='ok'>✅ " . count($empresas) . " empresa(s) encontrada(s):</p>";
    echo "<ul>";
    foreach ($empresas as $emp) {
        echo "<li>" . htmlspecialchars($emp['nm_empresa']) . " (CD: {$emp['cd_empresa']})</li>";
    }
    echo "</ul>";
}

// 3. Vincular admin às empresas
if ($admin_existe && !empty($empresas)) {
    echo "<h2>3. Vinculando Admin às Empresas</h2>";
    
    // Remove vinculações antigas
    $db->query("DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = 1");
    $db->query("DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = 1");
    
    $vinculados = 0;
    foreach ($empresas as $emp) {
        $cd_empresa = (int)$emp['cd_empresa'];
        
        // Vincula empresa
        $sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) VALUES (1, $cd_empresa)";
        if ($db->query($sql)) {
            $vinculados++;
        }
        
        // Vincula todas as interfaces
        $interfaces = $db->fetchAll("SELECT cd_interface FROM sysapp_interfaces");
        foreach ($interfaces as $iface) {
            $cd_interface = (int)$iface['cd_interface'];
            $sql = "INSERT INTO sysapp_config_user_empresas_interfaces (cd_usuario, cd_empresa, cd_interface) 
                    VALUES (1, $cd_empresa, $cd_interface)";
            @$db->query($sql);
        }
    }
    
    echo "<p class='ok'>✅ Admin vinculado a $vinculados empresa(s)</p>";
}

// 4. Resumo final
echo "<h2>Resumo</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Status:</strong> Sistema pronto para uso ✅</p>";
echo "<p><strong>Login:</strong> admin</p>";
echo "<p><strong>Senha:</strong> admin123</p>";
echo "<p><a href='/usuarios/login' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Ir para Login</a></p>";
echo "</div>";

echo "</body></html>";
?>
