<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionario Model
 *
 */
class GlbConfiguracaoSmsValor extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_configuracao_sms_valor';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_configuracao';

    /**
     * 
     * Validação dos dados do formulário
     * 
     */
     public $validate = array(
      'dt_vigencia_ini' => array('rule' => 'notempty'),
      'dt_vigencia_fim' => array('rule' => 'notempty'),
      'valor_sms' => array('rule' => 'notempty')
      );
     

    public function beforeSave($options = array()) {
        App::import('Component', 'Funcionalidades');
        $funcionalidades = new FuncionalidadesComponent();
        
        if (!isset($this->data["GlbConfiguracaoSmsValor"]["cd_configuracao"])) {
            $this->data["GlbConfiguracaoSmsValor"]["cd_configuracao"] = $this->nextval("seq_glb_configuracao_sms_valor");
        }

        if (isset($this->data["GlbConfiguracaoSmsValor"]["valor_sms"])) {
            $this->data["GlbConfiguracaoSmsValor"]["valor_sms"] = $funcionalidades->formatarMoedaBd($this->data["GlbConfiguracaoSmsValor"]["valor_sms"]);
        }

        if (isset($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_ini"]) && isset($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_fim"])) {
            $this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_ini"] = $funcionalidades->formatarDataBd($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_ini"]);
            $this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_fim"] = $funcionalidades->formatarDataBd($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_fim"]);
        }
    }

}
