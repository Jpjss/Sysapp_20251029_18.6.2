<?php
/**
 * Gerenciador de Sessão
 */

class Session {
    
    /**
     * Inicia a sessão
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Define um valor na sessão
     */
    public static function write($key, $value) {
        self::start();
        
        // Suporta notação de ponto (ex: 'User.name')
        $keys = explode('.', $key);
        $current = &$_SESSION;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }
    
    /**
     * Lê um valor da sessão
     */
    public static function read($key = null) {
        self::start();
        
        if ($key === null) {
            return $_SESSION;
        }
        
        // Suporta notação de ponto
        $keys = explode('.', $key);
        $current = $_SESSION;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                return null;
            }
            $current = $current[$k];
        }
        
        return $current;
    }
    
    /**
     * Verifica se uma chave existe na sessão
     */
    public static function check($key) {
        return self::read($key) !== null;
    }
    
    /**
     * Remove uma chave da sessão
     */
    public static function delete($key) {
        self::start();
        
        $keys = explode('.', $key);
        $current = &$_SESSION;
        $last = array_pop($keys);
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                return;
            }
            $current = &$current[$k];
        }
        
        unset($current[$last]);
    }
    
    /**
     * Destrói a sessão
     */
    public static function destroy() {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
    
    /**
     * Define uma mensagem flash
     */
    public static function setFlash($message, $type = 'info') {
        self::write('Flash.message', $message);
        self::write('Flash.type', $type);
    }
    
    /**
     * Retorna e remove a mensagem flash
     */
    public static function flash() {
        $message = self::read('Flash.message');
        $type = self::read('Flash.type');
        
        self::delete('Flash.message');
        self::delete('Flash.type');
        
        if ($message) {
            return ['message' => $message, 'type' => $type];
        }
        
        return null;
    }
    
    /**
     * Verifica se a sessão é válida (usuário logado)
     */
    public static function isValid() {
        return self::check('Questionarios.cd_usu');
    }
}
