<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioParametroVincFaixaMediaAtraso extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_vinc_media_atraso';

    public function inserir(array $dados, $emp, $pquestionario) {
        foreach ($dados as $value) {
            $this->query('INSERT INTO glb_questionario_parametro_vinc_media_atraso (cd_emp, cd_parametro_questionario, cd_parametro_faixa_media_atraso) VALUES (' . $emp . ', ' . $pquestionario . ', ' . $value . ')');
        }
    }

    public function excluir($emp, $pquestionario) {
        $this->query("DELETE FROM glb_questionario_parametro_vinc_media_atraso WHERE cd_emp = '" . $emp . "' AND cd_parametro_questionario = '" . $pquestionario . "';");
    }

}
