<?php
/**
 * Model de Cliente
 */

class Cliente {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca clientes com paginação
     */
    public function listar($limit = 20, $offset = 0, $filtro = '') {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $where = '';
        if (!empty($filtro)) {
            $filtro = $this->db->escape(strtoupper($filtro));
            $where = "WHERE UPPER(nm_fant) LIKE '%$filtro%' OR UPPER(nm_razao) LIKE '%$filtro%'";
        }
        
        $sql = "SELECT cd_pessoa, nm_fant, nm_razao, cpf_cnpj, fone 
                FROM glb_pessoa 
                $where
                ORDER BY nm_fant 
                LIMIT $limit OFFSET $offset";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Conta total de clientes
     */
    public function count($filtro = '') {
        $where = '';
        if (!empty($filtro)) {
            $filtro = $this->db->escape(strtoupper($filtro));
            $where = "WHERE UPPER(nm_fant) LIKE '%$filtro%' OR UPPER(nm_razao) LIKE '%$filtro%'";
        }
        
        $sql = "SELECT COUNT(*) as total FROM glb_pessoa $where";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
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
        
        $sql = "SELECT cd_pessoa, nm_fant 
                FROM glb_pessoa 
                WHERE UPPER(nm_fant) LIKE '$term%' 
                ORDER BY nm_fant 
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca telefones do cliente
     */
    public function getTelefones($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        $sql = "SELECT * FROM glb_pessoa_fone WHERE cd_pessoa = $cd_pessoa ORDER BY cd_fone";
        
        return $this->db->fetchAll($sql);
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
