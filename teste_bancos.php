<?php
echo "=== TESTE DE BANCOS ===\n\n";

// Testa banco sysapp
echo "1. Testando banco SYSAPP:\n";
$conn_sysapp = @pg_connect('host=localhost port=5432 dbname=sysapp user=postgres password=postgres');
if ($conn_sysapp) {
    echo "   ✅ Conectado!\n";
    $res = pg_query($conn_sysapp, 'SELECT COUNT(*) as total FROM sysapp_config_empresas');
    if ($res) {
        $row = pg_fetch_assoc($res);
        echo "   Total de empresas: " . $row['total'] . "\n";
        
        // Lista empresas
        $res2 = pg_query($conn_sysapp, 'SELECT cd_empresa, nome_empresa, hostname_banco, nome_banco FROM sysapp_config_empresas ORDER BY cd_empresa');
        while ($emp = pg_fetch_assoc($res2)) {
            echo "   - [{$emp['cd_empresa']}] {$emp['nome_empresa']} -> {$emp['hostname_banco']}/{$emp['nome_banco']}\n";
        }
    } else {
        echo "   ❌ Erro ao consultar: " . pg_last_error($conn_sysapp) . "\n";
    }
    pg_close($conn_sysapp);
} else {
    echo "   ❌ Erro ao conectar ao sysapp\n";
}

echo "\n2. Testando banco BD_PROPASSO:\n";
$conn_propasso = @pg_connect('host=banco.propasso.systec.ftp.sh port=5432 dbname=bd_propasso user=admin password=systec2011.');
if ($conn_propasso) {
    echo "   ✅ Conectado!\n";
    $res = pg_query($conn_propasso, 'SELECT COUNT(*) as total FROM sysapp_config_empresas');
    if ($res) {
        $row = pg_fetch_assoc($res);
        echo "   Total de empresas no bd_propasso: " . $row['total'] . "\n";
        
        // Lista empresas
        $res2 = pg_query($conn_propasso, 'SELECT cd_empresa, nome_empresa, hostname_banco, nome_banco FROM sysapp_config_empresas ORDER BY cd_empresa');
        while ($emp = pg_fetch_assoc($res2)) {
            echo "   - [{$emp['cd_empresa']}] {$emp['nome_empresa']} -> {$emp['hostname_banco']}/{$emp['nome_banco']}\n";
        }
    } else {
        echo "   ❌ Tabela não existe no bd_propasso\n";
    }
    pg_close($conn_propasso);
} else {
    echo "   ❌ Erro ao conectar ao bd_propasso\n";
}
?>
