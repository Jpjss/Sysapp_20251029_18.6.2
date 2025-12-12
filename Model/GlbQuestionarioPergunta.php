<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioPergunta Model
 *
 */
class GlbQuestionarioPergunta extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_pergunta';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_pergunta';

    /**
     * Validação dos dados do formulário
     */
    public $validate = array(
        'ds_pergunta' => array('rule' => 'isUnique','message'=>'Essa pergunta já está cadastrada!'),
        'tp_pergunta' => array('rule' => 'notempty')
    );
    

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'cd_pergunta';

    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioPergunta"]["cd_pergunta"])) {
            $this->data["GlbQuestionarioPergunta"]["cd_pergunta"] = $this->nextval("seq_glb_questionario_pergunta");
        }
    }

}
