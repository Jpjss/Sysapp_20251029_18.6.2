<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarios Controller
 *
 * @property GlbQuestionario $GlbQuestionario
 */
class GlbQuestionariosController extends AppController {

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
        $this->GlbQuestionario->recursive = 0;
        $this->set('glbQuestionarios', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionario->exists($id)) {
            throw new NotFoundException(__('Questionário inválido'));
        }
        $options = array('conditions' => array('GlbQuestionario.' . $this->GlbQuestionario->primaryKey => $id));
        $glbQuestionario = $this->GlbQuestionario->find('first', $options);

        $this->loadModel("GlbQuestionarioGlbQuestionarioPergunta");
//        $perguntas = $this->VwQuestionario->find('all', array('order' => array("prioridades_perguntas" => "ASC"), 'conditions' => array('cd_questionario' => $id), 'fields' => array('pergunta', 'cd_pergunta'), 'group' => array('VwQuestionario.pergunta', 'cd_pergunta', 'prioridades_perguntas')));
        $perguntas = $this->GlbQuestionarioGlbQuestionarioPergunta->find("all", array('conditions' => array('cd_questionario' => $id), 'fields' => "qPergunta.ds_pergunta",
            "joins" => array(
                array(
                    "table" => "glb_questionario_pergunta",
                    "alias" => "qPergunta",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioGlbQuestionarioPergunta.cd_pergunta = qPergunta.cd_pergunta")
                    )))
        );
        $this->set('glbQuestionario', $this->GlbQuestionario->find('first', $options));
        $this->set('perguntas', $perguntas);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            if ($this->data["GlbQuestionario"]["dt_vigencia_ini"] != "00/00/0000 00:00:00" || $this->data["GlbQuestionario"]["dt_vigencia_fim"] != "00/00/0000 00:00:00") {

                $this->GlbQuestionario->create();
                if ($this->GlbQuestionario->save($this->request->data)) {
                    $this->Session->setFlash('A pesquisa foi salva com sucesso!', 'sucesso');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash('Falha ao salvar pesquisa. Por favor, tente novamente.', 'erro');
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
        if (!$this->GlbQuestionario->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionário'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->data["GlbQuestionario"]["dt_vigencia_ini"] != "00/00/0000 00:00:00" || $this->data["GlbQuestionario"]["dt_vigencia_fim"] != "00/00/0000 00:00:00") {
                if ($this->GlbQuestionario->save($this->request->data)) {
                    $this->Session->setFlash('A pesquisa foi salva com sucesso', 'sucesso');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash('Falha ao salvar pesquisa. Por favor, tente novamente.', 'erro');
                }
            } else {
                $this->Session->setFlash('Data inválida!', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionario.' . $this->GlbQuestionario->primaryKey => $id));
            $this->request->data = $this->GlbQuestionario->find('first', $options);
//            $this->request->date["GlbQuestionario"]["data_vigencia_ini"] = "2013-10-01 18:00:01";
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
        $this->GlbQuestionario->id = $id;
        if (!$this->GlbQuestionario->exists()) {
            throw new NotFoundException(__('Pesquisa inválida'));
        }
        $this->request->onlyAllow('post', 'delete');

        $this->loadModel("GlbQuestionarioResposta");
        $options = array('conditions' => array('GlbQuestionarioResposta.cd_questionario' => $id));
        $existe = $this->GlbQuestionarioResposta->find('first', $options);

        if (isset($existe["GlbQuestionarioResposta"]["cd_resposta"])) {
            $this->Session->setFlash('A pesquisa não pode ser apagada, pois existe atendimento relacionado a ela.', 'erro');
            $this->redirect(array('action' => 'index'));
        }
        
        $this->loadModel("GlbQuestionarioGlbQuestionarioPergunta");
        $this->GlbQuestionarioGlbQuestionarioPergunta->deleteAll(array('cd_questionario' => $id));

        $this->loadModel("GlbQuestionarioGlbQuestionarioParametro");
        $this->GlbQuestionarioGlbQuestionarioParametro->deleteAll(array('cd_questionario' => $id));

        if ($this->GlbQuestionario->delete()) {
            $this->Session->setFlash('Pesquisa deletada com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Falha ao deletar pesquisa!', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
