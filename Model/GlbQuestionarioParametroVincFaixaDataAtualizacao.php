<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioParametroVincFaixaDataAtualizacao extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_vinc_data_atualizacao';

    public function inserir(array $dados, $emp, $pquestionario) {
        foreach ($dados as $value) {
            $this->query('INSERT INTO glb_questionario_parametro_vinc_data_atualizacao (cd_emp, cd_parametro_questionario, cd_parametro_faixa_data_atualizacao) VALUES (' . $emp . ', ' . $pquestionario . ', ' . $value . ')');
        }
    }

    public function excluir($emp, $pquestionario) {
        $this->query("DELETE FROM glb_questionario_parametro_vinc_data_atualizacao WHERE cd_emp = '" . $emp . "' AND cd_parametro_questionario = '" . $pquestionario . "';");
    }

}
