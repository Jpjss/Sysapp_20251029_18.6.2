<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioParametroFaixaValorMedioCompra Model
 *
 */
class GlbQuestionarioParametroFaixaValorMedioCompra extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'glb_questionario_parametro_faixa_valor_medio_compra';

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'cd_parametro_faixa_valor_medio_compra';

    public function beforeSave($options = array()) {
        App::import('Component', 'Funcionalidades');
        if (!isset($this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["cd_parametro_faixa_valor_medio_compra"])) {
            $this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["cd_parametro_faixa_valor_medio_compra"] = $this->nextval("seq_glb_questionario_parametro_faixa_valor_medio_compra");
        }
        if (isset($this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_inicial"]) && isset($this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_final"])) {
            $funcionalidades = new FuncionalidadesComponent();
            $this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_inicial"] = $funcionalidades->formatarMoedaBd($this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_inicial"]);
            $this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_final"] = $funcionalidades->formatarMoedaBd($this->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["valor_final"]);
        }
    }


    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'ds_parametro_faixa_valor_medio_compra' => array('rule' => 'isUnique','message'=>'Esse filtro já está cadastrado!'),
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
