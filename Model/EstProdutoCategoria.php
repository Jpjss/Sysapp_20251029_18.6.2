<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class EstProdutoCategoria extends AppModel { 
    

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'est_produto_categoria';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_categoria';
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
