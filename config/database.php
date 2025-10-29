<?php
/**
 * Configuração do Banco de Dados
 * Sistema SysApp - Versão PHP Puro
 */

class Database {
    private static $instance = null;
    private $conn;
    
    // Configurações padrão do banco
    private $host = 'localhost';
    private $port = '5432';
    private $database = 'sysapp';
    private $username = 'postgres';
    private $password = 'systec';
    
    private function __construct() {
        // Construtor privado para Singleton
    }
    
    /**
     * Conecta ao banco de dados PostgreSQL
     */
    public function connect($host = null, $database = null, $username = null, $password = null, $port = null) {
        try {
            // Usa configurações personalizadas se fornecidas
            $h = $host ?? $this->host;
            $d = $database ?? $this->database;
            $u = $username ?? $this->username;
            $p = $password ?? $this->password;
            $pt = $port ?? $this->port;
            
            error_log("=== TENTANDO CONECTAR AO BANCO ===");
            error_log("Host: $h, Database: $d, User: $u, Port: $pt");
            
            $conn_string = "host=$h port=$pt dbname=$d user=$u password=$p";
            $this->conn = pg_connect($conn_string);
            
            if (!$this->conn) {
                error_log("ERRO: Falha ao conectar ao banco de dados");
                throw new Exception("Erro ao conectar ao banco de dados");
            }
            
            error_log("SUCESSO: Conectado ao banco de dados");
            return $this->conn;
        } catch (Exception $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Retorna instância única do Database (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retorna a conexão ativa
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Executa uma query SQL
     */
    public function query($sql) {
        if (!$this->conn) {
            return false;
        }
        return pg_query($this->conn, $sql);
    }
    
    /**
     * Executa query e retorna todos os resultados
     */
    public function fetchAll($sql) {
        $result = $this->query($sql);
        if (!$result) {
            return [];
        }
        return pg_fetch_all($result);
    }
    
    /**
     * Executa query e retorna primeira linha
     */
    public function fetchOne($sql) {
        $result = $this->query($sql);
        if (!$result) {
            return null;
        }
        return pg_fetch_assoc($result);
    }
    
    /**
     * Escapa string para prevenir SQL Injection
     */
    public function escape($string) {
        if (!$this->conn) {
            return $string;
        }
        return pg_escape_string($this->conn, $string);
    }
    
    /**
     * Fecha a conexão
     */
    public function close() {
        if ($this->conn) {
            pg_close($this->conn);
            $this->conn = null;
        }
    }
}
