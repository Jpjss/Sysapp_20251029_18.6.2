<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioParametroVincDataUltCompra extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_vinc_data_ult_compra';

    public function inserir(array $dados, $emp, $pquestionario) {
        foreach ($dados as $value) {
            $this->query('INSERT INTO glb_questionario_parametro_vinc_data_ult_compra (cd_emp, cd_parametro_questionario, cd_parametro_data_ult_compra) VALUES (' . $emp . ', ' . $pquestionario . ', ' . $value . ')');
        }
    }

    public function excluir($emp, $pquestionario) {
        $this->query("DELETE FROM glb_questionario_parametro_vinc_data_ult_compra WHERE cd_emp = '" . $emp . "' AND cd_parametro_questionario = '" . $pquestionario ."';");
    }

}
