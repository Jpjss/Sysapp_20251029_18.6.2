<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioParametroDataUltCompras Controller
 *
 * @property GlbQuestionarioParametroDataUltCompra $GlbQuestionarioParametroDataUltCompra
 */
class GlbQuestionarioParametroDataUltComprasController extends AppController {

    /**
     * Método verifica se usuário está logado
     *
     * @return void
     */
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
        $this->GlbQuestionarioParametroDataUltCompra->recursive = 0;
        $options = array(
            'order' => array('valor_inicial' => 'ASC')
        );

        $this->paginate = $options;
        $this->set('glbQuestionarioParametroDataUltCompras', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioParametroDataUltCompra->exists($id)) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        $options = array('conditions' => array('GlbQuestionarioParametroDataUltCompra.' . $this->GlbQuestionarioParametroDataUltCompra->primaryKey => $id));
        $this->set('glbQuestionarioParametroDataUltCompra', $this->GlbQuestionarioParametroDataUltCompra->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->request->data["GlbQuestionarioParametroDataUltCompra"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametroDataUltCompra"]["dt_cad"] = date("Y-m-d");

            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroDataUltCompra"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroDataUltCompra"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroDataUltCompra->find('first', $options);
            if ($resultado != null) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'add'));
            }

            $this->GlbQuestionarioParametroDataUltCompra->create();
            if ($this->GlbQuestionarioParametroDataUltCompra->save($this->request->data)) {
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
        if (!$this->GlbQuestionarioParametroDataUltCompra->exists($id)) {
            throw new NotFoundException(__('Filtro inválido'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $options = array('conditions' => array('valor_inicial' => $this->request->data["GlbQuestionarioParametroDataUltCompra"]['valor_inicial'], 'valor_final' => $this->request->data["GlbQuestionarioParametroDataUltCompra"]['valor_final']));
            $resultado = $this->GlbQuestionarioParametroDataUltCompra->find('first', $options);
            
            if ($resultado != null && $resultado['GlbQuestionarioParametroDataUltCompra']['cd_parametro_data_ult_co'] != $this->request->data["GlbQuestionarioParametroDataUltCompra"]['cd_parametro_data_ult_compra']) {
                $this->Session->setFlash('Já existe um filtro com os mesmos valores inicias e finais.', 'erro');
                $this->redirect(array('action' => 'edit',$id));
            }
            
            if ($this->GlbQuestionarioParametroDataUltCompra->save($this->request->data)) {
                $this->Session->setFlash('Filtro salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar filtro. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioParametroDataUltCompra.' . $this->GlbQuestionarioParametroDataUltCompra->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioParametroDataUltCompra->find('first', $options);
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
        $this->GlbQuestionarioParametroDataUltCompra->id = $id;
        if (!$this->GlbQuestionarioParametroDataUltCompra->exists()) {
            throw new NotFoundException(__('Filtro inválido!'));
        }
        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $options = array('conditions' => array('GlbQuestionarioParametroVincDataUltCompra.cd_parametro_data_ult_compra' => $id));
        $existe = $this->GlbQuestionarioParametroVincDataUltCompra->find('first', $options);

        if ($existe != null) {
            $this->Session->setFlash('O filtro não pode ser apagado, pois já está vinculado a um parâmetro!', 'erro');
            $this->redirect(array('action' => 'index'));
        }

        $this->request->onlyAllow('post', 'delete');
        if ($this->GlbQuestionarioParametroDataUltCompra->delete()) {
            $this->Session->setFlash('Filtro apagado com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Erro ao apagar filtro. Por favor, tente novamente.', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
