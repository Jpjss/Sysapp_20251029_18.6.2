<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class GlbQuestionarioParametroVincFaixaDtUltPagamento extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_vinc_dt_ult_pagamento';

    public function inserir(array $dados, int $emp, int $pquestionario) {
        foreach ($dados as $value) {
            $this->query('INSERT INTO glb_questionario_parametro_vinc_dt_ult_pagamento (cd_emp, cd_parametro_questionario, cd_parametro_faixa_dt_ult_pagamento) VALUES (' . $emp . ', ' . $pquestionario . ', ' . $value . ')');
        }
    }

    public function excluir($emp, $pquestionario) {
        $this->query("DELETE FROM glb_questionario_parametro_vinc_dt_ult_pagamento WHERE cd_emp = '" . $emp . "' AND cd_parametro_questionario = '" . $pquestionario . "';");
    }

}
