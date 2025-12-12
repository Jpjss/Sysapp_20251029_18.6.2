<?php
App::uses('AppModel', 'Model');
/**
 * VwQuestionario Model
 *
 */
class VwQuestionario extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'vw_questionario';

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'prioridade_respostas';

}
