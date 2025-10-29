<?php
/**
 * Model de Cliente
 */

class Cliente {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Se há configuração de banco na sessão, reconecta
        if (Session::check('Config.database')) {
            $host = Session::read('Config.host');
            $database = Session::read('Config.database');
            $user = Session::read('Config.user');
            $password = Session::read('Config.password');
            $port = Session::read('Config.porta');
            
            error_log("=== Cliente::__construct() - Reconectando ao banco da sessão ===");
            error_log("Database: $database, Host: $host, Port: $port");
            
            $this->db->connect($host, $database, $user, $password, $port);
        }
    }
    
    /**
     * Busca clientes com paginação
     */
    public function listar($limit = 20, $offset = 0, $filtro = '') {
        error_log("=== Cliente::listar() CHAMADO ===");
        error_log("Limit: $limit, Offset: $offset, Filtro: $filtro");
        
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        // Detecta qual estrutura de banco está sendo usada
        $estrutura = $this->detectarEstrutura();
        error_log("Estrutura detectada: $estrutura");
        
        $where = '';
        if (!empty($filtro)) {
            $filtro = $this->db->escape(strtoupper($filtro));
            if ($estrutura === 'propasso') {
                // Banco Propasso: usa nm_pessoa e cpf_cgc
                $where = "WHERE UPPER(COALESCE(nm_pessoa, '')) LIKE '%$filtro%' OR UPPER(COALESCE(cpf_cgc, '')) LIKE '%$filtro%'";
            } else {
                // Banco teste: usa nm_fant, nm_pessoa e nr_cpf_cnpj
                $where = "WHERE UPPER(COALESCE(nm_fant, '')) LIKE '%$filtro%' OR UPPER(COALESCE(nm_pessoa, '')) LIKE '%$filtro%' OR UPPER(COALESCE(nr_cpf_cnpj, '')) LIKE '%$filtro%'";
            }
        }
        
        if ($estrutura === 'propasso') {
            // Banco Propasso
            $sql = "SELECT cd_pessoa, 
                           nm_pessoa as nm_fant, 
                           nm_pessoa as nm_razao, 
                           cpf_cgc as cpf_cnpj, 
                           '' as fone 
                    FROM glb_pessoa 
                    $where
                    ORDER BY nm_pessoa 
                    LIMIT $limit OFFSET $offset";
        } else {
            // Banco teste
            $sql = "SELECT cd_pessoa, 
                           COALESCE(nm_fant, nm_pessoa) as nm_fant, 
                           nm_pessoa as nm_razao, 
                           nr_cpf_cnpj as cpf_cnpj, 
                           '' as fone 
                    FROM glb_pessoa 
                    $where
                    ORDER BY COALESCE(nm_fant, nm_pessoa) 
                    LIMIT $limit OFFSET $offset";
        }
        
        error_log("SQL: $sql");
        
        $result = $this->db->fetchAll($sql);
        error_log("Quantidade de clientes retornados: " . count($result));
        
        if (!empty($result)) {
            error_log("Primeiro cliente: " . print_r($result[0], true));
        }
        
        return $result;
    }
    
    /**
     * Detecta qual estrutura de banco está sendo usada
     */
    private function detectarEstrutura() {
        // Verifica se existe a coluna nr_cpf_cnpj (banco teste) ou cpf_cgc (banco propasso)
        $sql = "SELECT column_name FROM information_schema.columns 
                WHERE table_name = 'glb_pessoa' AND column_name IN ('nr_cpf_cnpj', 'cpf_cgc')";
        
        $result = $this->db->fetchAll($sql);
        
        if ($result) {
            foreach ($result as $row) {
                if ($row['column_name'] === 'cpf_cgc') {
                    return 'propasso';
                }
            }
        }
        
        return 'teste';
    }
    
    /**
     * Conta total de clientes
     */
    public function count($filtro = '') {
        error_log("=== Cliente::count() CHAMADO ===");
        
        $estrutura = $this->detectarEstrutura();
        
        $where = '';
        if (!empty($filtro)) {
            $filtro = $this->db->escape(strtoupper($filtro));
            if ($estrutura === 'propasso') {
                $where = "WHERE UPPER(COALESCE(nm_pessoa, '')) LIKE '%$filtro%' OR UPPER(COALESCE(cpf_cgc, '')) LIKE '%$filtro%'";
            } else {
                $where = "WHERE UPPER(COALESCE(nm_fant, '')) LIKE '%$filtro%' OR UPPER(COALESCE(nm_pessoa, '')) LIKE '%$filtro%' OR UPPER(COALESCE(nr_cpf_cnpj, '')) LIKE '%$filtro%'";
            }
        }
        
        $sql = "SELECT COUNT(*) as total FROM glb_pessoa $where";
        error_log("SQL COUNT: $sql");
        
        $result = $this->db->fetchOne($sql);
        $total = $result ? (int)$result['total'] : 0;
        
        error_log("Total de clientes: $total");
        
        return $total;
    }
    
    /**
     * Busca cliente por ID
     */
    public function findById($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        $sql = "SELECT * FROM glb_pessoa WHERE cd_pessoa = $cd_pessoa";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Busca para autocomplete (Select2)
     */
    public function search($term, $limit = 10) {
        $term = $this->db->escape(strtoupper($term));
        $limit = (int)$limit;
        
        $sql = "SELECT cd_pessoa, COALESCE(nm_fant, nm_pessoa) as nm_fant 
                FROM glb_pessoa 
                WHERE UPPER(COALESCE(nm_fant, nm_pessoa)) LIKE '$term%' 
                ORDER BY COALESCE(nm_fant, nm_pessoa) 
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca telefones do cliente
     */
    public function getTelefones($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        // Tenta buscar na tabela glb_pessoa_telefone (nova estrutura)
        $sql = "SELECT cd_telefone as cd_fone, nr_telefone as fone, tp_telefone as tipo 
                FROM glb_pessoa_telefone 
                WHERE cd_pessoa = $cd_pessoa 
                ORDER BY cd_telefone";
        
        $result = $this->db->fetchAll($sql);
        
        // Se não encontrar, tenta na estrutura antiga
        if (empty($result)) {
            $sql = "SELECT * FROM glb_pessoa_fone WHERE cd_pessoa = $cd_pessoa ORDER BY cd_fone";
            $result = $this->db->fetchAll($sql);
        }
        
        return $result;
    }
    
    /**
     * Busca observações de contato
     */
    public function getObservacoes($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        $sql = "SELECT * FROM glb_pessoa_obs_contato 
                WHERE cd_pessoa = $cd_pessoa 
                ORDER BY dt_obs DESC";
        
        return $this->db->fetchAll($sql);
    }
}
