<?php
/**
 * Helper para trabalhar com diferentes estruturas de banco de dados
 * Detecta colunas disponíveis e constrói queries dinamicamente
 */

class DatabaseHelper {
    private $db;
    private static $columnCache = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Retorna todas as colunas de uma tabela
     * Cache para evitar múltiplas consultas
     */
    public function getTableColumns($tableName) {
        // Verifica cache
        $cacheKey = $this->db->getDatabase() . '.' . $tableName;
        
        if (isset(self::$columnCache[$cacheKey])) {
            return self::$columnCache[$cacheKey];
        }
        
        $sql = "SELECT column_name, data_type, is_nullable 
                FROM information_schema.columns 
                WHERE table_name = '" . $this->db->escape($tableName) . "' 
                ORDER BY ordinal_position";
        
        $result = $this->db->fetchAll($sql);
        
        $columns = [];
        if ($result) {
            foreach ($result as $row) {
                $columns[$row['column_name']] = [
                    'type' => $row['data_type'],
                    'nullable' => $row['is_nullable'] === 'YES'
                ];
            }
        }
        
        // Armazena em cache
        self::$columnCache[$cacheKey] = $columns;
        
        return $columns;
    }
    
    /**
     * Verifica se uma coluna existe em uma tabela
     */
    public function columnExists($tableName, $columnName) {
        $columns = $this->getTableColumns($tableName);
        return isset($columns[$columnName]);
    }
    
    /**
     * Retorna a primeira coluna disponível de uma lista de opções
     * Útil para campos que podem ter nomes diferentes em bancos diferentes
     */
    public function getAvailableColumn($tableName, array $columnOptions) {
        $columns = $this->getTableColumns($tableName);
        
        foreach ($columnOptions as $option) {
            if (isset($columns[$option])) {
                return $option;
            }
        }
        
        return null;
    }
    
    /**
     * Constrói SELECT dinâmico baseado em mapeamento de colunas
     * 
     * @param string $tableName Nome da tabela
     * @param array $columnMap Mapeamento: ['alias' => ['opcao1', 'opcao2', ...]]
     * @param string $defaultValue Valor padrão quando coluna não existe
     * @return string SELECT clause
     */
    public function buildDynamicSelect($tableName, array $columnMap, $defaultValue = "''") {
        $columns = $this->getTableColumns($tableName);
        $selectParts = [];
        
        foreach ($columnMap as $alias => $options) {
            $foundColumn = null;
            
            // Se options é string, converte para array
            if (is_string($options)) {
                $options = [$options];
            }
            
            // Procura primeira coluna disponível
            foreach ($options as $option) {
                if (isset($columns[$option])) {
                    $foundColumn = $option;
                    break;
                }
            }
            
            if ($foundColumn) {
                if ($foundColumn === $alias) {
                    // Sem necessidade de alias
                    $selectParts[] = $foundColumn;
                } else {
                    // Com alias
                    $selectParts[] = "$foundColumn as $alias";
                }
            } else {
                // Coluna não existe, usa valor padrão
                $selectParts[] = "$defaultValue as $alias";
            }
        }
        
        return implode(', ', $selectParts);
    }
    
    /**
     * Constrói WHERE clause dinâmica para busca
     * Só inclui colunas que existem na tabela
     */
    public function buildSearchWhere($tableName, array $searchColumns, $searchTerm, $operator = 'LIKE') {
        $columns = $this->getTableColumns($tableName);
        $conditions = [];
        
        $searchTerm = $this->db->escape(strtoupper($searchTerm));
        
        foreach ($searchColumns as $column) {
            if (isset($columns[$column])) {
                if ($operator === 'LIKE') {
                    $conditions[] = "UPPER(COALESCE($column, '')) LIKE '%$searchTerm%'";
                } else {
                    $conditions[] = "UPPER(COALESCE($column, '')) $operator '$searchTerm'";
                }
            }
        }
        
        if (empty($conditions)) {
            return '';
        }
        
        return 'WHERE ' . implode(' OR ', $conditions);
    }
    
    /**
     * Constrói ORDER BY dinâmico
     * Usa primeira coluna disponível da lista
     */
    public function buildOrderBy($tableName, array $orderColumns, $direction = 'ASC') {
        $columns = $this->getTableColumns($tableName);
        
        foreach ($orderColumns as $column) {
            if (isset($columns[$column])) {
                return "ORDER BY $column $direction";
            }
        }
        
        return '';
    }
    
    /**
     * Verifica se uma tabela existe
     */
    public function tableExists($tableName) {
        $sql = "SELECT EXISTS (
                    SELECT 1 FROM information_schema.tables 
                    WHERE table_name = '" . $this->db->escape($tableName) . "'
                ) as existe";
        
        $result = $this->db->fetchOne($sql);
        
        return ($result && $result['existe'] === 't');
    }
    
    /**
     * Retorna tipo de estrutura do banco baseado em colunas específicas
     * 'propasso' ou 'generico'
     */
    public function detectDatabaseStructure($tableName = 'glb_pessoa') {
        $columns = $this->getTableColumns($tableName);
        
        // Banco Propasso tem cpf_cgc
        if (isset($columns['cpf_cgc'])) {
            return 'propasso';
        }
        
        return 'generico';
    }
    
    /**
     * Limpa cache de colunas (útil após alterações no schema)
     */
    public static function clearCache($tableName = null) {
        if ($tableName) {
            $db = Database::getInstance();
            $cacheKey = $db->getDatabase() . '.' . $tableName;
            unset(self::$columnCache[$cacheKey]);
        } else {
            self::$columnCache = [];
        }
    }
    
    /**
     * Retorna mapeamento padrão de colunas para clientes
     */
    public static function getClientColumnMapping() {
        return [
            'cd_pessoa' => ['cd_pessoa', 'cd_cliente', 'codigo'],
            'nm_fant' => ['nm_fant', 'nm_fantasia', 'nome_fantasia'],
            'nm_razao' => ['nm_razao', 'nm_pessoa', 'razao_social', 'nome'],
            'cpf_cnpj' => ['nr_cpf_cnpj', 'cpf_cgc', 'cpf_cnpj', 'documento'],
            'endereco' => ['ds_endereco', 'endereco', 'rua', 'logradouro'],
            'bairro' => ['ds_bairro', 'bairro'],
            'cidade' => ['ds_cidade', 'cidade', 'municipio'],
            'uf' => ['ds_uf', 'uf', 'estado'],
            'cep' => ['nr_cep', 'cep'],
            'fone' => ['fone', 'telefone', 'nr_telefone', 'nr_fone'],
            'email' => ['ds_email', 'email', 'e_mail'],
            'dt_nascimento' => ['dt_nascimento', 'dt_nasc', 'data_nascimento'],
            'dt_cadastro' => ['dt_cadastro', 'dt_cad', 'data_cadastro']
        ];
    }
}
