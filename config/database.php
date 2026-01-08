<?php
/**
 * Configuração do Banco de Dados
 * Sistema SysApp - Versão PHP Puro
 */

class Database {
    private static $instance = null;
    private $conn;
    
    // Configurações padrão do banco
    private $host = 'banco.propasso.systec.ftp.sh';
    private $port = '5432';
    private $database = 'bd_propasso';
    private $username = 'admin';
    private $password = 'systec2011.';
    
    private function __construct() {
        // Construtor privado para Singleton
        // NÃO conecta aqui - será conectado explicitamente no index.php
        // $this->connect(); // REMOVIDO
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
            
            $conn_string = "host=$h port=$pt dbname=$d user=$u password=$p";
            
            // DEBUG: Log da conexão
            file_put_contents(__DIR__ . '/../login_debug.log', "[DB] Conectando em: $h | DB: $d\n", FILE_APPEND);
            
            // Suprime warning para não quebrar output
            $this->conn = @pg_connect($conn_string);
            
            if (!$this->conn) {
                error_log("ERRO: Falha ao conectar ao banco de dados: $h/$d");
                throw new Exception("Erro ao conectar ao banco de dados");
            }
            
            // Atualiza as propriedades da instância após conexão bem-sucedida
            $this->host = $h;
            $this->database = $d;
            $this->username = $u;
            $this->password = $p;
            $this->port = $pt;
            
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
     * Retorna o nome do banco de dados atualmente conectado
     */
    public function getDatabase() {
        return $this->database;
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
     * Suporta parâmetros nomeados (:param) ou posicionais ($1)
     */
    public function fetchAll($sql, $params = []) {
        if (empty($params)) {
            // Sem parâmetros - query simples
            $result = $this->query($sql);
            if (!$result) {
                return [];
            }
            return pg_fetch_all($result) ?: [];
        }
        
        // Com parâmetros - usar pg_query_params
        // Converter parâmetros nomeados (:nome) para posicionais ($1, $2...)
        $paramValues = [];
        $paramIndex = 1;
        
        foreach ($params as $key => $value) {
            $placeholder = ltrim($key, ':');
            $sql = str_replace(':' . $placeholder, '$' . $paramIndex, $sql);
            $paramValues[] = $value;
            $paramIndex++;
        }
        
        $result = pg_query_params($this->conn, $sql, $paramValues);
        if (!$result) {
            error_log("Database::fetchAll ERROR: " . pg_last_error($this->conn));
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($paramValues, true));
            return [];
        }
        
        return pg_fetch_all($result) ?: [];
    }
    
    /**
     * Executa query e retorna primeira linha
     * Suporta parâmetros nomeados (:param) ou posicionais ($1)
     */
    public function fetchOne($sql, $params = []) {
        if (empty($params)) {
            // Sem parâmetros - query simples
            $result = $this->query($sql);
            if (!$result) {
                return null;
            }
            return pg_fetch_assoc($result);
        }
        
        // Com parâmetros - usar pg_query_params
        $paramValues = [];
        $paramIndex = 1;
        
        foreach ($params as $key => $value) {
            $placeholder = ltrim($key, ':');
            $sql = str_replace(':' . $placeholder, '$' . $paramIndex, $sql);
            $paramValues[] = $value;
            $paramIndex++;
        }
        
        $result = pg_query_params($this->conn, $sql, $paramValues);
        if (!$result) {
            error_log("Database::fetchOne ERROR: " . pg_last_error($this->conn));
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($paramValues, true));
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
