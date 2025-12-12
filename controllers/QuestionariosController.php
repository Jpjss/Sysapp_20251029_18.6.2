<?php
/**
 * Controller de Questionários
 */

require_once BASE_PATH . '/models/Questionario.php';
require_once BASE_PATH . '/models/Cliente.php';

class QuestionariosController extends Controller {
    private $Questionario;
    private $Cliente;
    
    public function __construct() {
        parent::__construct();
        $this->Questionario = new Questionario();
        $this->Cliente = new Cliente();
    }
    
    /**
     * Lista questionários
     */
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $questionarios = $this->Questionario->listar($limit, $offset);
        $total = $this->Questionario->count();
        $totalPages = ceil($total / $limit);
        
        $this->set([
            'questionarios' => $questionarios,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
        
        $this->render();
    }
    
    /**
     * Responder questionário
     */
    public function responder($cd_questionario = null, $cd_pessoa = null) {
        $this->requireAuth();
        
        if (!$cd_questionario) {
            Session::setFlash('Questionário não encontrado!', 'error');
            $this->redirect('questionarios/index');
        }
        
        $questionario = $this->Questionario->findById($cd_questionario);
        
        if (!$questionario) {
            Session::setFlash('Questionário não encontrado!', 'error');
            $this->redirect('questionarios/index');
        }
        
        $cliente = null;
        if ($cd_pessoa) {
            $cliente = $this->Cliente->findById($cd_pessoa);
        }
        
        if ($this->isPost()) {
            $cd_pessoa = (int)$_POST['cd_pessoa'];
            $cd_usuario = Session::read('Questionarios.cd_usu');
            
            // Salva respostas
            foreach ($_POST['respostas'] as $cd_pergunta => $resposta) {
                if (!empty($resposta)) {
                    $dados = [
                        'cd_questionario' => $cd_questionario,
                        'cd_pessoa' => $cd_pessoa,
                        'cd_pergunta' => $cd_pergunta,
                        'resposta' => $resposta,
                        'cd_usuario' => $cd_usuario
                    ];
                    
                    $this->Questionario->salvarResposta($dados);
                }
            }
            
            Session::setFlash('Questionário respondido com sucesso!', 'success');
            $this->redirect('questionarios/proximosAtendimentos');
        }
        
        $perguntas = $this->Questionario->getPerguntas($cd_questionario);
        
        $this->set([
            'questionario' => $questionario,
            'perguntas' => $perguntas,
            'cliente' => $cliente
        ]);
        
        $this->render();
    }
    
    /**
     * Próximos atendimentos
     */
    public function proximosAtendimentos() {
        $this->requireAuth();
        
        $atendimentos = $this->Questionario->getProximosAtendimentos(50);
        
        $this->set([
            'atendimentos' => $atendimentos
        ]);
        
        $this->render();
    }
    
    /**
     * Aniversariantes
     */
    public function aniversariantes() {
        $this->requireAuth();
        
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : null;
        $aniversariantes = $this->Questionario->getAniversariantes($mes);
        
        $this->set([
            'aniversariantes' => $aniversariantes,
            'mes' => $mes ?? date('m')
        ]);
        
        $this->render();
    }
    
    /**
     * Histórico de atendimentos do cliente
     */
    public function historico($cd_pessoa = null) {
        $this->requireAuth();
        
        if (!$cd_pessoa) {
            Session::setFlash('Cliente não encontrado!', 'error');
            $this->redirect('clientes/index');
        }
        
        $cliente = $this->Cliente->findById($cd_pessoa);
        
        if (!$cliente) {
            Session::setFlash('Cliente não encontrado!', 'error');
            $this->redirect('clientes/index');
        }
        
        $historico = $this->Questionario->getHistorico($cd_pessoa);
        
        $this->set([
            'cliente' => $cliente,
            'historico' => $historico
        ]);
        
        $this->render();
    }
}
