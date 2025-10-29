<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioPerguntaCpls Controller
 *
 * @property GlbQuestionarioPerguntaCpl $GlbQuestionarioPerguntaCpl
 */
class GlbQuestionarioPerguntaCplsController extends AppController {

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
        $this->GlbQuestionarioPerguntaCpl->recursive = 0;
        $this->set('glbQuestionarioPerguntaCpls', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioPerguntaCpl->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario pergunta cpl'));
        }
        $options = array('conditions' => array('GlbQuestionarioPerguntaCpl.' . $this->GlbQuestionarioPerguntaCpl->primaryKey => $id));
        $this->set('glbQuestionarioPerguntaCpl', $this->GlbQuestionarioPerguntaCpl->find('first', $options));
    }

    public function listaResposta($id = null) {
        $this->paginate = array('conditions' => array('cd_pergunta' => $id), 'order' => array('prioridade' => 'ASC'));

        $this->set('glbQuestionarioPerguntaCpls', $this->paginate());
    }

    public function ordenar($id = null) {

        if ($this->request->is('post')) {
            if ($this->request->data["GlbQuestionarioPerguntaCpl"]["ordemAtualizada"] == null) {
                $this->Session->setFlash('Ordenação realizada com sucesso!', 'sucesso');
                $this->redirect(array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'listaResposta', $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta"]));
            }

            $ordemId = explode(".", $this->request->data["GlbQuestionarioPerguntaCpl"]["ordem"]);
            $ordemAtualizada = explode(".", $this->request->data["GlbQuestionarioPerguntaCpl"]["ordemAtualizada"]);
            for ($i = 0; $i < count($ordemId); $i++) {
                echo array_search($i, $ordemAtualizada) . "<br>";
                $dados[] = array("id" => $ordemId[$i], "prioridade" => array_search($i, $ordemAtualizada));
            }
            if ($this->GlbQuestionarioPerguntaCpl->saveMany($dados)) {
                $this->Session->setFlash('Ordenação realizada com sucesso!', 'sucesso');
                $this->redirect(array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'listaResposta', $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta"],$this->request->data["GlbQuestionarioPerguntaCpl"]["tp_pergunta"]));
            }
        }
        
        $glbQuestionarioPerguntaCpls = $this->GlbQuestionarioPerguntaCpl->find('all', array('conditions' => array('cd_pergunta' => $id), 'order' => array('GlbQuestionarioPerguntaCpl.prioridade' => 'ASC')
        ));
        $this->set(compact('glbQuestionarioPerguntaCpls'));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($id = null) {
        if ($this->request->is('post')) {
            /*
             * Verifica se já existe a resposta para essa pergunta
             */
            $perguntacpl = utf8_decode(ucwords(mb_strtolower($this->request->data['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl'])));
            $respostas = $this->GlbQuestionarioPerguntaCpl->find('all', array('conditions' => array('cd_pergunta' => $id), 'fields' => array('ds_pergunta_cpl'),));

            foreach ($respostas as $value) {
                if (ucwords(utf8_decode($value['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl'])) == $perguntacpl) {
                    $this->Session->setFlash('Essa resposta já foi cadastrada para essa pergunta!', 'erro');
                    $this->redirect(array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'add', $id));
                }
            }
            $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            if ($this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"] == "") {
                $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"] = 0;
            } else if ($this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"] >= 0) {
                $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"] = $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"] + 1;
            }
            #Passa todas as letras pra minusculo, depois somente a primeira pra maiusculo
            $this->request->data['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl'] = ucwords(mb_strtolower($this->request->data['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl']));

            if ($this->GlbQuestionarioPerguntaCpl->save($this->request->data)) {
                $this->Session->setFlash('Resposta salva com sucesso!', 'sucesso');
                $this->redirect(array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'listaResposta', $this->request->data["GlbQuestionarioPerguntaCpl"]["cd_pergunta"],$this->request->data["GlbQuestionarioPerguntaCpl"]["tp_pergunta"]));
            } else {
                $this->Session->setFlash('Erro ao salvar Resposta. Por favor, tente novamente.', 'erro');
            }
        }
        $respostas = $this->GlbQuestionarioPerguntaCpl->find('first', array('conditions' => array('cd_pergunta' => $id), 'fields' => array('cd_pergunta_cpl'), 'order' => array('GlbQuestionarioPerguntaCpl.cd_pergunta_cpl' => 'DESC')
        ));
        $prioridade = $this->GlbQuestionarioPerguntaCpl->find('first', array('conditions' => array('cd_pergunta' => $id), 'fields' => array('prioridade'), 'order' => array('GlbQuestionarioPerguntaCpl.prioridade' => 'DESC')));
        $this->set(compact('respostas', 'prioridade'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null, $direction = null) {
        if (!$this->GlbQuestionarioPerguntaCpl->exists($id)) {
            throw new NotFoundException(__('Resposta inválida'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->GlbQuestionarioPerguntaCpl->save($this->request->data)) {
                $this->Session->setFlash('Resposta salva com sucesso!', 'sucesso');
                $this->redirect(array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'listaResposta', $direction,$this->request->data['GlbQuestionarioPerguntaCpl']['tp_pergunta']));
            } else {
                $this->Session->setFlash('Erro ao salvar Resposta. Por favor, tente novamente.', 'ERRO');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioPerguntaCpl.' . $this->GlbQuestionarioPerguntaCpl->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioPerguntaCpl->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null, $direction = null, $tpPergunta = null) {
        $this->GlbQuestionarioPerguntaCpl->id = $id;
        if (!$this->GlbQuestionarioPerguntaCpl->exists()) {
            throw new NotFoundException(__('Invalid glb questionario pergunta cpl'));
        }
        $this->request->onlyAllow('post', 'delete');

        $this->loadModel("GlbQuestionarioGlbQuestionarioPergunta");
        $options = array('conditions' => array('GlbQuestionarioGlbQuestionarioPergunta.cd_pergunta' => $direction));
        $existe = $this->GlbQuestionarioGlbQuestionarioPergunta->find('first', $options);
        if (!empty($existe["GlbQuestionarioGlbQuestionarioPergunta"]["cd_questionario"])) {
            $this->loadModel("GlbQuestionarioResposta");
            $options = array('conditions' => array('GlbQuestionarioResposta.cd_questionario' => $existe["GlbQuestionarioGlbQuestionarioPergunta"]["cd_questionario"]));
            $existe2 = $this->GlbQuestionarioResposta->find('first', $options);
            if (!empty($existe2["GlbQuestionarioResposta"]["cd_resposta"])) {
                $this->Session->setFlash('A resposta não pode ser apagada, pois ela já participou de pesquisa.', 'erro');
                $this->redirect(array('action' => 'listaResposta', $direction, $tpPergunta));
            }
        }

        if ($this->GlbQuestionarioPerguntaCpl->delete()) {
            $this->Session->setFlash('Resposta deletada com sucesso!', 'sucesso');
            if ($direction != null) {
                $this->redirect(array('action' => 'listaResposta', $direction, $tpPergunta));
            } else {
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->Session->setFlash(__('Glb questionario pergunta cpl was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

}
