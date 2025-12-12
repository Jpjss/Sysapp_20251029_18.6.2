<?php
/**
 * Model de Cliente
 */

require_once BASE_PATH . '/core/DatabaseHelper.php';

class Cliente {
    private $db;
    private $helper;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->helper = new DatabaseHelper();
        
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
        
        // Usa DatabaseHelper para construir query dinâmica
        $columnMap = DatabaseHelper::getClientColumnMapping();
        $selectClause = $this->helper->buildDynamicSelect('glb_pessoa', $columnMap);
        
        // Constrói WHERE dinâmico
        $where = '';
        if (!empty($filtro)) {
            $searchColumns = ['nm_pessoa', 'nm_fant', 'nm_razao', 'cpf_cgc', 'nr_cpf_cnpj', 'cpf_cnpj'];
            $where = $this->helper->buildSearchWhere('glb_pessoa', $searchColumns, $filtro);
        }
        
        // Constrói ORDER BY dinâmico
        $orderBy = $this->helper->buildOrderBy('glb_pessoa', ['nm_fant', 'nm_pessoa', 'nome']);
        
        $sql = "SELECT $selectClause 
                FROM glb_pessoa 
                $where
                $orderBy
                LIMIT $limit OFFSET $offset";
        
        $result = $this->db->fetchAll($sql);
        
        return $result;
    }
    
    /**
     * Conta total de clientes
     */
    public function count($filtro = '') {
        $where = '';
        if (!empty($filtro)) {
            $searchColumns = ['nm_pessoa', 'nm_fant', 'nm_razao', 'cpf_cgc', 'nr_cpf_cnpj', 'cpf_cnpj'];
            $where = $this->helper->buildSearchWhere('glb_pessoa', $searchColumns, $filtro);
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
        
        // Usa DatabaseHelper para construir query dinâmica
        $columnMap = DatabaseHelper::getClientColumnMapping();
        $selectClause = $this->helper->buildDynamicSelect('glb_pessoa', $columnMap);
        
        $sql = "SELECT $selectClause
                FROM glb_pessoa 
                WHERE cd_pessoa = $cd_pessoa";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Busca para autocomplete (Select2)
     */
    public function search($term, $limit = 10) {
        $term = $this->db->escape(strtoupper($term));
        $limit = (int)$limit;
        
        // Determina qual coluna de nome usar dinamicamente
        $nomeCol = $this->helper->getAvailableColumn('glb_pessoa', ['nm_fant', 'nm_pessoa', 'nome']);
        if (!$nomeCol) {
            $nomeCol = 'cd_pessoa'; // Fallback
        }
        
        $sql = "SELECT cd_pessoa, $nomeCol as nm_fant 
                FROM glb_pessoa 
                WHERE UPPER(COALESCE($nomeCol, '')) LIKE '$term%' 
                ORDER BY $nomeCol 
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca telefones do cliente
     */
    public function getTelefones($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        // Verifica se tabela de telefones existe
        if (!$this->helper->tableExists('glb_pessoa_fone')) {
            return [];
        }
        
        // Detecta colunas disponíveis
        $columns = $this->helper->getTableColumns('glb_pessoa_fone');
        
        // Monta SELECT dinâmico
        $selectParts = ['cd_fone'];
        
        // Número do telefone
        $foneCol = $this->helper->getAvailableColumn('glb_pessoa_fone', ['fone', 'nr_fone', 'telefone', 'nr_telefone']);
        if ($foneCol) {
            $selectParts[] = "$foneCol as nr_fone";
        }
        
        // Tipo do telefone
        if (isset($columns['tp_fone'])) {
            $selectParts[] = "CASE 
                               WHEN tp_fone = 10 THEN 'Celular'
                               WHEN tp_fone = 20 THEN 'Residencial'
                               WHEN tp_fone = 30 THEN 'Comercial'
                               ELSE tp_fone::text
                           END as tp_fone";
        } else {
            $selectParts[] = "'' as tp_fone";
        }
        
        // Nome do contato
        $contatoCol = $this->helper->getAvailableColumn('glb_pessoa_fone', ['nm_contato', 'contato', 'nome_contato']);
        if ($contatoCol) {
            $selectParts[] = "$contatoCol as nm_contato";
        } else {
            $selectParts[] = "'' as nm_contato";
        }
        
        $sql = "SELECT " . implode(', ', $selectParts) . " 
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
        if (!$this->helper->tableExists('glb_pessoa_obs_contato')) {
            return [];
        }
        
        $sql = "SELECT * FROM glb_pessoa_obs_contato 
                WHERE cd_pessoa = $cd_pessoa 
                ORDER BY dt_obs DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca histórico de vendas/atendimentos do cliente
     */
    public function getHistorico($cd_pessoa) {
        $cd_pessoa = (int)$cd_pessoa;
        
        // Verifica se tabela de pedidos existe
        if (!$this->helper->tableExists('ped_vd')) {
            return [];
        }
        
        // Detecta colunas disponíveis
        $columns = $this->helper->getTableColumns('ped_vd');
        
        // Monta SELECT dinâmico
        $selectParts = [];
        
        // Campos principais
        $camposPedido = [
            'cd_ped' => ['cd_ped', 'cd_pedido', 'numero_pedido'],
            'dt_hr_ped' => ['dt_hr_ped', 'dt_pedido', 'data_pedido'],
            'vlr_vd' => ['vlr_vd', 'vlr_total', 'valor_total', 'total'],
            'vlr_entrada' => ['vlr_entrada', 'entrada', 'valor_entrada'],
            'qtd_pecas' => ['qtd_pecas', 'quantidade', 'qtde'],
            'sit_ped' => ['sit_ped', 'situacao', 'status']
        ];
        
        foreach ($camposPedido as $alias => $options) {
            $col = $this->helper->getAvailableColumn('ped_vd', $options);
            if ($col) {
                if ($col === $alias) {
                    $selectParts[] = $col;
                } else {
                    $selectParts[] = "$col as $alias";
                }
            }
        }
        
        // Adiciona descrição de situação se campo existe
        if (isset($columns['sit_ped'])) {
            $selectParts[] = "CASE 
                                WHEN sit_ped = 0 THEN 'Ativo'
                                WHEN sit_ped = 1 THEN 'Finalizado'
                                WHEN sit_ped = 2 THEN 'Cancelado'
                                ELSE 'Outros'
                            END as ds_situacao";
        }
        
        if (empty($selectParts)) {
            return [];
        }
        
        // Campo de cliente pode ter nomes diferentes
        $clienteCol = $this->helper->getAvailableColumn('ped_vd', ['cd_cli', 'cd_cliente', 'cd_pessoa']);
        if (!$clienteCol) {
            return [];
        }
        
        // Campo de data para ordenação
        $dataCol = $this->helper->getAvailableColumn('ped_vd', ['dt_hr_ped', 'dt_pedido', 'data_pedido']);
        $orderBy = $dataCol ? "ORDER BY $dataCol DESC" : '';
        
        $sql = "SELECT " . implode(', ', $selectParts) . "
                FROM ped_vd
                WHERE $clienteCol = $cd_pessoa
                $orderBy
                LIMIT 50";
        
        return $this->db->fetchAll($sql);
    }
}
