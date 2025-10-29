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
        $sql = "SELECT cd_interface, nome_interface 
                FROM sysapp_controle_interface 
                ORDER BY nome_interface";
        
        return $this->db->fetchAll($sql);
    }
}
