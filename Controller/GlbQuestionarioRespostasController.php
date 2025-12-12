<?php
App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioRespostas Controller
 *
 * @property GlbQuestionarioResposta $GlbQuestionarioResposta
 */
class GlbQuestionarioRespostasController extends AppController {

    public $helpers = array('Funcionalidades');
    
    public function beforeFilter() {
        if (!in_array('atendimento', $this->Session->read('Questionarios.permissoes'))) {
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
    public function index($tipo = null) {
        $this->GlbQuestionarioResposta->recursive = 0;
        if ($tipo != null) {
            $options = array('conditions' => array('tipo_questionario' => $tipo), 'order' => array('cd_resposta' => 'DESC'));
        } else {
            $options = array('order' => array('cd_resposta' => 'DESC'));
        }
        $this->paginate = $options;
        $this->set('glbQuestionarioRespostas', $this->paginate());
    }

    public function atender() {
        $this->loadModel("VwQuestionario");
        $data_atual = date("Y-m-d H:i:s");
        $options = array(
            'fields' => array('VwQuestionario.questionario', 'dt_vigencia_ini', 'dt_vigencia_fim', 'observacao_questionario', 'cd_questionario', 'tipo_questionario'),
            'conditions' => array('VwQuestionario.dt_vigencia_ini <= ' => $data_atual, 'VwQuestionario.dt_vigencia_fim >= ' => $data_atual),
            'order' => array('VwQuestionario.dt_vigencia_fim' => 'ASC'),
            'group' => array('VwQuestionario.questionario', 'dt_vigencia_ini', 'dt_vigencia_fim', 'observacao_questionario', 'cd_questionario', 'tipo_questionario')
        );
        $this->paginate = $options;
        // Roda a consulta, já trazendo os resultados paginados
        $questionarios = $this->paginate('VwQuestionario');

        // Envia os dados pra view
        $this->set('glbQuestionarios', $questionarios);
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->GlbQuestionarioResposta->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta'));
        }

        $options = array('conditions' => array('GlbQuestionarioResposta.' . $this->GlbQuestionarioResposta->primaryKey => $id));
        $glbQuestionarioResposta = $this->GlbQuestionarioResposta->find('first', $options);

        $this->loadModel("glbPessoa");
        $cliente = $this->glbPessoa->find('first', array('conditions' => array('cd_pessoa' => $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_pessoa'])));

        $this->loadModel("glbQuestionarioRespostaHistorico");
        $questionarioHistorico = $this->glbQuestionarioRespostaHistorico->find('all', array('conditions' => array('cd_resposta' => $id)));

        $this->loadModel("glbQuestionario");
        $questionario = $this->glbQuestionario->find('first', array('conditions' => array('cd_questionario' => $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_questionario'])));

        $this->loadModel("Usuario");
        $usuario = $this->Usuario->find('first', array('conditions' => array('cd_usu' => $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_usu_cad'])));

        $this->loadModel("glbQuestionarioRespostaCpl");
        $respostas = $this->glbQuestionarioRespostaCpl->find("all", array('conditions' => array('cd_resposta' => $glbQuestionarioResposta['GlbQuestionarioResposta']['cd_resposta']), 'fields' => "qPergunta.ds_pergunta, qPergunta.cd_pergunta, qPergunta.tp_pergunta, glbQuestionarioRespostaCpl.ds_resposta, glbQuestionarioRespostaCpl.cd_pergunta_cpl",
            "joins" => array(
                array(
                    "table" => "glb_questionario_pergunta",
                    "alias" => "qPergunta",
                    "type" => "INNER",
                    "conditions" => array("glbQuestionarioRespostaCpl.cd_pergunta = qPergunta.cd_pergunta")
        ))));

        foreach ($respostas as $value) {
            if ($value['qPergunta']['tp_pergunta'] == 2) {
                $pPontuacao[] = $value['qPergunta']['cd_pergunta'];
            }
        }

        $pPontuacao[] = 24;
        $this->loadModel("glbQuestionarioPerguntaCpl");
        $glbQuestionarioPerguntaCpl = $this->glbQuestionarioPerguntaCpl->find("all", array('conditions' => array('cd_pergunta' => $pPontuacao), 'fields' => array("glbQuestionarioPerguntaCpl.cd_pergunta", "COUNT(glbQuestionarioPerguntaCpl.ds_pergunta_cpl) as ds_pergunta_cpl"), 'group' => array('cd_pergunta')));
        foreach ($glbQuestionarioPerguntaCpl as $value) {
            $pergCpl[$value['glbQuestionarioPerguntaCpl']['cd_pergunta']] = $value[0]['ds_pergunta_cpl'];
        }
        $this->set(compact('cliente', 'glbQuestionarioResposta', 'usuario', 'questionario', 'respostas', 'pergCpl', 'questionarioHistorico'));
    }

    /**
     * atendimento method
     * 
     * @return void
     */
    public function atendimento($id = null, $tipoAtendimento = null) {
        ini_set('max_execution_time', 0);
        if ($this->request->is('post')) {
            $dados["cd_resposta"] = $this->request->data["GlbQuestionarioResposta"]["cd_resposta"];
            $dados["cd_usu_cad"] = $this->request->data["GlbQuestionarioResposta"]["cd_usu_cad"];
            $dados["dt_cad"] = $this->request->data["GlbQuestionarioResposta"]["dt_cad"];
            $dados["hora_inicio"] = $this->request->data["GlbQuestionarioResposta"]["hora_inicio"];
            $dados["protocolo"] = $this->request->data["GlbQuestionarioResposta"]["protocolo"];
            $this->set(compact('dados'));
            date_default_timezone_set("Brazil/East");
            $this->request->data["GlbQuestionarioResposta"]["hora_fim"] = date("H:i:s");
            $finalizar = $this->request->data["GlbQuestionarioResposta"]['status_finalizar'];
            unset($this->request->data["GlbQuestionarioResposta"]['status_finalizar']);
            if ($this->GlbQuestionarioResposta->save($this->request->data)) {
                /*
                 * Se o status do atendimento for igual a 1 (sem contato) nãos salva nenhuma resposta.
                 */
                if ($this->request->data["GlbQuestionarioResposta"]["status_atendimento"] != 1) {

                    /*
                     * Após salvar o atendimento
                     * Salvar as respostas na tabela
                     */
                    $this->loadModel("GlbQuestionarioRespostaCpl");

                    $i = 1;
                    $idResposta = $this->request->data["GlbQuestionarioResposta"]["cd_resposta"];
                    $dados = "";
                    /*
                     * Tirando as últimas duas posições do array, pois não pertecem à tabela GlbQuestionarioRespostaCpl
                     */
                    array_pop($this->request->data["GlbQuestionarioResposta"]);
                    array_pop($this->request->data["GlbQuestionarioResposta"]);
                    /*
                     * Gerando o próximo ID da sequencia
                     */
                    $ultId = $this->GlbQuestionarioRespostaCpl->novoId();

                    foreach ($this->request->data["GlbQuestionarioResposta"] as $key => $valor) {
                        /*
                         *  Se i > 8, 8 é a posição que acaba as variaveis que não precisamos.
                         *  a partir da posição 8 começam as variáveis das respostas que serão gravadas na tabela glb_questionario_resposta_cpl
                         */
                        if ($i > 8) {
                            $valor = explode("_", $valor);
                            $chaves = explode("_", $key);
                            switch (@$chaves[2]) {
                                case (0):
                                    $dados[] = array("cd_resposta" => intval($idResposta), "cd_pergunta" => intval($chaves[1]), "ds_resposta" => $valor[1], "cd_pergunta_cpl" => $valor[0]);
                                    break;
                                case (1):
                                    $dados[] = array("cd_resposta" => intval($idResposta), "cd_pergunta" => intval($chaves[1]), "ds_resposta" => $valor[0], "cd_pergunta_cpl" => $chaves[3]);
                                    break;
                                case (2):
                                    $dados[] = array("cd_resposta" => intval($idResposta), "cd_pergunta" => intval($chaves[1]), "cd_pergunta_cpl" => "$valor");
                                    break;
                                case(3):
                                    $dados[] = array("cd_resposta" => intval($idResposta), "cd_pergunta" => intval($chaves[1]), "ds_resposta" => $valor[0], "cd_pergunta_cpl" => $chaves[3]);
                                    break;
                            }
                            /*
                             * Salva todas as respostas
                             */
                            $this->GlbQuestionarioRespostaCpl->inserir($dados, (int) $ultId);
                            $ultId++;
                            unset($dados);
                        }
                        $i++;
                    }

                    $idResposta = null;
                }
                if ($finalizar == 0) {
                    $this->Session->setFlash('Atendimento salvo com sucesso!', 'sucesso');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->redirect(array('action' => 'add', $this->request->data["GlbQuestionarioResposta"]["cd_questionario"], $this->request->data["GlbQuestionarioResposta"]["tipo_atendimento"]));
                }
            } else {
                $this->Session->setFlash('Erro ao salvar atendimento. Por favor, tente novamente.', 'erro');
            }
        }
        $atendimento = $this->GlbQuestionarioResposta->find('first', array('conditions' => array('cd_resposta' => $id),));
        $dados["cd_usu_cad"] = $atendimento['GlbQuestionarioResposta']['cd_usu_cad'];
        $dados['tipo_atendimento'] = $tipoAtendimento;
        $dados["dt_cad"] = $atendimento['GlbQuestionarioResposta']['dt_cad'];
        $dados["hora_inicio"] = $atendimento['GlbQuestionarioResposta'] ['hora_inicio'];
        $dados['cd_resposta'] = $atendimento['GlbQuestionarioResposta']['cd_resposta'];
        $dados['cd_questionario'] = $atendimento['GlbQuestionarioResposta']['cd_questionario'];
        $dados["protocolo"] = $atendimento['GlbQuestionarioResposta']['cd_resposta'] . "/" . $atendimento['GlbQuestionarioResposta']['cd_usu_cad'];


        $this->loadModel("VwQuestionario");
        $perguntas = $this->VwQuestionario->find('all', array('order' => array("prioridades_perguntas" => "ASC"), 'conditions' => array('cd_questionario' => $atendimento['GlbQuestionarioResposta']['cd_questionario']), 'fields' => array('pergunta', 'tipo_pergunta', 'cd_pergunta', 'cd_pergunta_cpl', 'opcoes_pergunta', 'prioridade_respostas', 'prioridades_perguntas'),));

        $this->loadModel("VwQuestionarioCliente");
        $cliente = $this->VwQuestionarioCliente->find('first', array('conditions' => array('cd_pessoa' => str_pad($atendimento['GlbQuestionarioResposta']['cd_pessoa'], 6, "0", STR_PAD_LEFT))));

        $this->loadModel("VwQuestionarioCompras");
        $contratos = $this->VwQuestionarioCompras->find('all', array('conditions' => array('cd_pessoa' => $atendimento['GlbQuestionarioResposta']['cd_pessoa'])));

        $this->loadModel("GlbQuestionario");
        $questionario = $this->GlbQuestionario->find('all', array('conditions' => array('cd_questionario' => $atendimento['GlbQuestionarioResposta']['cd_questionario'])));

        $this->loadModel("GlbPessoaFone");
        $telefones = $this->GlbPessoaFone->find('all', array('conditions' => array('cd_pessoa' => $atendimento['GlbQuestionarioResposta']['cd_pessoa']), 'fields' => array('fone', 'tp_fone')));

        $this->loadModel("GlbPessoaCtrVd");
        $parcelas = $this->GlbPessoaCtrVd->find('all', array('conditions' => array('cd_pessoa' => $atendimento['GlbQuestionarioResposta']['cd_pessoa'])));

        $this->loadModel("glbQuestionarioRespostaHistorico");
        $historico = $this->glbQuestionarioRespostaHistorico->find('all', array('conditions' => array('cd_pessoa' => $atendimento['GlbQuestionarioResposta']['cd_pessoa'])));

        if ($tipoAtendimento == 1) {
            $this->loadModel("VwQuestionarioProxAtendimentoAniversarianteAtendido");
            $atendidos = $this->VwQuestionarioProxAtendimentoAniversarianteAtendido->find('all', array('conditions' => array('cd_questionario' => $atendimento['GlbQuestionarioResposta']['cd_questionario'])));
        } else {
            $this->loadModel("VwQuestionarioProxAtendimentoQtdeAtendimento");
            $atendidos = $this->VwQuestionarioProxAtendimentoQtdeAtendimento->find('all', array('conditions' => array('cd_questionario' => $atendimento['GlbQuestionarioResposta']['cd_questionario'])));
        }
		
        $this->set(compact('cliente', 'perguntas', 'questionario', 'telefones', 'dados', 'viewAtendimento', 'parcelas', 'historico', 'contratos', 'atendidos'));
    }

    /**
     * add method - Método responsável por dar inicio ao atendimento.
     *
     * @return void
     */
    public function add($id = null, $tipoAtendimento = null) {
        ini_set('max_execution_time', 0);
        /*
         * Verificando qual o tipo do atendimento a ser realizado.
         * 1 = Aniversariantes
         * 2 = Inativo
         * 3 = Prospecção
         * 4 = Satisfação/Pós-Venda
         * 5 = VIP
         */
        $this->GlbQuestionarioResposta->atendimento_begin();
        if ($tipoAtendimento == 1) {
            $this->loadModel("VwQuestionarioProxAtendimentoAniversariante");
            $cliente = $this->VwQuestionarioProxAtendimentoAniversariante->find('all', array('conditions' => array('cd_questionario' => $id), 'limit' => 1));

            $cdPessoa = $cliente[0]["VwQuestionarioProxAtendimentoAniversariante"]["cd_pessoa"];
        } else {
            $this->loadModel("VwQuestionarioProxAtendimento");
            $cliente = $this->VwQuestionarioProxAtendimento->find('all', array('conditions' => array('cd_questionario' => $id), 'limit' => 1));
            $cdPessoa = $cliente[0]["VwQuestionarioProxAtendimento"]["cd_pessoa"];
	        }

        /*
         * Gerando o atendimento
         * 
         * Verifica se a view VwQuestionarioProxAtendimento retornou algum cliente.
         * Caso não exista cliente, o usuário é redirecionado.
         * 
         */

        if ($cdPessoa == NULL) {
            $this->Session->setFlash('Não existem clientes para essa pesquisa!', 'alerta');
            $this->redirect(array('action' => 'atender'));
        }
        /**
         * @TODO
         * @var int cd_emp fixado em 1 a pedido do Antônio
         */
        $this->request->data["cd_emp"] = 1;
        $this->request->data["tipo_questionario"] = $tipoAtendimento;
        $this->request->data["cd_questionario"] = $id;
        $this->request->data["cd_pessoa"] = $cdPessoa;
        $this->request->data["cd_usu_cad"] = $this->Session->read('Questionarios.cd_usu');
        $this->request->data["dt_cad"] = date("Y-m-d");
        $this->request->data["hora_inicio"] = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));
        $this->GlbQuestionarioResposta->save($this->request->data);

//        sleep(45);

        $this->GlbQuestionarioResposta->atendimento_commit();
        
        
        // ==========================================================================================================
        $this->redirect(array('action' => 'atendimento', $this->GlbQuestionarioResposta->getLastInsertID(), $tipoAtendimento));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->GlbQuestionarioResposta->exists($id)) {
            throw new NotFoundException(__('Invalid glb questionario resposta'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->GlbQuestionarioResposta->save($this->request->data)) {
                $this->Session->setFlash('Atendimento salvo com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Erro ao salvar atendimento. Por favor, tente novamente.', 'erro');
            }
        } else {
            $options = array('conditions' => array('GlbQuestionarioResposta.' . $this->GlbQuestionarioResposta->primaryKey => $id));
            $this->request->data = $this->GlbQuestionarioResposta->find('first', $options);
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
        $this->GlbQuestionarioResposta->id = $id;
        if (!$this->GlbQuestionarioResposta->exists()) {
            throw new NotFoundException(__('Invalid glb questionario resposta'));
        }
        $this->request->onlyAllow('post', 'delete');

        $this->loadModel("GlbQuestionarioRespostaCpl");
        $this->GlbQuestionarioRespostaCpl->deleteAll(array('cd_resposta' => $id));

        $this->loadModel("GlbQuestionarioRespostaHistorico");
        $this->GlbQuestionarioRespostaHistorico->deleteAll(array('cd_resposta' => $id));

        if ($this->GlbQuestionarioResposta->delete()) {
            $this->Session->setFlash('Atendimento apagado com sucesso', 'sucesso');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Erro ao apagar atendimento', 'erro');
        $this->redirect(array('action' => 'index'));
    }

}
