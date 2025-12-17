<?php
/**
 * Model de Empresa
 */

class Empresa {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lista todas empresas (sem paginação)
     */
    public function listarTodas() {
        $sql = "SELECT cd_empresa, nm_empresa as nome_empresa, ds_host, ds_banco, ds_usuario, ds_porta, fg_ativo 
                FROM sysapp_config_empresas 
                WHERE fg_ativo = 'S'
                ORDER BY nm_empresa";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Lista empresas com paginação
     */
    public function listar($limit = null, $offset = 0) {
        // Se limit for null, retorna todas sem paginação
        if ($limit === null) {
            return $this->listarTodas();
        }
        
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT cd_empresa, nm_empresa as nome_empresa, ds_host, ds_banco, ds_usuario, ds_porta, fg_ativo 
                FROM sysapp_config_empresas 
                ORDER BY nm_empresa 
                LIMIT $limit OFFSET $offset";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca empresa por ID
     */
    public function findById($cd_empresa) {
        $cd_empresa = (int)$cd_empresa;
        
        $sql = "SELECT * FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Busca próximo código de empresa
     */
    public function getNextCodigo() {
        $sql = "SELECT MAX(cd_empresa) + 1 as cd_empresa FROM sysapp_config_empresas";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['cd_empresa'] : 1;
    }
    
    /**
     * Salva nova empresa
     */
    public function salvar($dados) {
        $cd_empresa = (int)$dados['cd_empresa'];
        $nm_empresa = $this->db->escape(ucwords($dados['nome_empresa']));
        $ds_host = $this->db->escape($dados['hostname']);
        $ds_banco = $this->db->escape($dados['nome_banco']);
        $ds_usuario = $this->db->escape($dados['usuario_banco']);
        $ds_senha = $this->db->escape($dados['senha_banco']);
        $ds_porta = $this->db->escape($dados['porta_banco']);
        
        $sql = "INSERT INTO sysapp_config_empresas 
                (cd_empresa, nm_empresa, ds_host, ds_banco, ds_usuario, ds_senha, ds_porta) 
                VALUES ($cd_empresa, '$nm_empresa', '$ds_host', '$ds_banco', 
                        '$ds_usuario', '$ds_senha', '$ds_porta')";
        
        error_log("SQL INSERT: " . $sql);
        
        $result = $this->db->query($sql);
        
        if (!$result) {
            $error = pg_last_error($this->db->getConnection());
            error_log("ERRO ao inserir empresa: " . $error);
        } else {
            error_log("Empresa inserida com sucesso! CD: $cd_empresa");
        }
        
        return $result;
    }
    
    /**
     * Atualiza empresa
     */
    public function atualizar($dados) {
        $cd_empresa = (int)$dados['cd_empresa'];
        $nm_empresa = $this->db->escape(ucwords($dados['nome_empresa']));
        $ds_host = $this->db->escape($dados['hostname']);
        $ds_banco = $this->db->escape($dados['nome_banco']);
        $ds_usuario = $this->db->escape($dados['usuario_banco']);
        $ds_senha = $this->db->escape($dados['senha_banco']);
        $ds_porta = $this->db->escape($dados['porta_banco']);
        
        $sql = "UPDATE sysapp_config_empresas 
                SET nm_empresa = '$nm_empresa',
                    ds_host = '$ds_host',
                    ds_banco = '$ds_banco',
                    ds_usuario = '$ds_usuario',
                    ds_senha = '$ds_senha',
                    ds_porta = '$ds_porta'
                WHERE cd_empresa = $cd_empresa";
        
        return $this->db->query($sql);
    }
    
    /**
     * Exclui empresa
     */
    public function excluir($cd_empresa) {
        $cd_empresa = (int)$cd_empresa;
        
        // Verifica se há usuários usando esta empresa
        $sql = "SELECT COUNT(*) as total FROM sysapp_config_user_empresas WHERE cd_empresa = $cd_empresa";
        $result = $this->db->fetchOne($sql);
        
        if ($result && $result['total'] > 0) {
            return false; // Não pode excluir, há usuários vinculados
        }
        
        $sql = "DELETE FROM sysapp_config_empresas WHERE cd_empresa = $cd_empresa";
        
        return $this->db->query($sql);
    }
    
    /**
     * Conta total de empresas
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM sysapp_config_empresas";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Conta empresas ativas
     */
    public function countAtivas() {
        $sql = "SELECT COUNT(*) as total FROM sysapp_config_empresas WHERE fg_ativo = 'S'";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
}
