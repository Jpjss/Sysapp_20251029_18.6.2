<?php
/**
 * Detector de Estrutura de Banco de Dados
 * 
 * Detecta automaticamente quais tabelas existem no banco conectado
 * e retorna a query apropriada para cada relatório.
 */
class DatabaseStructureDetector {
    
    private $db;
    private $structure = null;
    private $cacheFile = null;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Cache por banco (nome do banco na sessão)
        if (Session::check('Config.database')) {
            $dbName = Session::read('Config.database');
            $this->cacheFile = __DIR__ . '/../cache/db_structure_' . md5($dbName) . '.json';
        }
    }
    
    /**
     * Detecta e retorna a estrutura do banco atual
     */
    public function detectStructure() {
        // Verificar cache (válido por 1 hora)
        if ($this->cacheFile && file_exists($this->cacheFile)) {
            $cacheTime = filemtime($this->cacheFile);
            if (time() - $cacheTime < 3600) {
                $this->structure = json_decode(file_get_contents($this->cacheFile), true);
                return $this->structure;
            }
        }
        
        $this->structure = [
            'version' => $this->detectVersion(),
            'tables' => $this->detectTables(),
            'detected_at' => date('Y-m-d H:i:s')
        ];
        
        // Salvar cache
        if ($this->cacheFile) {
            $dir = dirname($this->cacheFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            file_put_contents($this->cacheFile, json_encode($this->structure));
        }
        
        return $this->structure;
    }
    
    /**
     * Detecta qual versão da estrutura está sendo usada
     */
    private function detectVersion() {
        $hasNewStructure = $this->tableExists('dm_orcamento_vendas_consolidadas');
        $hasOldStructure = $this->tableExists('ped_vd');
        
        if ($hasNewStructure) {
            return 'NEW'; // Estrutura nova (dm_*)
        } elseif ($hasOldStructure) {
            return 'OLD'; // Estrutura antiga (ped_vd, est_produto)
        }
        
        return 'UNKNOWN';
    }
    
    /**
     * Detecta quais tabelas existem
     */
    private function detectTables() {
        $tables = [
            // Tabelas novas
            'dm_orcamento_vendas_consolidadas' => false,
            'dm_orcamento_vendas_consolidadas_cubo' => false,
            'dm_produto' => false,
            'dm_estoque_atual' => false,
            
            // Tabelas antigas
            'ped_vd' => false,
            'ped_vd_produto_cpl_tamanho' => false,
            'est_produto' => false,
            'est_produto_cpl_tamanho' => false,
            'glb_pessoa' => false,
            
            // Tabelas comuns
            'prc_filial' => false
        ];
        
        foreach (array_keys($tables) as $tableName) {
            $tables[$tableName] = $this->tableExists($tableName);
        }
        
        return $tables;
    }
    
    /**
     * Verifica se uma tabela existe
     */
    private function tableExists($tableName) {
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = :table_name
        )";
        
        try {
            $result = $this->db->fetchOne($sql, [':table_name' => $tableName]);
            return $result && ($result['exists'] === 't' || $result['exists'] === true || $result['exists'] === '1');
        } catch (Exception $e) {
            error_log("Erro ao verificar tabela $tableName: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Retorna o nome da tabela de vendas disponível
     */
    public function getVendasTable() {
        $structure = $this->structure ?? $this->detectStructure();
        
        if ($structure['tables']['dm_orcamento_vendas_consolidadas']) {
            return 'dm_orcamento_vendas_consolidadas';
        } elseif ($structure['tables']['dm_orcamento_vendas_consolidadas_cubo']) {
            return 'dm_orcamento_vendas_consolidadas_cubo';
        } elseif ($structure['tables']['ped_vd']) {
            return 'ped_vd';
        }
        
        throw new Exception('Nenhuma tabela de vendas encontrada no banco de dados');
    }
    
    /**
     * Retorna o nome da tabela de produtos disponível
     */
    public function getProdutosTable() {
        $structure = $this->structure ?? $this->detectStructure();
        
        if ($structure['tables']['dm_produto']) {
            return 'dm_produto';
        } elseif ($structure['tables']['est_produto']) {
            return 'est_produto';
        }
        
        throw new Exception('Nenhuma tabela de produtos encontrada no banco de dados');
    }
    
    /**
     * Verifica se o banco usa estrutura nova
     */
    public function isNewStructure() {
        $structure = $this->structure ?? $this->detectStructure();
        return $structure['version'] === 'NEW';
    }
    
    /**
     * Verifica se o banco usa estrutura antiga
     */
    public function isOldStructure() {
        $structure = $this->structure ?? $this->detectStructure();
        return $structure['version'] === 'OLD';
    }
    
    /**
     * Limpa o cache de estrutura
     */
    public function clearCache() {
        if ($this->cacheFile && file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
        $this->structure = null;
    }
}
