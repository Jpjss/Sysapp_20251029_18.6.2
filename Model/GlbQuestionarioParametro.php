<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionario Model
 *
 */
class GlbQuestionarioParametro extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro';
    public $primaryKey = 'cd_parametro_questionario';

    /**
     * Validação dos dados do formulário
     */
    public $validate = array(
        'ds_parametro_questionario' => array('rule' => 'notempty')
    );

    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioParametro"]["cd_parametro_questionario"])) {
            $this->data["GlbQuestionarioParametro"]["cd_parametro_questionario"] = $this->nextval("seq_glb_questionario_parametro");
        }
    }

}
