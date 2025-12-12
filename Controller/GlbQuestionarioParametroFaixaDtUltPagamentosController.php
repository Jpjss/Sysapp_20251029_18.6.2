<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioParametroFaixaDtUltPagamentos Controller
 *
 * @property GlbQuestionarioParametroFaixaDtUltPagamento $GlbQuestionarioParametroFaixaDtUltPagamento
 */
class GlbQuestionarioParametroFaixaDtUltPagamentosController extends AppController {

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
        $this->GlbQuestionarioParametroFaixaDtUltPagamento->recursive = 0;
        $options = array(
            'order' => array('valor_inicial' => 'ASC')
        );

        $this->paginate = $options;
        $this->set('glbQuestionarioParametroFaixaDtUltPagamentos', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioParametroFaixaDtUltPagamento->exists($id)) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        $options = array('conditions' => array('GlbQuestionarioParametroFaixaDtUltPagamento.' . $this->GlbQuestionarioParametroFaixaDtUltPagamento->primaryKey => $id));
        $this->set('glbQuestionarioParametroFaixaDtUltPagamento', $this->GlbQuestionarioParametroFaixaDtUltPagamento->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]["dt_cad"] = date("Y-m-d");
            
            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroFaixaDtUltPagamento->find('first', $options);
            if ($resultado != null) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'add'));
            }
            
            $this->GlbQuestionarioParametroFaixaDtUltPagamento->create();
            if ($this->GlbQuestionarioParametroFaixaDtUltPagamento->save($this->request->data)) {
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
        if (!$this->GlbQuestionarioParametroFaixaDtUltPagamento->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario parametro faixa quantidade compra'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
                        
            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroFaixaDtUltPagamento->find('first', $options);
            if ($resultado != null && $resultado['GlbQuestionarioParametroFaixaDtUltPagamento']['cd_parametro_faixa'] != $this->request->data["GlbQuestionarioParametroFaixaDtUltPagamento"]['cd_parametro_faixa_dt_ult_pagamento']) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'edit',$id));
            }
            
            if ($this->GlbQuestionarioParametroFaixaDtUltPagamento->save($this->request->data)) {
              $this->Session->setFlash('Filtro salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar filtro. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioParametroFaixaDtUltPagamento.' . $this->GlbQuestionarioParametroFaixaDtUltPagamento->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioParametroFaixaDtUltPagamento->find('first', $options);
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
        $this->GlbQuestionarioParametroFaixaDtUltPagamento->id = $id;
        if (!$this->GlbQuestionarioParametroFaixaDtUltPagamento->exists()) {
            throw new NotFoundException(__('Filtro inválido'));
        }
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $options = array('conditions' => array('GlbQuestionarioParametroVincFaixaQuantidadeCompra.cd_parametro_faixa_quantidade_compra' => $id));
        $existe = $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->find('first', $options);

        if ($existe != null) {
            $this->Session->setFlash('O filtro não pode ser apagado, pois já está vinculado a um parâmetro!', 'erro');
            $this->redirect(array('action' => 'index'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioParametroFaixaDtUltPagamento->delete()) {
            $this->Session->setFlash('Filtro apagado com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Erro ao apagar filtro. Por favor, tente novamente.', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
