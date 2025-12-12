<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioPerguntaCpl Model
 *
 */
class GlbQuestionarioPerguntaCpl extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_pergunta_cpl';

    /**
     * Validação dos dados do formulário
     *
     * 
     */
    public $validate = array(
        'ds_pergunta_cpl' => array('rule' => 'notempty')
    );

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'id';

    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioPerguntaCpl"]["id"])) {
            $this->data["GlbQuestionarioPerguntaCpl"]["id"] = $this->nextval("seq_glb_questionario_pergunta_cpl");
        }
    }

}
