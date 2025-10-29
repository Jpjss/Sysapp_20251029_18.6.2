<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioResposta extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_resposta';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_resposta';
    public $validate = array(
        'status_atendimento' => array('rule' => 'notempty','message' => "Você deve escolher um status para o atendimento.",)
           
    );

    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioResposta"]["cd_resposta"])) {
            $this->data["GlbQuestionarioResposta"]["cd_resposta"] = $this->nextval("seq_glb_questionario_resposta");
        }
    }
    
    public function atendimento_begin() {
        $this->query("BEGIN;");
        return $this->query(" LOCK TABLE glb_questionario_controle_prox_atend IN EXCLUSIVE MODE;");
    }
    public function atendimento_commit() {
        return $this->query("commit;");
    }

}
