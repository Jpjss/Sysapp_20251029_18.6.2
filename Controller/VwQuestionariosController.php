<?php

App::uses('AppController', 'Controller');

/**
 * VwQuestionarios Controller
 *
 * @property VwQuestionario $VwQuestionario
 */
class VwQuestionariosController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->VwQuestionario->recursive = 0;
        $this->set('vwQuestionarios', $this->paginate());
    }

}
