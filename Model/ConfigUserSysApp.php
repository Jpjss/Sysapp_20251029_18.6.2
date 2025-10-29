<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class ConfigUserSysApp extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'sysapp_config_user';
    
    public function retornaCodigoUsuErp($usuario) {
        
        $SQL = "";
        $SQL .= "SELECT   cd_usu_erp ";
        $SQL .= "FROM     sysapp_config_user ";
        $SQL .= "WHERE    cd_usuario = " . $usuario;
        
        //21957: Configurar acesso por loja WebApp
        return $this->query($SQL);
    }
    
}
