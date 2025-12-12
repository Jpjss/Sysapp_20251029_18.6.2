<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarios Controller
 *
 * @property GlbConfiguracaoSmsValor $GlbConfiguracaoSmsValor
 */
class GlbConfiguracaoSmsValorController extends AppController {

    public $helpers = array('Funcionalidades');

    public function beforeFilter() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('Você não está logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->GlbConfiguracaoSmsValor->recursive = 0;
        $this->set('glbConfiguracaoSmsValores', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbConfiguracaoSmsValor->exists($id)) {
            throw new NotFoundException(__('Questionário inválido'));
        }
        $options = array('conditions' => array('GlbConfiguracaoSmsValor.' . $this->GlbConfiguracaoSmsValor->primaryKey => $id));
        $this->set('glbConfiguracaoSmsValor', $this->GlbConfiguracaoSmsValor->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            if ($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_ini"] != "00/00/0000" || $this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_fim"] != "00/00/0000") {

                $this->GlbConfiguracaoSmsValor->create();
                if ($this->GlbConfiguracaoSmsValor->save($this->request->data)) {
                    $this->Session->setFlash('Período e valor salvos com sucesso!', 'sucesso');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash('Falha ao salvar. Por favor, tente novamente.', 'erro');
                }
            } else {
                $this->Session->setFlash('Data inválida!', 'erro');
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->GlbConfiguracaoSmsValor->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionário'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_ini"] != "00/00/0000" || $this->data["GlbConfiguracaoSmsValor"]["dt_vigencia_fim"] != "00/00/0000") {
                if ($this->GlbConfiguracaoSmsValor->save($this->request->data)) {
                    $this->Session->setFlash('A pesquisa foi salva com sucesso', 'sucesso');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash('Falha ao salvar pesquisa. Por favor, tente novamente.', 'erro');
                }
            } else {
                $this->Session->setFlash('Data inválida!', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbConfiguracaoSmsValor.' . $this->GlbConfiguracaoSmsValor->primaryKey => $id));
            $this->request->data = $this->GlbConfiguracaoSmsValor->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->GlbConfiguracaoSmsValor->id = $id;
        if (!$this->GlbConfiguracaoSmsValor->exists()) {
            throw new NotFoundException(__('Pesquisa inválida'));
        }
        $this->request->onlyAllow('post', 'delete');

        if ($this->GlbConfiguracaoSmsValor->delete()) {
            $this->Session->setFlash('Configuração deletada com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Falha ao deletar configuração!', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
