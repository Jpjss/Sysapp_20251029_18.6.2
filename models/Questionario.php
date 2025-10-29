<?php
/**
 * Model de Questionário
 */

class Questionario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lista questionários
     */
    public function listar($limit = 20, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT * FROM glb_questionario 
                ORDER BY ds_questionario 
                LIMIT $limit OFFSET $offset";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Conta total de questionários
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM glb_questionario";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Busca questionário por ID
     */
    public function findById($cd_questionario) {
        $cd_questionario = (int)$cd_questionario;
        
        $sql = "SELECT * FROM glb_questionario WHERE cd_questionario = $cd_questionario";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Busca perguntas do questionário
     */
    public function getPerguntas($cd_questionario) {
        $cd_questionario = (int)$cd_questionario;
        
        $sql = "SELECT gqp.*, gp.ds_pergunta 
                FROM glb_questionario_glb_questionario_pergunta gqp
                INNER JOIN glb_questionario_pergunta gp ON gqp.cd_pergunta = gp.cd_pergunta
                WHERE gqp.cd_questionario = $cd_questionario
                ORDER BY gqp.ordem";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca respostas de um cliente para um questionário
     */
    public function getRespostas($cd_questionario, $cd_pessoa) {
        $cd_questionario = (int)$cd_questionario;
        $cd_pessoa = (int)$cd_pessoa;
        
        $sql = "SELECT * FROM glb_questionario_resposta 
                WHERE cd_questionario = $cd_questionario 
                AND cd_pessoa = $cd_pessoa
                ORDER BY dt_resposta DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca clientes para atendimento (próximos atendimentos)
     */
    public function getProximosAtendimentos($limit = 50) {
        $limit = (int)$limit;
        
        $sql = "SELECT * FROM vw_questionario_prox_atendimento 
                ORDER BY dt_prox_atendimento 
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca aniversariantes
     */
    public function getAniversariantes($mes = null) {
        if ($mes === null) {
            $mes = date('m');
        }
        $mes = (int)$mes;
        
        $sql = "SELECT * FROM vw_questionario_prox_atendimento_aniversariante 
                WHERE EXTRACT(MONTH FROM dt_nasc) = $mes
                ORDER BY EXTRACT(DAY FROM dt_nasc)";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Salva resposta do questionário
     */
    public function salvarResposta($dados) {
        $cd_questionario = (int)$dados['cd_questionario'];
        $cd_pessoa = (int)$dados['cd_pessoa'];
        $cd_pergunta = (int)$dados['cd_pergunta'];
        $resposta = $this->db->escape($dados['resposta']);
        $cd_usuario = (int)$dados['cd_usuario'];
        
        $sql = "INSERT INTO glb_questionario_resposta 
                (cd_questionario, cd_pessoa, cd_pergunta, ds_resposta, cd_usuario, dt_resposta) 
                VALUES ($cd_questionario, $cd_pessoa, $cd_pergunta, '$resposta', $cd_usuario, NOW())";
        
        return $this->db->query($sql);
    }
    
    /**
     * Busca histórico de respostas
     */
    public function getHistorico($cd_pessoa, $limit = 10) {
        $cd_pessoa = (int)$cd_pessoa;
        $limit = (int)$limit;
        
        $sql = "SELECT qrh.*, q.ds_questionario, u.nome_usuario
                FROM glb_questionario_resposta_historico qrh
                INNER JOIN glb_questionario q ON qrh.cd_questionario = q.cd_questionario
                LEFT JOIN vw_login u ON qrh.cd_usuario = u.cd_usuario
                WHERE qrh.cd_pessoa = $cd_pessoa
                ORDER BY qrh.dt_resposta DESC
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
}
