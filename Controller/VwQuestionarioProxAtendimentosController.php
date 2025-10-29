<?php

App::uses('AppController', 'Controller');

/**
 * VwQuestionarios Controller
 *
 * @property VwQuestionario $VwQuestionario
 */
class VwQuestionarioProxAtendimentosController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->VwQuestionarioProxAtendimento->recursive = 0;
        $this->set('vwQuestionarios', $this->paginate());
    }

}
