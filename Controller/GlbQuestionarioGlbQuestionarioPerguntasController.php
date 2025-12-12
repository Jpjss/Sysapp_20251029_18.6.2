<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioGlbQuestionarioPerguntas Controller
 *
 * @property GlbQuestionarioGlbQuestionarioPergunta $GlbQuestionarioGlbQuestionarioPergunta
 */
class GlbQuestionarioGlbQuestionarioPerguntasController extends AppController {

    public $helpers = array('Funcionalidades');

    public function beforeFilter() {
        if (!in_array('pesquisas', $this->Session->read('Questionarios.permissoes'))) {
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
        $this->GlbQuestionarioGlbQuestionarioPergunta->recursive = 0;
        $this->set('glbQuestionarioGlbQuestionarioPerguntas', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioGlbQuestionarioPergunta->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario glb questionario pergunta'));
        }
        $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioPergunta.' . $this->GlbQuestionarioGlbQuestionarioPergunta->primaryKey => $id));
        $this->set('glbQuestionarioGlbQuestionarioPergunta', $this->GlbQuestionarioGlbQuestionarioPergunta->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($id = null) {
        if ($this->request->is('post')) {
            if (!empty($this->request->data["GlbQuestionarioGlbQuestionarioPergunta"]["perguntas"])) {
                $this->GlbQuestionarioGlbQuestionarioPergunta->deleteAll(array('cd_questionario' => $id), false);
                $todasPerguntas = $this->request->data["GlbQuestionarioGlbQuestionarioPergunta"]["perguntas"];
                $questionario = $this->request->data["GlbQuestionarioGlbQuestionarioPergunta"]["cd_questionario"];
                $novoId = $this->GlbQuestionarioGlbQuestionarioPergunta->novoId();
                foreach ($todasPerguntas as $pergunta) {

                    $dados[] = array("id" => $novoId, "cd_pergunta" => $pergunta, "cd_questionario" => $questionario);
                    $novoId++;
                }
                if ($this->GlbQuestionarioGlbQuestionarioPergunta->saveMany($dados)) {
                    $this->Session->setFlash('A pergunta foi relacionada com sucesso!', 'sucesso');
                    $this->redirect(array('controller' => 'GlbQuestionarios', 'action' => 'index'));
                } else {
                    $this->Session->setFlash('Falha ao relacionar pergunta. Por favor, tente novamente.', 'erro');
                }
            } else {
                $this->Session->setFlash('Você deve selecionar uma pergunta. Por favor, tente novamente.', 'erro');
            }
        }
        $pergRelacionadas = $this->GlbQuestionarioGlbQuestionarioPergunta->find('all', array('conditions' => array('cd_questionario' => $id), 'fields' => 'cd_pergunta'));

        $this->loadModel("GlbQuestionarioPergunta");
        $perguntas = $this->GlbQuestionarioPergunta->find('all', array('order' => 'GlbQuestionarioPergunta.cd_pergunta ASC'));
        if ($perguntas == null) {
            $this->Session->setFlash('Não existem perguntas cadastradas!', 'sucesso');
            $this->redirect(array('controller' => 'GlbQuestionarios', 'action' => 'index'));
        }
        $this->loadModel("GlbQuestionario");
        $questionario = $this->GlbQuestionario->find('all', array('conditions' => array('cd_questionario' => $id)));
        $this->set(compact('perguntas', 'questionario', 'pergRelacionadas'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->GlbQuestionarioGlbQuestionarioPergunta->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario glb questionario pergunta'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->GlbQuestionarioGlbQuestionarioPergunta->save($this->request->data)) {
                $this->Session->setFlash(__('The glb questionario glb questionario pergunta has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The glb questionario glb questionario pergunta could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioPergunta.' . $this->GlbQuestionarioGlbQuestionarioPergunta->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioGlbQuestionarioPergunta->find('first', $options);
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
        $this->GlbQuestionarioGlbQuestionarioPergunta->id = $id;
        if (!$this->GlbQuestionarioGlbQuestionarioPergunta->exists()) {
            throw new NotFoundException(__('Invalid glb questionario glb questionario pergunta'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioGlbQuestionarioPergunta->delete()) {
            $this->Session->setFlash(__('Glb questionario glb questionario pergunta deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Glb questionario glb questionario pergunta was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

}
