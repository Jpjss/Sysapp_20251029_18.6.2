<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioRespostaCpl Model
 *
 */
class GlbQuestionarioRespostaCpl extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_resposta_cpl';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'id';

//    function beforeSave() {
//        if (!isset($this->data["GlbQuestionarioRespostaCpl"]["id"])) {
//            $this->data["GlbQuestionarioRespostaCpl"]["id"] = $this->nextval("seq_glb_questionario_resposta_cpl");
//        }
//    }

    public function novoId() {
        $sql = "SELECT max(id) from glb_questionario_resposta_cpl";
        $result = $this->query($sql);
        return (int) $result[0][0]['max']+1;
    }

    public function inserir(array $dados, $id) {
//        die("edu");
//            $i = $this->novoId();
//        foreach ($dados as $value) {
//            $maior = "SELECT max(id) from glb_questionario_resposta_cpl";
//            $result = $this->query($maior);
//            $id = (int)($result[0][0]['max']+1);
//       $teste ="INSERT INTO glb_questionario_resposta_cpl (cd_resposta, cd_pergunta, ds_resposta, cd_pergunta_cpl, id) VALUES (" . $dados[0]['cd_resposta'] . ", " . $dados[0]['cd_pergunta'] . ", '" . $dados[0]['ds_resposta'] . "', " . $dados[0]['cd_pergunta_cpl'] . ", " . $id . ")";
//        echo $teste;
//               die();

        @$this->query("INSERT INTO glb_questionario_resposta_cpl (cd_resposta, cd_pergunta, ds_resposta, cd_pergunta_cpl, id) VALUES (" . $dados[0]['cd_resposta'] . ", " . $dados[0]['cd_pergunta'] . ", '" . $dados[0]['ds_resposta'] . "', " . $dados[0]['cd_pergunta_cpl'] . ", " . $id . ")");

//        }
    }

}
