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
            
            $this->db->connect($host, $database, $user, $password, $port);
        }
    }
    
    /**
     * Busca clientes com paginação
     */
    public function listar($limit = 20, $offset = 0, $filtro = '') {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        // Detecta qual estrutura de banco está sendo usada
        $estrutura = $this->detectarEstrutura();
        
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
        
        $result = $this->db->fetchAll($sql);
        
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
        
        $result = $this->db->fetchOne($sql);
        $total = $result ? (int)$result['total'] : 0;
        
        return $total;
    }
    
    /**
     * Busca cliente por ID
     */
    public function findById($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        $estrutura = $this->detectarEstrutura();
        
        if ($estrutura === 'propasso') {
            // Banco Propasso: usa nm_pessoa e cpf_cgc
            $sql = "SELECT cd_pessoa,
                           nm_pessoa as nm_fant,
                           nm_pessoa as nm_razao,
                           cpf_cgc as cpf_cnpj,
                           '' as endereco,
                           '' as bairro,
                           '' as cidade,
                           '' as uf
                    FROM glb_pessoa 
                    WHERE cd_pessoa = $cd_pessoa";
        } else {
            // Banco teste: estrutura completa
            $sql = "SELECT cd_pessoa,
                           COALESCE(nm_fant, nm_pessoa) as nm_fant,
                           nm_pessoa as nm_razao,
                           nr_cpf_cnpj as cpf_cnpj,
                           COALESCE(ds_endereco, '') as endereco,
                           COALESCE(ds_bairro, '') as bairro,
                           COALESCE(ds_cidade, '') as cidade,
                           COALESCE(ds_uf, '') as uf
                    FROM glb_pessoa 
                    WHERE cd_pessoa = $cd_pessoa";
        }
        
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
        
        // Busca na tabela glb_pessoa_fone (estrutura Propasso)
        $sql = "SELECT cd_fone,
                       fone as nr_fone,
                       CASE 
                           WHEN tp_fone = 10 THEN 'Celular'
                           WHEN tp_fone = 20 THEN 'Residencial'
                           WHEN tp_fone = 30 THEN 'Comercial'
                           ELSE tp_fone::text
                       END as tp_fone,
                       COALESCE(nm_contato, '') as nm_contato
                FROM glb_pessoa_fone 
                WHERE cd_pessoa = $cd_pessoa 
                ORDER BY cd_fone";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca observações de contato
     */
    public function getObservacoes($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        // Verifica se tabela existe
        $check_sql = "SELECT EXISTS (
            SELECT 1 FROM information_schema.tables 
            WHERE table_name = 'glb_pessoa_obs_contato'
        ) as existe";
        
        $existe = $this->db->fetchOne($check_sql);
        
        if ($existe && $existe['existe'] === 't') {
            $sql = "SELECT * FROM glb_pessoa_obs_contato 
                    WHERE cd_pessoa = $cd_pessoa 
                    ORDER BY dt_obs DESC";
            return $this->db->fetchAll($sql);
        }
        
        return [];
    }
    
    /**
     * Busca histórico de vendas/atendimentos do cliente
     */
    public function getHistorico($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        // Busca pedidos do cliente (ped_vd)
        $sql = "SELECT 
                    pv.cd_ped,
                    pv.dt_hr_ped,
                    pv.vlr_vd,
                    pv.vlr_entrada,
                    pv.qtd_pecas,
                    pv.sit_ped,
                    CASE 
                        WHEN pv.sit_ped = 0 THEN 'Ativo'
                        WHEN pv.sit_ped = 1 THEN 'Finalizado'
                        WHEN pv.sit_ped = 2 THEN 'Cancelado'
                        ELSE 'Outros'
                    END as ds_situacao
                FROM ped_vd pv
                WHERE pv.cd_cli = $cd_pessoa
                ORDER BY pv.dt_hr_ped DESC
                LIMIT 50";
        
        return $this->db->fetchAll($sql);
    }
}
