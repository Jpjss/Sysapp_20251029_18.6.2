<?php

App::uses('AppController', 'Controller');

/**
 * GlbQuestionarioRespostaCpls Controller
 *
 * @property GlbQuestionarioRespostaCpl $GlbQuestionarioRespostaCpl
 */
class GlbQuestionarioRespostaHistoricosController extends AppController {

    public $helpers = array("Formatacao");

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
        $this->GlbQuestionarioRespostaHistorico->recursive = 0;
        $this->set('GlbQuestionarioRespostaHistoricos', $this->paginate());
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->GlbQuestionarioRespostaHistorico->create();

            date_default_timezone_set('America/Sao_Paulo');

            // Formata a diferença de horas para
            // aparecer no formato 00:00:00 na página
            $hora1 = explode(":", $this->request->data["hr_inicio"]);
            $hora2 = explode(":", $this->request->data["hr_final"]);
            $acumulador1 = ($hora1[0] * 3600) + ($hora1[1] * 60) + $hora1[2];
            $acumulador2 = ($hora2[0] * 3600) + ($hora2[1] * 60) + $hora2[2];
            $resultado = $acumulador2 - $acumulador1;
            $hora_ponto = floor($resultado / 3600);
            $resultado = $resultado - ($hora_ponto * 3600);
            $min_ponto = floor($resultado / 60);
            $resultado = $resultado - ($min_ponto * 60);
            $secs_ponto = $resultado;
			
            print_r($this->request->data);
            
            $this->request->data["tmp_ligacao"] = $hora_ponto . ":" . $min_ponto . ":" . $secs_ponto;
            /**
             * @TODO
             * @var int cd_emp fixado em 1 a pedido do Antônio
             */
            $this->request->data["cd_emp"] = 1;
            
            if ($this->GlbQuestionarioRespostaHistorico->save($this->request->data)) {
                $options = array('conditions' => array('GlbQuestionarioRespostaHistorico.cd_pessoa' => $this->request->data["cd_pessoa"]));
                $glbQuestionarioRespostaHistorico = $this->GlbQuestionarioRespostaHistorico->find('all', $options);
                $this->set(compact('glbQuestionarioRespostaHistorico'));
                
                $this->loadModel("GlbPessoaObsContato");

                $dados['cd_pessoa']   = $this->request->data["cd_pessoa"];
                $dados['cd_emp']      = $this->request->data["cd_emp"];
                $dados['dt_obs']      = date("Y-m-d H:i:s");
                $dados['obs_contato'] = $this->request->data["ds_historico"];
                $dados['cd_usu']      = $this->request->data["cd_usu"];
                $dados['tp_contato']  = 0;
                
                $this->GlbPessoaObsContato->gravar_contato($dados);

                $this->layout = 'historico';
                $this->render('historico');
            } else {
                $this->Session->setFlash(__('Erro ao salvar. Por Favor, tente novamente.'));
            }
        }
    }

}
