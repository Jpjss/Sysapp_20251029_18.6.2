<?php
require_once 'core/Security.php';

echo "=== EXPLORANDO TABELAS RELACIONADAS A PRODUTOS, ESTOQUE E VENDAS ===\n\n";

// Conectar ao banco sysapp para pegar as empresas
try {
    $dsn = "pgsql:host=localhost;port=5432;dbname=sysapp";
    $pdo = new PDO($dsn, 'postgres', 'postgres');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, ds_senha, ds_porta FROM sysapp_config_empresas ORDER BY cd_empresa");
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($empresas as $empresa) {
        echo "========================================\n";
        echo "EMPRESA: {$empresa['nm_empresa']} (ID: {$empresa['cd_empresa']})\n";
        echo "BANCO: {$empresa['ds_banco']}\n";
        echo "========================================\n\n";
        
        try {
            // Conectar ao banco da empresa
            $senha_descriptografada = Security::decrypt($empresa['ds_senha']);
            $dsn = "pgsql:host={$empresa['ds_host']};port={$empresa['ds_porta']};dbname={$empresa['ds_banco']}";
            $pdo_empresa = new PDO($dsn, $empresa['ds_usuario'], $senha_descriptografada);
            $pdo_empresa->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Buscar todas as tabelas
            $sql = "SELECT table_name 
                    FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_type = 'BASE TABLE'
                    ORDER BY table_name";
            
            $stmt = $pdo_empresa->query($sql);
            $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Categorizar tabelas por tipo
            $categorias = [
                'PRODUTOS' => [],
                'ESTOQUE' => [],
                'VENDAS' => [],
                'MARCAS/MODELOS' => [],
                'TAMANHOS/GRADES' => [],
                'MOVIMENTAÇÕES' => [],
                'OUTRAS RELACIONADAS' => []
            ];
            
            foreach ($tabelas as $tabela) {
                $tabela_lower = strtolower($tabela);
                
                if (preg_match('/produto|item|artigo|mercadoria/i', $tabela)) {
                    $categorias['PRODUTOS'][] = $tabela;
                } elseif (preg_match('/estoque|saldo|inventario/i', $tabela)) {
                    $categorias['ESTOQUE'][] = $tabela;
                } elseif (preg_match('/vend|pedido|nota|nf|cupom|ordem/i', $tabela)) {
                    $categorias['VENDAS'][] = $tabela;
                } elseif (preg_match('/marca|modelo|fabricante/i', $tabela)) {
                    $categorias['MARCAS/MODELOS'][] = $tabela;
                } elseif (preg_match('/tamanho|grade|cor|variacao|variante/i', $tabela)) {
                    $categorias['TAMANHOS/GRADES'][] = $tabela;
                } elseif (preg_match('/moviment|entrada|saida|transf/i', $tabela)) {
                    $categorias['MOVIMENTAÇÕES'][] = $tabela;
                } elseif (preg_match('/cat|grup|fam|linha|secao|depart|unid|preco|custo/i', $tabela)) {
                    $categorias['OUTRAS RELACIONADAS'][] = $tabela;
                }
            }
            
            // Exibir tabelas por categoria
            foreach ($categorias as $categoria => $tabelas_cat) {
                if (!empty($tabelas_cat)) {
                    echo "--- $categoria ---\n";
                    foreach ($tabelas_cat as $tab) {
                        echo "  • $tab\n";
                        
                        // Buscar colunas da tabela
                        $sql_cols = "SELECT column_name, data_type, character_maximum_length 
                                    FROM information_schema.columns 
                                    WHERE table_name = :table_name 
                                    ORDER BY ordinal_position";
                        $stmt_cols = $pdo_empresa->prepare($sql_cols);
                        $stmt_cols->execute(['table_name' => $tab]);
                        $colunas = $stmt_cols->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($colunas as $col) {
                            $tipo = $col['data_type'];
                            if ($col['character_maximum_length']) {
                                $tipo .= "({$col['character_maximum_length']})";
                            }
                            echo "      - {$col['column_name']} ({$tipo})\n";
                        }
                        echo "\n";
                    }
                    echo "\n";
                }
            }
            
            echo "Total de tabelas encontradas: " . count($tabelas) . "\n\n";
            
        } catch (Exception $e) {
            echo "ERRO ao conectar ao banco {$empresa['ds_banco']}: {$e->getMessage()}\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: {$e->getMessage()}\n";
}

echo "\n=== EXPLORAÇÃO CONCLUÍDA ===\n";
?>
