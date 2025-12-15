<?php
/**
 * Model de Usuário
 */

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca usuário por login
     */
    public function findByLogin($login) {
        $login = $this->db->escape(strtolower($login));
        
        $sql = "SELECT cd_usuario 
                FROM sysapp_config_user 
                WHERE LOWER(ds_email) = '$login' 
                   OR LOWER(ds_login) = '$login' 
                   OR LOWER(nm_usuario) = '$login'";
        
        $result = $this->db->fetchOne($sql);
        
        return $result;
    }
    
    /**
     * Busca dados do usuário para autenticação
     */
    public function findForAuth($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_senha as senha_usuario 
                FROM sysapp_config_user 
                WHERE cd_usuario = $cd_usuario AND fg_ativo = 'S'";
        
        $result = $this->db->fetchOne($sql);
        
        return $result;
    }
    
    /**
     * Busca empresas do usuário
     */
    public function getEmpresas($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT DISTINCT cd_empresa 
                FROM sysapp_config_user_empresas 
                WHERE cd_usuario = $cd_usuario";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca informações das empresas do usuário
     */
    public function getEmpresasInfo($cd_usuario, $cd_empresas) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT ce.cd_empresa, ce.nm_empresa as nome_empresa, ce.ds_host as hostname_banco, 
                       ce.ds_banco as nome_banco, ce.ds_usuario as usuario_banco, 
                       ce.ds_senha as senha_banco, ce.ds_porta as porta_banco
                FROM sysapp_config_empresas ce
                INNER JOIN sysapp_config_user_empresas cue 
                    ON ce.cd_empresa = cue.cd_empresa
                WHERE cue.cd_usuario = $cd_usuario 
                AND ce.cd_empresa IN ($cd_empresas)
                ORDER BY ce.nm_empresa";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca permissões do usuário
     */
    public function getPermissoes($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT nm_interface as nome_interface 
                FROM vw_login_empresa_interface 
                WHERE cd_usuario = $cd_usuario";
        
        $result = $this->db->fetchAll($sql);
        
        $permissoes = [];
        if ($result) {
            foreach ($result as $row) {
                $permissoes[] = $row['nome_interface'];
            }
        }
        
        return $permissoes;
    }
    
    /**
     * Troca senha do usuário
     */
    public function trocarSenha($cd_usuario, $novaSenha) {
        $cd_usuario = (int)$cd_usuario;
        $novaSenha = $this->db->escape($novaSenha);
        
        $sql = "UPDATE vw_login 
                SET senha_usuario = '$novaSenha' 
                WHERE cd_usuario = $cd_usuario";
        
        return $this->db->query($sql);
    }
    
    /**
     * Lista todos usuários
     */
    public function listar($limit = 10, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_login as login_usuario, cd_usu_erp 
                FROM sysapp_config_user 
                ORDER BY nm_usuario 
                LIMIT $limit OFFSET $offset";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Conta total de usuários
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM sysapp_config_user";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Busca próximo código de usuário
     */
    public function getNextCodigo() {
        $sql = "SELECT MAX(cd_usuario) + 1 as cd_usuario FROM sysapp_config_user";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['cd_usuario'] : 1;
    }
    
    /**
     * Busca usuário por ID
     */
    public function findById($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT cd_usuario, nm_usuario as nome_usuario, ds_login as login_usuario, cd_usu_erp 
                FROM sysapp_config_user 
                WHERE cd_usuario = $cd_usuario";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Salva novo usuário
     */
    public function salvar($dados) {
        $cd_usuario = (int)$dados['cd_usuario'];
        $nome_usuario = $this->db->escape(ucwords($dados['nome_usuario']));
        $login_usuario = $this->db->escape(strtolower($dados['login_usuario']));
        $senha_usuario = $this->db->escape($dados['senha_usuario']);
        $cd_usu_erp = isset($dados['cd_usu_erp']) ? (int)$dados['cd_usu_erp'] : 'NULL';
        
        $sql = "INSERT INTO sysapp_config_user 
                (cd_usuario, nm_usuario, ds_login, ds_senha, cd_usu_erp) 
                VALUES ($cd_usuario, '$nome_usuario', '$login_usuario', '$senha_usuario', $cd_usu_erp)";
        
        return $this->db->query($sql);
    }
    
    /**
     * Atualiza usuário
     */
    public function atualizar($dados) {
        $cd_usuario = (int)$dados['cd_usuario'];
        $nome_usuario = $this->db->escape(ucwords($dados['nome_usuario']));
        $login_usuario = $this->db->escape(strtolower($dados['login_usuario']));
        $cd_usu_erp = isset($dados['cd_usu_erp']) ? (int)$dados['cd_usu_erp'] : 'NULL';
        
        $sql = "UPDATE sysapp_config_user 
                SET nm_usuario = '$nome_usuario', 
                    ds_login = '$login_usuario', 
                    cd_usu_erp = $cd_usu_erp";
        
        // Atualiza senha se fornecida
        if (!empty($dados['senha_usuario'])) {
            $senha_usuario = $this->db->escape($dados['senha_usuario']);
            $sql .= ", ds_senha = '$senha_usuario'";
        }
        
        $sql .= " WHERE cd_usuario = $cd_usuario";
        
        return $this->db->query($sql);
    }
    
    /**
     * Exclui usuário
     */
    public function excluir($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "DELETE FROM sysapp_config_user WHERE cd_usuario = $cd_usuario";
        
        return $this->db->query($sql);
    }
    
    /**
     * Salva empresas do usuário
     */
    public function salvarEmpresas($cd_usuario, $empresas) {
        $cd_usuario = (int)$cd_usuario;
        
        // Remove empresas antigas
        $this->db->query("DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario");
        
        // Adiciona novas empresas
        if (!empty($empresas)) {
            foreach ($empresas as $cd_empresa) {
                $cd_empresa = (int)$cd_empresa;
                $sql = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) 
                        VALUES ($cd_empresa, $cd_usuario)";
                $this->db->query($sql);
            }
        }
        
        return true;
    }
    
    /**
     * Salva permissões do usuário
     */
    public function salvarPermissoes($cd_usuario, $empresas, $interfaces) {
        $cd_usuario = (int)$cd_usuario;
        
        // Remove permissões antigas
        $this->db->query("DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = $cd_usuario");
        
        // Adiciona novas permissões
        if (!empty($empresas) && !empty($interfaces)) {
            foreach ($empresas as $cd_empresa) {
                foreach ($interfaces as $cd_interface) {
                    $cd_empresa = (int)$cd_empresa;
                    $cd_interface = (int)$cd_interface;
                    $sql = "INSERT INTO sysapp_config_user_empresas_interfaces 
                            (cd_empresa, cd_usuario, cd_interface) 
                            VALUES ($cd_empresa, $cd_usuario, $cd_interface)";
                    $this->db->query($sql);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Busca empresas do usuário
     */
    public function getEmpresasUsuario($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT cd_empresa FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario";
        
        $result = $this->db->fetchAll($sql);
        $empresas = [];
        
        if ($result) {
            foreach ($result as $row) {
                $empresas[] = $row['cd_empresa'];
            }
        }
        
        return $empresas;
    }
    
    /**
     * Busca interfaces do usuário
     */
    public function getInterfacesUsuario($cd_usuario) {
        $cd_usuario = (int)$cd_usuario;
        
        $sql = "SELECT DISTINCT cd_interface 
                FROM sysapp_config_user_empresas_interfaces 
                WHERE cd_usuario = $cd_usuario";
        
        $result = $this->db->fetchAll($sql);
        $interfaces = [];
        
        if ($result) {
            foreach ($result as $row) {
                $interfaces[] = $row['cd_interface'];
            }
        }
        
        return $interfaces;
    }
    
    /**
     * Verifica se email já existe
     */
    public function emailExiste($email, $cd_usuario = null) {
        $email = $this->db->escape(strtolower($email));
        
        $sql = "SELECT cd_usuario FROM sysapp_config_user WHERE LOWER(ds_login) = '$email'";
        
        if ($cd_usuario) {
            $cd_usuario = (int)$cd_usuario;
            $sql .= " AND cd_usuario != $cd_usuario";
        }
        
        $result = $this->db->fetchOne($sql);
        return $result !== false && $result !== null;
    }
    
    /**
     * Atualiza empresas do usuário
     */
    public function atualizarEmpresas($cd_usuario, $empresas) {
        $cd_usuario = (int)$cd_usuario;
        $conn = $this->db->getConnection();
        
        // Remove todas as empresas do usuário
        $sql = "DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = $cd_usuario";
        pg_query($conn, $sql);
        
        // Adiciona as empresas selecionadas
        if (!empty($empresas)) {
            foreach ($empresas as $cd_empresa) {
                $cd_empresa = (int)$cd_empresa;
                $sql = "INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
                        VALUES ($cd_usuario, $cd_empresa)";
                pg_query($conn, $sql);
            }
        }
        
        return true;
    }
    
    /**
     * Atualiza permissões do usuário
     */
    public function atualizarPermissoes($cd_usuario, $permissoes) {
        $cd_usuario = (int)$cd_usuario;
        $conn = $this->db->getConnection();
        
        // Remove todas as permissões do usuário
        $sql = "DELETE FROM sysapp_config_user_interfaces WHERE cd_usuario = $cd_usuario";
        pg_query($conn, $sql);
        
        // Adiciona as permissões selecionadas
        if (!empty($permissoes)) {
            foreach ($permissoes as $nm_interface) {
                $nm_interface = $this->db->escape($nm_interface);
                $sql = "INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) 
                        VALUES ($cd_usuario, '$nm_interface')";
                pg_query($conn, $sql);
            }
        }
        
        return true;
    }
}
