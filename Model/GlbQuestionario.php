<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionario Model
 *
 */
class GlbQuestionario extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_questionario';

    /**
     * Validação dos dados do formulário
     *
     * 
     */
    public $validate = array(
        'ds_questionario' => array('rule' => 'isUnique','message' => 'Esse questionário já existe'),
        'dt_vigencia_ini' => array('rule' => 'notempty'),
        'dt_vigencia_fim' => array('rule' => 'notempty'),
        'tipo_questionario' => array('rule' => 'notempty')
    );

    public function beforeSave($options = array()) {
        App::import('Component', 'Funcionalidades');
        if (!isset($this->data["GlbQuestionario"]["cd_questionario"])) {
            $this->data["GlbQuestionario"]["cd_questionario"] = $this->nextval("seq_glb_questionario");
        }
        if (isset($this->data["GlbQuestionario"]["dt_vigencia_ini"]) && isset($this->data["GlbQuestionario"]["dt_vigencia_fim"])) {
            $funcionalidades = new FuncionalidadesComponent();
            $this->data["GlbQuestionario"]["dt_vigencia_ini"] = $funcionalidades->formatarDataBd($this->data["GlbQuestionario"]["dt_vigencia_ini"]);
            $this->data["GlbQuestionario"]["dt_vigencia_fim"] = $funcionalidades->formatarDataBd($this->data["GlbQuestionario"]["dt_vigencia_fim"]);
            $this->data["GlbQuestionario"]["dt_cad"] = $funcionalidades->formatarDataBd($this->data["GlbQuestionario"]["dt_cad"]);
            
        }
    }

}
