<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioGlbQuestionarioPergunta Model
 *
 */
class GlbQuestionarioGlbQuestionarioPergunta extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_glb_questionario_pergunta';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'id';

//    function beforeSave() {
//        if (empty($this->data["GlbQuestionarioGlbQuestionarioPergunta"]["id"])) {
//            return $this->data["GlbQuestionarioGlbQuestionarioPergunta"]["id"] = $this->nextval("seq_glb_questionario_glb_questionario_pergunta");
//        }
////        return true;
//    }
    function novoId(){
        return $this->nextval("seq_glb_questionario_glb_questionario_pergunta");
    }


}
