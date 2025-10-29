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
     * Lista todas empresas
     */
    public function listar() {
        $sql = "SELECT cd_empresa, nome_empresa 
                FROM sysapp_config_empresas 
                ORDER BY nome_empresa";
        
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
        $nome_empresa = $this->db->escape(ucwords($dados['nome_empresa']));
        $hostname = $this->db->escape($dados['hostname']);
        $nome_banco = $this->db->escape($dados['nome_banco']);
        $usuario_banco = $this->db->escape($dados['usuario_banco']);
        $senha_banco = $this->db->escape($dados['senha_banco']);
        $porta_banco = $this->db->escape($dados['porta_banco']);
        
        $sql = "INSERT INTO sysapp_config_empresas 
                (cd_empresa, nome_empresa, hostname_banco, nome_banco, usuario_banco, senha_banco, porta_banco) 
                VALUES ($cd_empresa, '$nome_empresa', '$hostname', '$nome_banco', 
                        '$usuario_banco', '$senha_banco', '$porta_banco')";
        
        return $this->db->query($sql);
    }
    
    /**
     * Atualiza empresa
     */
    public function atualizar($dados) {
        $cd_empresa = (int)$dados['cd_empresa'];
        $nome_empresa = $this->db->escape(ucwords($dados['nome_empresa']));
        $hostname = $this->db->escape($dados['hostname']);
        $nome_banco = $this->db->escape($dados['nome_banco']);
        $usuario_banco = $this->db->escape($dados['usuario_banco']);
        $senha_banco = $this->db->escape($dados['senha_banco']);
        $porta_banco = $this->db->escape($dados['porta_banco']);
        
        $sql = "UPDATE sysapp_config_empresas 
                SET nome_empresa = '$nome_empresa',
                    hostname_banco = '$hostname',
                    nome_banco = '$nome_banco',
                    usuario_banco = '$usuario_banco',
                    senha_banco = '$senha_banco',
                    porta_banco = '$porta_banco'
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
}
