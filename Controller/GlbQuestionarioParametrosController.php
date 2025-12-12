<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarios Controller
 *
 * @property GlbQuestionarioParametro $GlbQuestionarioParametro
 */
class GlbQuestionarioParametrosController extends AppController {

    public $components = array('Funcionalidades');

    /*
     * Parametros metodo
     * 
     */

    public function beforeFilter() {
        if (!in_array('parametros', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash('Você não tem permissão para acessar essa página!');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'modulos'));
        }
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('Você não está logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
    }

    public function parametros() {
        if ($this->request->is('post')) {
            /**
             * @FIXME 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["cd_filial"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["regiao_cliente"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_alt_cad"] = $this->Session->read('Questionarios.cd_usu');

            $filiais_ult_compra = ',';
            foreach ($this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] as $value) {
                $filiais_ult_compra .= $value . ",";
            }
//            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = substr($filiais_ult_compra, 1);
            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = $filiais_ult_compra;
            $this->GlbQuestionarioParametro->create();

            if ($this->GlbQuestionarioParametro->save($this->request->data)) {
                if ($this->data['GlbQuestionarioParametro']['qtdCompras'] != NULL) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->inserir($this->data['GlbQuestionarioParametro']['qtdCompras'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
                    $this->GlbQuestionarioParametroVincDataUltCompra->inserir($this->data['GlbQuestionarioParametro']['dtUltCompra'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }
                if (isset($this->data['GlbQuestionarioParametro']['vlrUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
                    $this->GlbQuestionarioParametroVincValorUltCompra->inserir($this->data['GlbQuestionarioParametro']['vlrUltCompra'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }
                if (isset($this->data['GlbQuestionarioParametro']['vlrMedio'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
                    $this->GlbQuestionarioParametroVincValorMedioCompra->inserir($this->data['GlbQuestionarioParametro']['vlrMedio'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }

//                if (isset($this->data['GlbQuestionarioParametro']['dtMediaAtraso'])) {
//                    $this->loadModel("GlbQuestionarioParametroVincFaixaMediaAtraso");
//                    $this->GlbQuestionarioParametroVincFaixaMediaAtraso->inserir($this->data['GlbQuestionarioParametro']['dtMediaAtraso'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
//                }
                if (isset($this->data['GlbQuestionarioParametro']['dtCadastro'])) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaDataCadastro");
                    $this->GlbQuestionarioParametroVincFaixaDataCadastro->inserir($this->data['GlbQuestionarioParametro']['dtCadastro'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }

                if (isset($this->data['GlbQuestionarioParametro']['dtAtualizacao'])) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaDataAtualizacao");
                    $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->inserir($this->data['GlbQuestionarioParametro']['dtAtualizacao'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }

//                if (isset($this->data['GlbQuestionarioParametro']['dtUltPagamento'])) {
//                    $this->loadModel("GlbQuestionarioParametroVincFaixaDtUltPagamento");
//                    $this->GlbQuestionarioParametroVincFaixaDtUltPagamento->inserir($this->data['GlbQuestionarioParametro']['dtUltPagamento'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
//                }

                $this->Session->setFlash('O parâmetro foi salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }

        $this->loadModel("GlbQuestionarioParametroFaixaQuantidadeCompra");
        $faixaQuantidade = $this->GlbQuestionarioParametroFaixaQuantidadeCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroFaixaValorMedioCompra");
        $valorMedio = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroFaixaValorUltCompra");
        $valorUltCompra = $this->GlbQuestionarioParametroFaixaValorUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroDataUltCompra");
        $dataUltCompra = $this->GlbQuestionarioParametroDataUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

//        $this->loadModel("GlbQuestionarioParametroFaixaMediaAtraso");
//        $mediaAtraso = $this->GlbQuestionarioParametroFaixaMediaAtraso->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroFaixaDataCadastro");
        $dtCadastro = $this->GlbQuestionarioParametroFaixaDataCadastro->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroFaixaDataAtualizacao");
        $dtAtualizacao = $this->GlbQuestionarioParametroFaixaDataAtualizacao->find('all', array('order' => array("valor_inicial" => "ASC"),));

//        $this->loadModel("GlbQuestionarioParametroFaixaDtUltPagamento");
//        $dtUltPagamento = $this->GlbQuestionarioParametroFaixaDtUltPagamento->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("PrcRegiaoFilial");
        $regiaoFilial = $this->PrcRegiaoFilial->find('all', array('order' => array("ds_regiao" => "ASC"),));

        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'conditions' => array('sts_filial = 1')));

        $this->set(compact('valorMedio', 'faixaQuantidade', 'valorUltCompra', 'dataUltCompra', 'regiaoFilial', 'filiais', 'mediaAtraso', 'dtCadastro', 'dtAtualizacao', 'dtUltPagamento'));
    }

    public function parametrosAniversariantes() {
        if ($this->request->is('post')) {
            $funcionalidades = new FuncionalidadesComponent();

            /**
             * @FIXME 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["cd_filial"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["regiao_cliente"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_alt_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametro"]["tipo_questionario"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["dt_vigencia"] = $funcionalidades->formatarDataBd($this->request->data["GlbQuestionarioParametro"]["dt_vigencia"]);

            $filiais_ult_compra = ',';
            foreach ($this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] as $value) {
                $filiais_ult_compra .= $value . ",";
            }
//            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = substr($filiais_ult_compra, 1);
            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = $filiais_ult_compra;

            $this->GlbQuestionarioParametro->create();

            if ($this->GlbQuestionarioParametro->save($this->request->data)) {
                if ($this->data['GlbQuestionarioParametro']['qtdCompras'] != NULL) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->inserir($this->data['GlbQuestionarioParametro']['qtdCompras'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
                    $this->GlbQuestionarioParametroVincDataUltCompra->inserir($this->data['GlbQuestionarioParametro']['dtUltCompra'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }

                if (isset($this->data['GlbQuestionarioParametro']['vlrMedio'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
                    $this->GlbQuestionarioParametroVincValorMedioCompra->inserir($this->data['GlbQuestionarioParametro']['vlrMedio'], 1, $this->GlbQuestionarioParametro->getLastInsertID());
                }

                $this->Session->setFlash('O parâmetro foi salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }

        $this->loadModel("GlbQuestionarioParametroFaixaQuantidadeCompra");
        $faixaQuantidade = $this->GlbQuestionarioParametroFaixaQuantidadeCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroFaixaValorMedioCompra");
        $valorMedio = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("GlbQuestionarioParametroDataUltCompra");
        $dataUltCompra = $this->GlbQuestionarioParametroDataUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));

        $this->loadModel("PrcRegiaoFilial");
        $regiaoFilial = $this->PrcRegiaoFilial->find('all', array('order' => array("ds_regiao" => "ASC"),));

        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'conditions' => array('sts_filial = 1')));

        $this->set(compact('valorMedio', 'faixaQuantidade', 'dataUltCompra', 'regiaoFilial', 'filiais'));
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->GlbQuestionarioParametro->recursive = 0;
        $this->set('glbQuestionarioParametro', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioParametro->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta'));
        }
        //Mostrar os dados do parametro ============================================================================================
        $options = array('conditions' => array('cd_parametro_questionario' => $id));
        $parametro = $this->GlbQuestionarioParametro->find('first', $options);
        //Fim dados parametro ======================================================================================================
//        var_dump($parametro);
        //Mostrar a Região cadastrada no parametro =================================================================================
        $this->loadModel("PrcRegiaoFilial");
        $optionsRegiao = array('conditions' => array('cd_regiao' => $parametro["GlbQuestionarioParametro"]["regiao_cliente"]));
        $regiaoFilial = $this->PrcRegiaoFilial->find('first', $optionsRegiao);
        //Fim Região ===============================================================================================================
        //Mostrar as Filiais cadastradas no parametro ==============================================================================
        $this->loadModel("PrcFilial");
        $codFiliais = str_replace(array("(", ")"), "", $parametro["GlbQuestionarioParametro"]["filial_ult_compra"]);
        $codFiliais = explode(",", $codFiliais);
        $filiais = $this->PrcFilial->find('all', array('conditions' => array('cd_filial' => $codFiliais), 'fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'conditions' => array('sts_filial = 1')));
        //Fim Filiais ==============================================================================================================
        //Mostrar quantidade de compras cadastradas no parametro ===================================================================
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $qtdCompras = $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "FaixaQtd.ds_parametro_faixa_quantidade_compra",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_quantidade_compra",
                    "alias" => "FaixaQtd",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincFaixaQuantidadeCompra.cd_parametro_faixa_quantidade_compra = FaixaQtd.cd_parametro_faixa_quantidade_compra")
        ))));
        //Fim quantidade de compras ================================================================================================
        //Mostrar Data da última compra cadastradas no parametro ===================================================================
        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $dtUltCompra = $this->GlbQuestionarioParametroVincDataUltCompra->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "DtUltimaCompra.ds_parametro_data_ult_compra",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_data_ult_compra",
                    "alias" => "DtUltimaCompra",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincDataUltCompra.cd_parametro_data_ult_compra = DtUltimaCompra.cd_parametro_data_ult_compra")
        ))));
        //Fim Data da última compra ================================================================================================
        //Mostrar Valor da última compra cadastradas no parametro ==================================================================
        $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
        $valorUltCompra = $this->GlbQuestionarioParametroVincValorUltCompra->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "ValorUltimaCompra.ds_parametro_faixa_valor_ult_compra",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_valor_ult_compra",
                    "alias" => "ValorUltimaCompra",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincValorUltCompra.cd_parametro_faixa_valor_ult_compra = ValorUltimaCompra.cd_parametro_faixa_valor_ult_compra")
        ))));
        //Fim valor da última de compra ============================================================================================
        //Mostrar Valor médio de compra cadastradas no parametro ===================================================================
        $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
        $valorMedio = $this->GlbQuestionarioParametroVincValorMedioCompra->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "ValorMedio.ds_parametro_faixa_valor_medio_compra",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_valor_medio_compra",
                    "alias" => "ValorMedio",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincValorMedioCompra.cd_parametro_faixa_valor_medio_compra = ValorMedio.cd_parametro_faixa_valor_medio_compra")
        ))));
        //Fim valor médio de compra ================================================================================================
        //Mostrar Média de Atraso ==================================================================================================
        $this->loadModel("GlbQuestionarioParametroVincFaixaMediaAtraso");
        $mediaAtraso = $this->GlbQuestionarioParametroVincFaixaMediaAtraso->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "MediaAtraso.ds_parametro_faixa_media_atraso",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_media_atraso",
                    "alias" => "MediaAtraso",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincFaixaMediaAtraso.cd_parametro_faixa_media_atraso = MediaAtraso.cd_parametro_faixa_media_atraso")
        ))));
        //Fim Média de Atraso ======================================================================================================
        //Mostrar Data de Cadastro =================================================================================================
        $this->loadModel("GlbQuestionarioParametroVincFaixaDataCadastro");
        $dtCadastro = $this->GlbQuestionarioParametroVincFaixaDataCadastro->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "DataCadastro.ds_parametro_faixa_data_cadastro",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_data_cadastro",
                    "alias" => "DataCadastro",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincFaixaDataCadastro.cd_parametro_faixa_data_cadastro = DataCadastro.cd_parametro_faixa_data_cadastro")
        ))));
        //Fim Data de Cadastro =====================================================================================================
        //Mostrar Data de Atualização ==============================================================================================
        $this->loadModel("GlbQuestionarioParametroVincFaixaDataAtualizacao");
        $dtAtualizacao = $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "DataAtualizacao.ds_parametro_faixa_data_atualizacao",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_data_atualizacao",
                    "alias" => "DataAtualizacao",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincFaixaDataAtualizacao.cd_parametro_faixa_data_atualizacao = DataAtualizacao.cd_parametro_faixa_data_atualizacao")
        ))));
        //Fim Data de Atualização ==================================================================================================
        //Mostrar Data de Último Pagamento =========================================================================================
        $this->loadModel("GlbQuestionarioParametroVincFaixaDtUltPagamento");
        $dtUltPagamento = $this->GlbQuestionarioParametroVincFaixaDtUltPagamento->find("all", array('conditions' => array('cd_parametro_questionario' => $parametro["GlbQuestionarioParametro"]["cd_parametro_questionario"]), 'fields' => "UltimoPagamento.ds_parametro_faixa_dt_ult_pagamento",
            "joins" => array(
                array(
                    "table" => "glb_questionario_parametro_faixa_dt_ult_pagamento",
                    "alias" => "UltimoPagamento",
                    "type" => "INNER",
                    "conditions" => array("GlbQuestionarioParametroVincFaixaDtUltPagamento.cd_parametro_faixa_dt_ult_pagamento = UltimoPagamento.cd_parametro_faixa_dt_ult_pagamento")
        ))));
        //Fim Data de Último Pagamento =============================================================================================

        $this->set(compact('parametro', 'regiaoFilial', 'filiais', 'qtdCompras', 'dtUltCompra', 'valorUltCompra', 'valorMedio', 'mediaAtraso', 'dtCadastro', 'dtAtualizacao', 'dtUltPagamento'));
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
            $this->request->data["GlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["cd_filial"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["regiao_cliente"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_alt_cad"] = $this->Session->read('Questionarios.cd_usu');
            $filiais_ult_compra = ',';
            foreach ($this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] as $value) {
                $filiais_ult_compra .= $value . ',';
            }
            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = $filiais_ult_compra;

            $this->GlbQuestionarioParametro->create();
            if ($this->GlbQuestionarioParametro->save($this->request->data)) {
                if ($this->data['GlbQuestionarioParametro']['qtdCompras'] != NULL) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->inserir($this->data['GlbQuestionarioParametro']['qtdCompras'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
                    $this->GlbQuestionarioParametroVincDataUltCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincDataUltCompra->inserir($this->data['GlbQuestionarioParametro']['dtUltCompra'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['vlrUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
                    $this->GlbQuestionarioParametroVincValorUltCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincValorUltCompra->inserir($this->data['GlbQuestionarioParametro']['vlrUltCompra'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['vlrMedio'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
                    $this->GlbQuestionarioParametroVincValorMedioCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincValorMedioCompra->inserir($this->data['GlbQuestionarioParametro']['vlrMedio'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtCadastro'])) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaDataCadastro");
                    $this->GlbQuestionarioParametroVincFaixaDataCadastro->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincFaixaDataCadastro->inserir($this->data['GlbQuestionarioParametro']['dtCadastro'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtAtualizacao'])) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaDataAtualizacao");
                    $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->inserir($this->data['GlbQuestionarioParametro']['dtAtualizacao'], 1, $id);
                }

                $this->Session->setFlash('O parâmetro foi salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }

        /*
         * Buscando as faixas de quantidade
         * Buscando as faixas de quantidade já cadastradas para esse parametro
         */
        $this->loadModel("GlbQuestionarioParametroFaixaQuantidadeCompra");
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $faixaQuantidade = $this->GlbQuestionarioParametroFaixaQuantidadeCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $quantRel = $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_quantidade_compra'));

        /*
         * Buscando as faixas de valor médio
         * Buscando as faixas de valor médio já cadastradas para esse parametro
         */
        $this->loadModel("GlbQuestionarioParametroFaixaValorMedioCompra");
        $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
        $valorMedio = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $valorMedioRel = $this->GlbQuestionarioParametroVincValorMedioCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_valor_medio_compra'));

        $this->loadModel("GlbQuestionarioParametroFaixaValorUltCompra");
        $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
        $valorUltCompra = $this->GlbQuestionarioParametroFaixaValorUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $valorUltCompraRel = $this->GlbQuestionarioParametroVincValorUltCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_valor_ult_compra'));

        $this->loadModel("GlbQuestionarioParametroDataUltCompra");
        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $dataUltCompra = $this->GlbQuestionarioParametroDataUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $dataUltCompraRel = $this->GlbQuestionarioParametroVincDataUltCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_data_ult_compra'));


        $this->loadModel("GlbQuestionarioParametroFaixaDataCadastro");
        $this->loadModel("GlbQuestionarioParametroVincFaixaDataCadastro");
        $dtCadastro = $this->GlbQuestionarioParametroFaixaDataCadastro->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $dtCadastroRel = $this->GlbQuestionarioParametroVincFaixaDataCadastro->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_data_cadastro'));


        $this->loadModel("GlbQuestionarioParametroFaixaDataAtualizacao");
        $this->loadModel("GlbQuestionarioParametroVincFaixaDataAtualizacao");
        $dtAtualizacao = $this->GlbQuestionarioParametroFaixaDataAtualizacao->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $dtAtualizacaoRel = $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_data_atualizacao'));

        $options = array('conditions' => array('GlbQuestionarioParametro.' . $this->GlbQuestionarioParametro->primaryKey => $id));
        $this->request->data = $this->GlbQuestionarioParametro->find('first', $options);

        $this->loadModel("PrcRegiaoFilial");
        $regiaoFilial = $this->PrcRegiaoFilial->find('all', array('order' => array("ds_regiao" => "ASC"),));

        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'conditions' => array('sts_filial = 1')));

        $this->set(compact('valorMedio', 'faixaQuantidade', 'valorUltCompra', 'dataUltCompra', 'regiaoFilial', 'filiais', 'quantRel', 'valorMedioRel', 'valorUltCompraRel', 'dataUltCompraRel', 'dtAtualizacao', 'dtCadastro', 'dtCadastroRel', 'dtAtualizacaoRel'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function editAniversariantes($id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
//            $funcionalidades = new FuncionalidadesComponent();
            /**
             * @FIXME 
             * cd_emp = 1 e cd_filial = 0 ficaram fixos a pedido do Antonio.
             */
            $this->request->data["GlbQuestionarioParametro"]["cd_emp"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["cd_filial"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["regiao_cliente"] = 0;
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
            $this->request->data["GlbQuestionarioParametro"]["cd_usu_alt_cad"] = $this->Session->read('Questionarios.cd_usu');


            $this->request->data["GlbQuestionarioParametro"]["tipo_questionario"] = 1;
            $this->request->data["GlbQuestionarioParametro"]["dt_vigencia"] = $this->Funcionalidades->formatarDataBd($this->request->data["GlbQuestionarioParametro"]["dt_vigencia"]);


            $filiais_ult_compra = ',';
            foreach ($this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] as $value) {
                $filiais_ult_compra .= $value . ',';
            }
            $this->request->data['GlbQuestionarioParametro']['filial_ult_compra'] = $filiais_ult_compra;


            $this->GlbQuestionarioParametro->create();

            if ($this->GlbQuestionarioParametro->save($this->request->data)) {
                if ($this->data['GlbQuestionarioParametro']['qtdCompras'] != NULL) {
                    $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->inserir($this->data['GlbQuestionarioParametro']['qtdCompras'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['dtUltCompra'])) {
                    $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
                    $this->GlbQuestionarioParametroVincDataUltCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincDataUltCompra->inserir($this->data['GlbQuestionarioParametro']['dtUltCompra'], 1, $id);
                }
                if (isset($this->data['GlbQuestionarioParametro']['vlrMedio'])) {
                    $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
                    $this->GlbQuestionarioParametroVincValorMedioCompra->excluir(1, $id);
                    $this->GlbQuestionarioParametroVincValorMedioCompra->inserir($this->data['GlbQuestionarioParametro']['vlrMedio'], 1, $id);
                }

                $this->Session->setFlash('O parâmetro foi salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
//                $this->Session->setFlash('Falha ao salvar parametro. Por favor, tente novamente.', 'erro');
            }
        }

        /*
         * Buscando as faixas de quantidade
         * Buscando as faixas de quantidade já cadastradas para esse parametro
         */
        $this->loadModel("GlbQuestionarioParametroFaixaQuantidadeCompra");
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $faixaQuantidade = $this->GlbQuestionarioParametroFaixaQuantidadeCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $quantRel = $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_quantidade_compra'));

        /*
         * Buscando as faixas de valor médio
         * Buscando as faixas de valor médio já cadastradas para esse parametro
         */
        $this->loadModel("GlbQuestionarioParametroFaixaValorMedioCompra");
        $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
        $valorMedio = $this->GlbQuestionarioParametroFaixaValorMedioCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $valorMedioRel = $this->GlbQuestionarioParametroVincValorMedioCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_valor_medio_compra'));

        $this->loadModel("GlbQuestionarioParametroFaixaValorUltCompra");
        $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
        $valorUltCompra = $this->GlbQuestionarioParametroFaixaValorUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $valorUltCompraRel = $this->GlbQuestionarioParametroVincValorUltCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_faixa_valor_ult_compra'));

        $this->loadModel("GlbQuestionarioParametroDataUltCompra");
        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $dataUltCompra = $this->GlbQuestionarioParametroDataUltCompra->find('all', array('order' => array("valor_inicial" => "ASC"),));
        $dataUltCompraRel = $this->GlbQuestionarioParametroVincDataUltCompra->find('all', array('conditions' => array('cd_parametro_questionario' => $id), 'fields' => 'cd_parametro_data_ult_compra'));

        $options = array('conditions' => array('GlbQuestionarioParametro.' . $this->GlbQuestionarioParametro->primaryKey => $id));
        $this->request->data = $this->GlbQuestionarioParametro->find('first', $options);

        $date = explode(" ", $this->request->data['GlbQuestionarioParametro']['dt_vigencia']);
        $date[0] = implode("/", array_reverse(explode("-", $date[0])));
        $this->request->data['GlbQuestionarioParametro']['dt_vigencia'] = $date[0] . " " . @$date[1];

        $this->loadModel("PrcRegiaoFilial");
        $regiaoFilial = $this->PrcRegiaoFilial->find('all', array('order' => array("ds_regiao" => "ASC"),));

        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'conditions' => array('sts_filial = 1')));

        $this->set(compact('valorMedio', 'faixaQuantidade', 'valorUltCompra', 'dataUltCompra', 'regiaoFilial', 'filiais', 'quantRel', 'valorMedioRel', 'valorUltCompraRel', 'dataUltCompraRel', 'dtAtualizacao', 'dtCadastro', 'dtCadastroRel', 'dtAtualizacaoRel'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($cd_emp = null, $cd_parametro_questionario = null) {
        $this->GlbQuestionarioParametro->id = $cd_parametro_questionario;

        $this->loadModel("GlbQuestionarioGlbQuestionarioParametro");
        $questParametro = $this->GlbQuestionarioGlbQuestionarioParametro->find('first', array('conditions' => array('cd_emp' => $cd_emp, 'cd_parametro_questionario' => $cd_parametro_questionario), 'fields' => 'cd_questionario'));
        if (!empty($questParametro)) {
            $this->Session->setFlash('Parâmetro não pode ser apagado, pois existe relação com um questionário.', 'erro');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel("GlbQuestionarioParametroVincFaixaQuantidadeCompra");
        $this->GlbQuestionarioParametroVincFaixaQuantidadeCompra->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $this->GlbQuestionarioParametroVincDataUltCompra->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincValorUltCompra");
        $this->GlbQuestionarioParametroVincValorUltCompra->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincValorMedioCompra");
        $this->GlbQuestionarioParametroVincValorMedioCompra->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincDataUltCompra");
        $this->GlbQuestionarioParametroVincDataUltCompra->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincFaixaDataAtualizacao");
        $this->GlbQuestionarioParametroVincFaixaDataAtualizacao->excluir($cd_emp, $cd_parametro_questionario);

        $this->loadModel("GlbQuestionarioParametroVincFaixaDataCadastro");
        $this->GlbQuestionarioParametroVincFaixaDataCadastro->excluir($cd_emp, $cd_parametro_questionario);

        if ($this->GlbQuestionarioParametro->delete()) {
            $this->Session->setFlash('Parâmetro apagado com sucesso!', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function listarFiliais() {
        $regioes = substr($this->request->data['regioes'], 1);
        $regioes = explode(',', $regioes);
        $this->loadModel("PrcRegiaoFilial");
        $filiais = $this->PrcRegiaoFilial->find("all", array('conditions' => array('PrcRegiaoFilial.cd_regiao' => $regioes), 'fields' => array('filial.cd_filial', 'filial.nm_fant'), 'order' => 'filial.nm_fant',
            "joins" => array(
                array(
                    "table" => "prc_regiao_filial_prc_filial",
                    "alias" => "regiaoFilial",
                    "type" => "INNER",
                    "conditions" => array("PrcRegiaoFilial.cd_regiao = regiaoFilial.cd_regiao")),
                array(
                    "table" => "prc_filial",
                    "alias" => "filial",
                    "type" => "INNER",
                    "conditions" => array("regiaoFilial.cd_filial = filial.cd_filial")
        ))));

        $this->set(compact('filiais'));
        $this->layout = 'historico';
    }

}
