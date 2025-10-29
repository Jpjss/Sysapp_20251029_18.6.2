<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioParametroFaixaValorMedioCompras Controller
 *
 * @property GlbQuestionarioParametroFaixaValorMedioCompra $GlbQuestionarioParametroFaixaValorMedioCompra
 */
class GlbQuestionarioParametroFaixaValorMedioComprasController extends AppController {
    
    public $helpers = array("Formatacao");
    public $components = array('Funcionalidades');

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
        $this->GlbQuestionarioParametroFaixaValorMedioCompra->recursive = 0;
        $options = array(
            'order' => array('valor_inicial' => 'ASC')
        );

        $this->paginate = $options;
        $this->set('glbQuestionarioParametroFaixaValorMedioCompras', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioParametroFaixaValorMedioCompra->exists($id)) {
            throw new NotFoundException(__('Filtro inválido'));
        }
        $options = array('conditions' => array('GlbQuestionarioParametroFaixaValorMedioCompra.' . $this->GlbQuestionarioParametroFaixaValorMedioCompra->primaryKey => $id));
        $this->set('glbQuestionarioParametroFaixaValorMedioCompra', $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {

            $this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]["dt_cad"] = date("Y-m-d");
            
            $options = array('conditions' => array('valor_inicial' => $this->Funcionalidades->formatarMoedaBd($this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]['valor_inicial']), 'valor_final' => $this->Funcionalidades->formatarMoedaBd($this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]['valor_final'])));
            $resultado = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('first', $options);
            if ($resultado != null) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'add'));
            }
            
            $this->GlbQuestionarioParametroFaixaValorMedioCompra->create();
            if ($this->GlbQuestionarioParametroFaixaValorMedioCompra->save($this->request->data)) {
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
        if (!$this->GlbQuestionarioParametroFaixaValorMedioCompra->exists($id)) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {

            $options = array('conditions' => array('valor_inicial' => $this->Funcionalidades->formatarMoedaBd($this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]['valor_inicial']), 'valor_final' => $this->Funcionalidades->formatarMoedaBd($this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]['valor_final'])));
            $resultado = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('first', $options);

            if ($resultado != null && $resultado['GlbQuestionarioParametroFaixaValorMedioCompra']['cd_parametro_fai'] != $this->request->data["GlbQuestionarioParametroFaixaValorMedioCompra"]['cd_parametro_faixa_valor_medio_compra']) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'edit',$id));
            }
            
            if ($this->GlbQuestionarioParametroFaixaValorMedioCompra->save($this->request->data)) {
                $this->Session->setFlash('Filtro salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar filtro. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioParametroFaixaValorMedioCompra.' . $this->GlbQuestionarioParametroFaixaValorMedioCompra->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('first', $options);
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
        $this->GlbQuestionarioParametroFaixaValorMedioCompra->id = $id;
        if (!$this->GlbQuestionarioParametroFaixaValorMedioCompra->exists()) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        
        $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
        $options = array('conditions' => array('GlbQuestionarioParametroVincValorMedioCompra.cd_parametro_faixa_valor_medio_compra' => $id));
        $existe = $this->GlbQuestionarioParametroVincValorMedioCompra->find('first', $options);

        if ($existe != null) {
            $this->Session->setFlash('O filtro não pode ser apagado, pois já está vinculado a um parâmetro!', 'erro');
            $this->redirect(array('action' => 'index'));
        }
        
        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioParametroFaixaValorMedioCompra->delete()) {
            $this->Session->setFlash('Filtro apagado com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Erro ao apagar filtro. Por favor, tente novamente.', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
