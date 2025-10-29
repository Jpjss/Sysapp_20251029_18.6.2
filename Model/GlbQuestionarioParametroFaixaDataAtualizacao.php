<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioParametroFaixaValorMedioCompra Model
 *
 */
class GlbQuestionarioParametroFaixaDataAtualizacao extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_faixa_data_atualizacao';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_parametro_faixa_data_atualizacao';

    public function beforeSave($options = array()) {
        App::import('Component', 'Funcionalidades');
        if (!isset($this->data["GlbQuestionarioParametroFaixaDataAtualizacao"]["cd_parametro_faixa_data_atualizacao"])) {
            $this->data["GlbQuestionarioParametroFaixaDataAtualizacao"]["cd_parametro_faixa_data_atualizacao"] = $this->nextval("seq_glb_questionario_parametro_faixa_data_atualizacao");
        }

    }


    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'ds_parametro_faixa_data_atualizacao' => array('rule' => 'isUnique','message'=>'Esse filtro já está cadastrado!'),
        'valor_inicial' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'valor_final' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'cd_usu_cad' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'dt_cad' => array(
            'date' => array(
                'rule' => array('date'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );

}
