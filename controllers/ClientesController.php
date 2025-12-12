<?php
/**
 * Controller de Clientes
 */

require_once BASE_PATH . '/models/Cliente.php';

class ClientesController extends Controller {
    private $Cliente;
    
    public function __construct() {
        parent::__construct();
        $this->Cliente = new Cliente();
    }
    
    /**
     * Lista clientes
     */
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $filtro = $_GET['filtro'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $clientes = $this->Cliente->listar($limit, $offset, $filtro);
        $total = $this->Cliente->count($filtro);
        $totalPages = ceil($total / $limit);
        
        $this->set([
            'clientes' => $clientes,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'filtro' => $filtro
        ]);
        
        $this->render();
    }
    
    /**
     * Visualiza detalhes do cliente
     */
    public function view($cd_pessoa = null) {
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
        
        $telefones = $this->Cliente->getTelefones($cd_pessoa);
        $observacoes = $this->Cliente->getObservacoes($cd_pessoa);
        $historico = $this->Cliente->getHistorico($cd_pessoa);
        
        $this->set([
            'cliente' => $cliente,
            'telefones' => $telefones,
            'observacoes' => $observacoes,
            'historico' => $historico
        ]);
        
        $this->render();
    }
    
    /**
     * Busca clientes para autocomplete (AJAX)
     */
    public function search() {
        $this->layout = false;
        
        if ($this->isAjax()) {
            $term = $_GET['q'] ?? '';
            $clientes = $this->Cliente->search($term);
            
            $result = [];
            foreach ($clientes as $cliente) {
                $result[] = [
                    'id' => $cliente['cd_pessoa'],
                    'text' => $cliente['nm_fant']
                ];
            }
            
            $this->json($result);
        }
        
        exit;
    }
}
