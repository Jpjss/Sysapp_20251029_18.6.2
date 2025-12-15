<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$db->connect();

echo "=== VERIFICANDO TABELAS DE USUÁRIO ===\n\n";

$tabelas = [
    'config_user_sys_app',
    'sysapp_config_user', 
    'usuario',
    'ctrl_usuario_sysapp'
];

foreach ($tabelas as $tabela) {
    $sql = "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = '$tabela') as existe";
    $result = pg_query($db->getConnection(), $sql);
    $existe = pg_fetch_assoc($result)['existe'] === 't';
    
    if ($existe) {
        echo "✓ $tabela: EXISTE\n";
        
        // Mostra estrutura
        $sqlCols = "SELECT column_name, data_type 
                    FROM information_schema.columns 
                    WHERE table_name = '$tabela' 
                    ORDER BY ordinal_position";
        $resultCols = pg_query($db->getConnection(), $sqlCols);
        
        echo "  Colunas:\n";
        while ($col = pg_fetch_assoc($resultCols)) {
            echo "    - {$col['column_name']} ({$col['data_type']})\n";
        }
        
        // Conta registros
        $sqlCount = "SELECT COUNT(*) as total FROM $tabela";
        $resultCount = pg_query($db->getConnection(), $sqlCount);
        $total = pg_fetch_assoc($resultCount)['total'];
        echo "  Total de registros: $total\n\n";
    } else {
        echo "✗ $tabela: NÃO EXISTE\n";
    }
}
