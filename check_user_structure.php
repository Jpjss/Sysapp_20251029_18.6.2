<?php
// Verificar estrutura de usuários no banco
$conn = pg_connect("host=localhost port=5432 dbname=sysapp user=postgres password=postgres");

if ($conn) {
    echo "=== VERIFICANDO TABELAS DE USUÁRIOS ===\n\n";
    
    // Lista todas as tabelas que contém 'user' ou 'usuario'
    $sql = "SELECT table_name FROM information_schema.tables 
            WHERE table_schema='public' 
            AND (table_name LIKE '%user%' OR table_name LIKE '%usuario%')
            ORDER BY table_name";
    $result = pg_query($conn, $sql);
    
    echo "Tabelas encontradas:\n";
    while ($row = pg_fetch_row($result)) {
        echo "  - {$row[0]}\n";
    }
    
    echo "\n=== ESTRUTURA DA TABELA sysapp_config_user ===\n";
    $sql = "SELECT column_name, data_type, character_maximum_length 
            FROM information_schema.columns 
            WHERE table_name = 'sysapp_config_user'
            ORDER BY ordinal_position";
    $result = pg_query($conn, $sql);
    
    while ($row = pg_fetch_assoc($result)) {
        $len = $row['character_maximum_length'] ? "({$row['character_maximum_length']})" : "";
        echo "  {$row['column_name']}: {$row['data_type']}$len\n";
    }
    
    echo "\n=== BUSCANDO USUÁRIO diaazze@sys.io ===\n";
    // Buscar em várias possíveis colunas
    $sql = "SELECT * FROM sysapp_config_user LIMIT 5";
    $result = pg_query($conn, $sql);
    
    if ($result && pg_num_rows($result) > 0) {
        echo "Primeiros 5 usuários:\n";
        while ($row = pg_fetch_assoc($result)) {
            print_r($row);
            echo "\n";
        }
    }
    
    // Verificar se existe view vw_login
    echo "\n=== VERIFICANDO VIEW vw_login ===\n";
    $sql = "SELECT * FROM vw_login LIMIT 3";
    $result = pg_query($conn, $sql);
    
    if ($result && pg_num_rows($result) > 0) {
        echo "Estrutura da view vw_login:\n";
        while ($row = pg_fetch_assoc($result)) {
            print_r($row);
            echo "\n";
        }
    }
    
    pg_close($conn);
} else {
    echo "Erro ao conectar!\n";
}
