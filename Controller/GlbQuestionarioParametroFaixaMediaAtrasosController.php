<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioParametroFaixaQuantidadeCompras Controller
 *
 * @property GlbQuestionarioParametroFaixaMediaAtraso $GlbQuestionarioParametroFaixaMediaAtraso
 */
class GlbQuestionarioParametroFaixaMediaAtrasosController extends AppController {

    /**
     * Método verifica se usuário está logado
     *
     * @return void
     */
    public function beforeFilter() {
        if (!in_array('filtros', $this->Session->read('Questionarios.permissoes'))) {
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
        $this->GlbQuestionarioParametroFaixaMediaAtraso->recursive = 0;
        $options = array(
            'order' => array('valor_inicial' => 'ASC')
        );

        $this->paginate = $options;
        $this->set('glbQuestionarioParametroFaixaMediaAtrasos', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioParametroFaixaMediaAtraso->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario parametro faixa quantidade compra'));
        }
        $options = array('conditions' => array('GlbQuestionarioParametroFaixaMediaAtraso.' . $this->GlbQuestionarioParametroFaixaMediaAtraso->primaryKey => $id));
        $this->set('glbQuestionarioParametroFaixaMediaAtraso', $this->GlbQuestionarioParametroFaixaMediaAtraso->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]["dt_cad"] = date("Y-m-d");
            
            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroFaixaMediaAtraso->find('first', $options);
            if ($resultado != null) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'add'));
            }
            
            $this->GlbQuestionarioParametroFaixaMediaAtraso->create();
            if ($this->GlbQuestionarioParametroFaixaMediaAtraso->save($this->request->data)) {
                $this->Session->setFlash('Filtro salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar filtro. Por favor, tente novamente.', 'erro');
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
        if (!$this->GlbQuestionarioParametroFaixaMediaAtraso->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario parametro faixa quantidade compra'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroFaixaMediaAtraso->find('first', $options);
            if ($resultado != null && $resultado['GlbQuestionarioParametroFaixaMediaAtraso']['cd_parametro_faixa_me'] != $this->request->data["GlbQuestionarioParametroFaixaMediaAtraso"]['cd_parametro_faixa_media_atraso']) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'edit',$id));
            }
            
            if ($this->GlbQuestionarioParametroFaixaMediaAtraso->save($this->request->data)) {
                $this->Session->setFlash('Filtro salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar filtro. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioParametroFaixaMediaAtraso.' . $this->GlbQuestionarioParametroFaixaMediaAtraso->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioParametroFaixaMediaAtraso->find('first', $options);
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
        $this->GlbQuestionarioParametroFaixaMediaAtraso->id = $id;
        if (!$this->GlbQuestionarioParametroFaixaMediaAtraso->exists()) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $options = array('conditions' => array('GlbQuestionarioParametroVincFaixaQuantidadeCompra.cd_parametro_faixa_quantidade_compra' => $id));
        $existe = $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->find('first', $options);

        if ($existe != null) {
            $this->Session->setFlash('O filtro não pode ser apagado, pois já está vinculado a um parâmetro!', 'erro');
            $this->redirect(array('action' => 'index'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioParametroFaixaMediaAtraso->delete()) {
            $this->Session->setFlash('Filtro apagado com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Erro ao apagar filtro. Por favor, tente novamente.', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
