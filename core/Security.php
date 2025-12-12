<?php
/**
 * Funções de Segurança
 */

class Security {
    
    /**
     * Gera hash MD5 com salt (compatível com CakePHP)
     */
    public static function hash($string, $type = 'md5', $salt = null) {
        $salt = $salt ?? SECURITY_SALT;
        
        if ($type === 'md5') {
            // Formato CakePHP: salt + string + salt
            return md5($salt . $string . $salt);
        }
        
        return hash($type, $salt . $string . $salt);
    }
    
    /**
     * Criptografa texto (algoritmo original do sistema)
     */
    public static function encrypt($texto) {
        $G = 0;
        $salasana = 0;
        $Encrypted = '';
        
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            
            $X1 = 0;
            if ($G == 0) $X1 = $sana - ($salasana - 2);
            if ($G == 1) $X1 = $sana + ($salasana - 5);
            if ($G == 2) $X1 = $sana - ($salasana - 4);
            if ($G == 3) $X1 = $sana + ($salasana - 2);
            if ($G == 4) $X1 = $sana - ($salasana - 3);
            if ($G == 5) $X1 = $sana + ($salasana - 5);
            
            $X1 = $X1 + $G;
            $Encrypted = $Encrypted . chr($X1);
        }
        
        return $Encrypted;
    }
    
    /**
     * Descriptografa texto (algoritmo original do sistema)
     */
    public static function decrypt($texto) {
        $G = 0;
        $salasana = 0;
        $Decrypted = '';
        
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            
            $X1 = 0;
            if ($G == 0) $X1 = $sana + ($salasana - 2);
            if ($G == 1) $X1 = $sana - ($salasana - 5);
            if ($G == 2) $X1 = $sana + ($salasana - 4);
            if ($G == 3) $X1 = $sana - ($salasana - 2);
            if ($G == 4) $X1 = $sana + ($salasana - 3);
            if ($G == 5) $X1 = $sana - ($salasana - 5);
            
            $X1 = $X1 - $G;
            $Decrypted = $Decrypted . chr($X1);
        }
        
        return $Decrypted;
    }
    
    /**
     * Sanitiza entrada de dados
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
}
