<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioParametroFaixaQuantidadeCompra Model
 *
 */
class GlbQuestionarioParametroFaixaDataCadastro extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_faixa_data_cadastro';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_parametro_faixa_data_cadastro';

    public function beforeSave($options = array()) {
        if (!isset($this->data["GlbQuestionarioParametroFaixaDataCadastro"]["cd_parametro_faixa_data_cadastro"])) {
            $this->data["GlbQuestionarioParametroFaixaDataCadastro"]["cd_parametro_faixa_data_cadastro"] = $this->nextval("seq_glb_questionario_parametro_faixa_data_cadastro");
        }
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'ds_parametro_faixa_data_cadastro' => array('rule' => 'isUnique','message'=>'Esse filtro já está cadastrado!'),
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
