<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarios Controller
 *
 * @property GlbQuestionarioParametro $GlbQuestionarioParametro
 */
class GlbQuestionarioGlbQuestionarioParametrosController extends AppController {

    public $helpers = array('Funcionalidades');

    /*
     * Parametros metodo
     * 
     */

    public function beforeFilter() {
        if (!in_array('pesquisaParametro', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash('Você não tem permissão para acessar essa página!');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'modulos'));
            
        }
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('Você não está logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
    }

    public function add() {
        if ($this->request->is('post')) {
            if(!isset($this->request->data['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario']) || !isset($this->request->data['GlbQuestionarioGlbQuestionarioParametro']['cd_parametro_questionario'])){
                $this->Session->setFlash('Você deve escolher uma pesquisa e um parâmetro!', 'erro');
                $this->redirect(array('action' => 'add'));
            }
            /**
             * @TODO 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioGlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->GlbQuestionarioGlbQuestionarioParametro->create();
            if ($this->GlbQuestionarioGlbQuestionarioParametro->save($this->request->data)) {
                $this->Session->setFlash('Relação Pesquisa/Parâmetro salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }
        $options = array("fields" => 'cd_questionario ');
        $glbQuestionarioGlbQuestionarioParametro = $this->GlbQuestionarioGlbQuestionarioParametro->find('all', $options);
        $existentes = "";
        foreach ($glbQuestionarioGlbQuestionarioParametro as $value) {
            $existentes[] = $value['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario'];
        }
        $this->loadModel("GlbQuestionario");
        $questionario = $this->GlbQuestionario->find('all', array('conditions' => array('NOT' => array('cd_questionario' => $existentes)), 'order' => array("dt_vigencia_ini" => "ASC"),));
        if ($questionario == null) {
            $this->Session->setFlash('Não existe pesquisa para ser relacionada!', 'alerta');
            $this->redirect(array('action' => 'index'));
        }

        $this->loadModel("GlbQuestionarioParametro");
        $parametro = $this->GlbQuestionarioParametro->find('all', array('conditions' => array('sts_parametro_cobranca' => 1), 'order' => array("dt_cad" => "ASC"),));
        if ($parametro == null) {
            $this->Session->setFlash('Não existe Parâmetro para ser relacionado', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('questionario', 'parametro'));
    }

    public function add_aniversariantes() {
        if ($this->request->is('post')) {
            if(!isset($this->request->data['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario']) || !isset($this->request->data['GlbQuestionarioGlbQuestionarioParametro']['cd_parametro_questionario'])){
                $this->Session->setFlash('Você deve escolher uma pesquisa e um parâmetro!', 'erro');
                $this->redirect(array('action' => 'add'));
            }
            /**
             * @TODO 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioGlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->GlbQuestionarioGlbQuestionarioParametro->create();
            if ($this->GlbQuestionarioGlbQuestionarioParametro->save($this->request->data)) {
                $this->Session->setFlash('Relação Pesquisa/Parâmetro aniversariante salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }

        $options = array("fields" => 'cd_questionario');
        $glbQuestionarioGlbQuestionarioParametro = $this->GlbQuestionarioGlbQuestionarioParametro->find('all', $options);
        $existentes = "";
        foreach ($glbQuestionarioGlbQuestionarioParametro as $value) {
            $existentes[] = $value['GlbQuestionarioGlbQuestionarioParametro']['cd_questionario'];
        }

        $this->loadModel("GlbQuestionario");
        $questionario = $this->GlbQuestionario->find('all', array('conditions' => array('NOT' => array('cd_questionario' => $existentes), 'tipo_questionario' => 1), 'order' => array("dt_vigencia_ini" => "ASC"),));
        if ($questionario == null) {
            $this->Session->setFlash('Não existe pesquisa para ser relacionada!', 'alerta');
            $this->redirect(array('action' => 'index'));
        }

        $this->loadModel("GlbQuestionarioParametro");
        $parametro = $this->GlbQuestionarioParametro->find('all', array('conditions' => array('sts_parametro_cobranca' => 1, 'tipo_questionario' => 1), 'order' => array("dt_cad" => "ASC"),));
        if ($parametro == null) {
            $this->Session->setFlash('Não existe Parâmetro para ser relacionado', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('questionario', 'parametro'));
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->GlbQuestionarioGlbQuestionarioParametro->recursive = 0;

        $options = array("fields" => 'gQuestionario.ds_questionario, GlbQuestionarioGlbQuestionarioParametro.cd_questionario, gQparametro.ds_parametro_questionario ',
            "joins" => array(
                array(
                    "table" => "glb_questionario",
                    "alias" => "gQuestionario",
                    "type" => "INNER",
                    "conditions" => array("gQuestionario.cd_questionario = GlbQuestionarioGlbQuestionarioParametro.cd_questionario")
                ), array(
                    "table" => "glb_questionario_parametro",
                    "alias" => "gQparametro",
                    "type" => "INNER",
                    "conditions" => array("gQparametro.cd_parametro_questionario = GlbQuestionarioGlbQuestionarioParametro.cd_parametro_questionario")
        )));
        $this->paginate = $options;
        $this->set('glbQuestionarioGlbQuestionarioParametro', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioGlbQuestionarioParametro->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta'));
        }
        $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioParametro.cd_questionario' => $id), "fields" => 'gQuestionario.ds_questionario, GlbQuestionarioGlbQuestionarioParametro.cd_questionario, gQparametro.ds_parametro_questionario ',
            "joins" => array(
                array(
                    "table" => "glb_questionario",
                    "alias" => "gQuestionario",
                    "type" => "INNER",
                    "conditions" => array("gQuestionario.cd_questionario = GlbQuestionarioGlbQuestionarioParametro.cd_questionario")
                ), array(
                    "table" => "glb_questionario_parametro",
                    "alias" => "gQparametro",
                    "type" => "INNER",
                    "conditions" => array("gQparametro.cd_parametro_questionario = GlbQuestionarioGlbQuestionarioParametro.cd_parametro_questionario")
        )));

        $this->set('glbQuestionarioGlbQuestionarioParametro', $this->GlbQuestionarioGlbQuestionarioParametro->find('first', $options));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            /**
             * @FIXME 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioGlbQuestionarioParametro"]["cd_emp"] = 1;


            $this->GlbQuestionarioGlbQuestionarioParametro->create();
            if ($this->GlbQuestionarioGlbQuestionarioParametro->save($this->request->data)) {

                $this->Session->setFlash('Relação Pesquisa / Parâmetro alterada com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }
        $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioParametro.cd_questionario' => $id));
        $this->request->data = $this->GlbQuestionarioGlbQuestionarioParametro->find('first', $options);

        $this->loadModel("GlbQuestionario");
        $questionario = $this->GlbQuestionario->find('first', array('conditions' => array('cd_questionario' => $id), 'order' => array("dt_vigencia_ini" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametro");
        $parametro = $this->GlbQuestionarioParametro->find('all', array('conditions' => array('sts_parametro_cobranca' => 1), 'order' => array("dt_cad" => "ASC"),));

        $this->set(compact('questionario', 'parametro'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->GlbQuestionarioGlbQuestionarioParametro->id = $id;

        if ($this->GlbQuestionarioGlbQuestionarioParametro->delete()) {
            $this->Session->setFlash('Relação Pesquisa/Parâmetro apagada com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
    }

}
