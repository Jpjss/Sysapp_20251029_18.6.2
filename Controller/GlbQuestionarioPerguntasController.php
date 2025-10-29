<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioPerguntas Controller
 *
 * @property GlbQuestionarioPergunta $GlbQuestionarioPergunta
 */
class GlbQuestionarioPerguntasController extends AppController {

    public function beforeFilter() {
        if (!in_array('perguntas', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash('Você não tem permissão para acessar essa página!');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'modulos'));
            
        }
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
        $this->GlbQuestionarioPergunta->recursive = 0;
        $this->set('glbQuestionarioPerguntas', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioPergunta->exists($id)) {
            throw new NotFoundException(__('Pergunta inválida'));
        }
        $options = array('conditions' => array('GlbQuestionarioPergunta.' . $this->GlbQuestionarioPergunta->primaryKey => $id));
        $this->set('glbQuestionarioPergunta', $this->GlbQuestionarioPergunta->find('first', $options));
        

        $this->loadModel("GlbQuestionarioPerguntaCpl");
        $respostas = $this->GlbQuestionarioPerguntaCpl->find('all', array('conditions' => array('cd_pergunta' => $id), 'fields' => array('ds_pergunta_cpl'),
        ));
        $this->set(compact('respostas'));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {

        if ($this->request->is('post')) {
            $this->request->data['GlbQuestionarioPergunta']['ds_pergunta'] = ucwords($this->request->data['GlbQuestionarioPergunta']['ds_pergunta']);
            $this->request->data['GlbQuestionarioPergunta']['obs'] = ucwords($this->request->data['GlbQuestionarioPergunta']['obs']);
            $this->GlbQuestionarioPergunta->create();
            if ($this->GlbQuestionarioPergunta->save($this->request->data)) {
                $this->Session->setFlash('A pergunta foi salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Falha ao salvar pergunta. Por favor, tente novamente.', 'erro');
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
        if (!$this->GlbQuestionarioPergunta->exists($id)) {
            throw new NotFoundException(__('Pergunta inválida'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['GlbQuestionarioPergunta']['ds_pergunta'] = ucwords($this->request->data['GlbQuestionarioPergunta']['ds_pergunta']);
            $this->request->data['GlbQuestionarioPergunta']['obs'] = ucwords($this->request->data['GlbQuestionarioPergunta']['obs']);
            if ($this->GlbQuestionarioPergunta->save($this->request->data)) {
                $this->Session->setFlash('A pergunta foi salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Falha ao salvar pergunta. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioPergunta.' . $this->GlbQuestionarioPergunta->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioPergunta->find('first', $options);
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
        $this->GlbQuestionarioPergunta->id = $id;
        if (!$this->GlbQuestionarioPergunta->exists()) {
            throw new NotFoundException(__('Pergunta inválida'));
        }
        $this->request->onlyAllow('post', 'delete');

        $this->loadModel("GlbQuestionarioGlbQuestionarioPergunta");
        $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioPergunta.cd_pergunta' => $id));
        $existe = $this->GlbQuestionarioGlbQuestionarioPergunta->find('first', $options);
        if (!empty($existe["GlbQuestionarioGlbQuestionarioPergunta"]["id"])) {
            $this->Session->setFlash('A pergunta não pode ser apagada, pois está relacionada a uma pesquisa.', 'erro');
            $this->redirect(array('action' => 'index'));
        }

        $this->loadModel("GlbQuestionarioPerguntaCpl");
        $this->GlbQuestionarioPerguntaCpl->deleteAll(array('cd_pergunta' => $id));

        if ($this->GlbQuestionarioPergunta->delete()) {
            $this->Session->setFlash('Pergunta deletada com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Falha ao deletar pergunta', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
