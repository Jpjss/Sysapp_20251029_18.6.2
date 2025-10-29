<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class EstProdutoDepto extends AppModel { 
    

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'est_produto_depto';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_depto';
//    public $validate = array(
//        'status_atendimento' => array('rule' => 'notempty','message' => "Você deve escolher um status para o atendimento.",)
//           
//    );

//    function beforeSave() {
//        if (!isset($this->data["GlbQuestionarioResposta"]["cd_resposta"])) {
//            $this->data["GlbQuestionarioResposta"]["cd_resposta"] = $this->nextval("seq_glb_questionario_resposta");
//        }
//    }

}
