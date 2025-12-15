<?php
echo "Testando conexões ao PostgreSQL...\n\n";

$senhas = ['postgres', 'systec2011', 'admin', '123456', ''];
$host = 'localhost';
$port = '5432';
$dbname = 'sysapp';
$user = 'postgres';

foreach ($senhas as $senha) {
    echo "Testando senha: '" . ($senha ?: '(vazia)') . "' ... ";
    
    $conn = @pg_connect("host=$host port=$port dbname=$dbname user=$user password=$senha");
    
    if ($conn) {
        echo "✅ CONECTOU!\n";
        echo "\n=== SENHA CORRETA: '$senha' ===\n\n";
        
        // Testa uma query
        $result = pg_query($conn, "SELECT COUNT(*) FROM sysapp_config_user");
        if ($result) {
            $row = pg_fetch_row($result);
            echo "Usuários no banco: " . $row[0] . "\n";
        }
        
        pg_close($conn);
        break;
    } else {
        echo "❌ Falhou\n";
    }
}
