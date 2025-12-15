<?php
// Atualizar senha do admin
$conn = pg_connect("host=localhost port=5432 dbname=sysapp user=postgres password=postgres");

if ($conn) {
    $hashCorreto = md5('admin');
    
    $sql = "UPDATE sysapp_config_user SET ds_senha = '$hashCorreto' WHERE ds_login = 'admin'";
    $result = pg_query($conn, $sql);
    
    if ($result) {
        echo "✓ Senha do admin atualizada com sucesso!\n";
        echo "  Login: admin\n";
        echo "  Senha: admin\n";
        echo "  Hash: $hashCorreto\n";
    } else {
        echo "✗ Erro ao atualizar senha: " . pg_last_error($conn) . "\n";
    }
    
    pg_close($conn);
} else {
    echo "✗ Erro ao conectar ao banco!\n";
}
