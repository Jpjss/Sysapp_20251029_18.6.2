<?php

App::uses('AppController', 'Controller');

/**
 * VwQuestionarios Controller
 *
 * @property VwRcLancCplDetalhes $VwRcLancCplDetalhes
 */
class VwRcLancCplDetalhesController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->VwRcLancCplDetalhes->recursive = 0;
        $this->set('VwRcLancCplDetalhes', $this->paginate());
    }

    /**
     * listar method
     * 
     */
    public function listar() {
        $options = array(
        'conditions' =>
            array('cd_lanc' => $this->request->data["cd_lanc"],
//               'CD_EMP' => 1,
                'cd_filial' => $this->request->data["cd_filial"]),
        'order' => array('parc'));
        $parcelas = $this->VwRcLancCplDetalhe->find('all', $options);
        $this->set(compact('parcelas'));
        $this->layout = 'historico';
        $this->render('parcelas');
    }

}
