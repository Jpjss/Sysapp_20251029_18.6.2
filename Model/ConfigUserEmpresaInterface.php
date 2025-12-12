<?php

App::uses('AppModel', 'Model');

class ConfigUserEmpresaInterface extends AppModel { 
    

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'sysapp_config_user_empresas_interfaces';
    
    public function buscaInfoDb($usuario, $cd_empresa){
    	$SQL = "";
    	$SQL .= "SELECT   sysapp_config_empresas.cd_empresa    ,";
    	$SQL .= "sysapp_config_empresas.nome_empresa  , ";
    	$SQL .= "sysapp_config_empresas.hostname_banco, ";
    	$SQL .= "sysapp_config_empresas.nome_banco    , ";
    	$SQL .= "sysapp_config_empresas.usuario_banco , ";
    	$SQL .= "sysapp_config_empresas.senha_banco   , ";
    	$SQL .= "sysapp_config_empresas.porta_banco ";
    	$SQL .= "FROM sysapp_config_empresas, ";
    	$SQL .= "sysapp_config_user_empresas_interfaces ";
    	$SQL .= "WHERE    cd_usuario                        = $usuario";
    	$SQL .= "AND      sysapp_config_empresas.cd_empresa = sysapp_config_user_empresas_interfaces.cd_empresa ";
    	$SQL .= "AND      sysapp_config_empresas.cd_empresa IN ($cd_empresa) ";
        $SQL .= "GROUP BY sysapp_config_empresas.cd_empresa    , ";
    	$SQL .= "sysapp_config_empresas.nome_empresa  , ";
    	$SQL .= "sysapp_config_empresas.hostname_banco, ";
    	$SQL .= "sysapp_config_empresas.nome_banco    , ";
    	$SQL .= "sysapp_config_empresas.usuario_banco , ";
    	$SQL .= "sysapp_config_empresas.senha_banco   , ";
    	$SQL .= "sysapp_config_empresas.porta_banco ";
    	$SQL .= "ORDER BY sysapp_config_empresas.nome_empresa ";

    	return $this->query($SQL);
    }

}
