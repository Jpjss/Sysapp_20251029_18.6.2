<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionario Model
 *
 */
class GlbPessoaObsContato extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_pessoa_obs_contato';

    
    public function gravar_contato($dados){
        return $this->query("INSERT INTO glb_pessoa_obs_contato(cd_emp, cd_pessoa, cd_ref_obs, dt_obs, obs_contato, cd_usu, tp_contato)VALUES ({$dados['cd_emp']}, {$dados['cd_pessoa']}, (select max(cd_ref_obs)+1 from glb_pessoa_obs_contato tmp where tmp.cd_pessoa = {$dados['cd_pessoa']} and tmp.cd_emp = {$dados['cd_emp']}), '{$dados['dt_obs']}', '{$dados['obs_contato']}', {$dados['cd_usu']}, {$dados['tp_contato']})");
    }

}
