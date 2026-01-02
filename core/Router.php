<?php
/**
 * Sistema de Rotas Simples
 */

class Router {
    private $controller = 'usuarios';
    private $action = 'login';
    private $params = [];
    
    /**
     * Processa a URL e determina controller/action
     */
    public function dispatch() {
        $url = $this->parseUrl();
        
        file_put_contents(__DIR__ . '/../login_debug.log', "[ROUTER] URL parsed: " . json_encode($url) . "\n", FILE_APPEND);
        
        // Determina o controller
        if (isset($url[0]) && !empty($url[0])) {
            $this->controller = strtolower($url[0]);
            unset($url[0]);
        }
        
        // Determina a action
        if (isset($url[1]) && !empty($url[1])) {
            $this->action = strtolower($url[1]);
            unset($url[1]);
        }
        
        file_put_contents(__DIR__ . '/../login_debug.log', "[ROUTER] Dispatching to: " . $this->controller . "::" . $this->action . "\n", FILE_APPEND);
        
        // Parâmetros restantes
        $this->params = $url ? array_values($url) : [];
        
        // Carrega o controller
        $controllerFile = BASE_PATH . '/controllers/' . ucfirst($this->controller) . 'Controller.php';
        
        if (!file_exists($controllerFile)) {
            $this->error404();
            return;
        }
        
        require_once $controllerFile;
        
        $controllerClass = ucfirst($this->controller) . 'Controller';
        $controller = new $controllerClass();
        
        // Verifica se a action existe
        if (!method_exists($controller, $this->action)) {
            $this->error404();
            return;
        }
        
        // Executa a action
        call_user_func_array([$controller, $this->action], $this->params);
    }
    
    /**
     * Faz parse da URL
     */
    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
    
    /**
     * Exibe erro 404
     */
    private function error404() {
        http_response_code(404);
        require BASE_PATH . '/views/errors/404.php';
        exit;
    }
    
    /**
     * Redireciona para uma URL
     */
    public static function redirect($url) {
        if (strpos($url, 'http') !== 0) {
            // Monta a URL completa
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            // Remove barras extras
            $path = '/' . trim($url, '/');

            // Redirecionamento compatível com servidores sem rewrite (ex: PHP dev server)
            $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';

            // Preferir redirecionamento relativo para manter cookies/sessões no mesmo host
            if (basename($script) === 'index.php') {
                // Usa caminho relativo: /index.php?url=relatorios/empresa
                $url = $script . '?url=' . ltrim($path, '/');
            } else {
                // Fallback para caminho relativo limpo
                $url = $path;
            }
        }
        
        header('Location: ' . $url);
        exit;
    }
}
