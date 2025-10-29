<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioRespostaCpls Controller
 *
 * @property GlbQuestionarioRespostaCpl $GlbQuestionarioRespostaCpl
 */
class GlbQuestionarioRespostaCplsController extends AppController {

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
        $this->GlbQuestionarioRespostaCpl->recursive = 0;
        $this->set('glbQuestionarioRespostaCpls', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioRespostaCpl->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta cpl'));
        }
        $options = array('conditions' => array('GlbQuestionarioRespostaCpl.' . $this->GlbQuestionarioRespostaCpl->primaryKey => $id));
        $this->set('glbQuestionarioRespostaCpl', $this->GlbQuestionarioRespostaCpl->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->GlbQuestionarioRespostaCpl->create();
            if ($this->GlbQuestionarioRespostaCpl->save($this->request->data)) {
                $this->Session->setFlash(__('The glb questionario resposta cpl has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The glb questionario resposta cpl could not be saved. Please, try again.'));
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
        if (!$this->GlbQuestionarioRespostaCpl->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta cpl'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->GlbQuestionarioRespostaCpl->save($this->request->data)) {
                $this->Session->setFlash(__('The glb questionario resposta cpl has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The glb questionario resposta cpl could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioRespostaCpl.' . $this->GlbQuestionarioRespostaCpl->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioRespostaCpl->find('first', $options);
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
        $this->GlbQuestionarioRespostaCpl->id = $id;
        if (!$this->GlbQuestionarioRespostaCpl->exists()) {
            throw new NotFoundException(__('Invalid glb questionario resposta cpl'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioRespostaCpl->delete()) {
            $this->Session->setFlash(__('Glb questionario resposta cpl deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Glb questionario resposta cpl was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

}
