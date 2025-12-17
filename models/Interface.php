<?php
/**
 * Model de Interface (Permissões)
 */

class InterfaceModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lista todas interfaces/relatórios
     */
    public function listar() {
        $sql = "SELECT cd_interface, nm_interface as nome_interface 
                FROM sysapp_controle_interface 
                WHERE fg_ativo = 'S'
                ORDER BY nr_ordem, nm_interface";
        
        return $this->db->fetchAll($sql);
    }
}
