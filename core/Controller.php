<?php
/**
 * Controller Base
 */

class Controller {
    protected $db;
    protected $layout = 'default';
    protected $viewVars = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
        // REMOVIDO: Não conectar aqui, pois o index.php já conecta
        // $this->db->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        
        // Se houver configuração de empresa na sessão, reconecta automaticamente
        $this->reconectarBancoEmpresaSeNecessario();
    }
    
    /**
     * Reconecta ao banco da empresa se houver configuração na sessão
     */
    protected function reconectarBancoEmpresaSeNecessario() {
        if (Session::check('Config.database')) {
            $host = Session::read('Config.host');
            $database = Session::read('Config.database');
            $user = Session::read('Config.user');
            $password = Session::read('Config.password');
            $port = Session::read('Config.porta');
            
            // Reconecta ao banco da empresa
            $this->db->connect($host, $database, $user, $password, $port);
        }
    }
    
    /**
     * Define variável para a view
     */
    protected function set($key, $value = null) {
        if (is_array($key)) {
            $this->viewVars = array_merge($this->viewVars, $key);
        } else {
            $this->viewVars[$key] = $value;
        }
    }
    
    /**
     * Renderiza uma view
     */
    protected function render($view = null, $layout = null) {
        // Extrai variáveis para a view
        extract($this->viewVars);
        
        // Determina o nome da view
        if ($view === null) {
            $backtrace = debug_backtrace();
            $view = $backtrace[1]['function'];
        }
        
        // Determina o controller
        $controller = strtolower(str_replace('Controller', '', get_class($this)));
        
        // Caminho da view
        $viewFile = BASE_PATH . '/views/' . $controller . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View não encontrada: $viewFile");
        }
        
        // Usa layout?
        $layout = $layout ?? $this->layout;
        
        if ($layout === false) {
            require $viewFile;
        } else {
            // Captura o conteúdo da view
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Renderiza com layout
            $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        }
    }
    
    /**
     * Retorna JSON
     */
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Verifica se é requisição POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica se é requisição AJAX
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Redireciona
     */
    protected function redirect($url) {
        Router::redirect($url);
    }
    
    /**
     * Verifica autenticação
     */
    protected function requireAuth() {
        if (!Session::isValid()) {
            Session::setFlash('Você não está logado.', 'error');
            $this->redirect('usuarios/login');
        }
    }
}
