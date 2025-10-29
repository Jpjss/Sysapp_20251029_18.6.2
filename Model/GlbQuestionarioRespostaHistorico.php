<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioRespostaHistorico extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_resposta_historico';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_resposta_historico';


    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioRespostaHistorico"]["cd_resposta_historico"])) {
            $this->data["GlbQuestionarioRespostaHistorico"]["cd_resposta_historico"] = $this->nextval("seq_glb_questionario_resposta_historico");
        }
    }

}
