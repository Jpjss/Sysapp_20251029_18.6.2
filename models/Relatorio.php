<?php
/**
 * Model de Relatório
 */

class Relatorio {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca estatísticas gerais
     */
    public function getEstatisticas() {
        $stats = [];
        
        // Total de clientes
        $sql = "SELECT COUNT(*) as total FROM glb_pessoa";
        $result = $this->db->fetchOne($sql);
        $stats['total_clientes'] = $result ? (int)$result['total'] : 0;
        
        // Total de questionários
        $sql = "SELECT COUNT(*) as total FROM glb_questionario";
        $result = $this->db->fetchOne($sql);
        $stats['total_questionarios'] = $result ? (int)$result['total'] : 0;
        
        // Total de respostas
        $sql = "SELECT COUNT(*) as total FROM glb_questionario_resposta";
        $result = $this->db->fetchOne($sql);
        $stats['total_respostas'] = $result ? (int)$result['total'] : 0;
        
        // Atendimentos hoje
        $sql = "SELECT COUNT(*) as total FROM glb_questionario_resposta 
                WHERE DATE(dt_resposta) = CURRENT_DATE";
        $result = $this->db->fetchOne($sql);
        $stats['atendimentos_hoje'] = $result ? (int)$result['total'] : 0;
        
        // Atendimentos mês
        $sql = "SELECT COUNT(*) as total FROM glb_questionario_resposta 
                WHERE EXTRACT(MONTH FROM dt_resposta) = EXTRACT(MONTH FROM CURRENT_DATE)
                AND EXTRACT(YEAR FROM dt_resposta) = EXTRACT(YEAR FROM CURRENT_DATE)";
        $result = $this->db->fetchOne($sql);
        $stats['atendimentos_mes'] = $result ? (int)$result['total'] : 0;
        
        return $stats;
    }
    
    /**
     * Busca atendimentos por período
     */
    public function getAtendimentosPorPeriodo($dt_inicio, $dt_fim) {
        $dt_inicio = $this->db->escape($dt_inicio);
        $dt_fim = $this->db->escape($dt_fim);
        
        $sql = "SELECT DATE(qr.dt_resposta) as data, 
                       COUNT(*) as total,
                       COUNT(DISTINCT qr.cd_pessoa) as clientes_unicos
                FROM glb_questionario_resposta qr
                WHERE DATE(qr.dt_resposta) BETWEEN '$dt_inicio' AND '$dt_fim'
                GROUP BY DATE(qr.dt_resposta)
                ORDER BY DATE(qr.dt_resposta)";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca top clientes atendidos
     */
    public function getTopClientes($limit = 10) {
        $limit = (int)$limit;
        
        $sql = "SELECT p.cd_pessoa, p.nm_fant, COUNT(*) as total_atendimentos,
                       MAX(qr.dt_resposta) as ultimo_atendimento
                FROM glb_questionario_resposta qr
                INNER JOIN glb_pessoa p ON qr.cd_pessoa = p.cd_pessoa
                GROUP BY p.cd_pessoa, p.nm_fant
                ORDER BY total_atendimentos DESC
                LIMIT $limit";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca atendimentos por usuário
     */
    public function getAtendimentosPorUsuario($dt_inicio = null, $dt_fim = null) {
        $where = '';
        
        if ($dt_inicio && $dt_fim) {
            $dt_inicio = $this->db->escape($dt_inicio);
            $dt_fim = $this->db->escape($dt_fim);
            $where = "WHERE DATE(qr.dt_resposta) BETWEEN '$dt_inicio' AND '$dt_fim'";
        }
        
        $sql = "SELECT u.nome_usuario, COUNT(*) as total_atendimentos
                FROM glb_questionario_resposta qr
                LEFT JOIN vw_login u ON qr.cd_usuario = u.cd_usuario
                $where
                GROUP BY u.nome_usuario
                ORDER BY total_atendimentos DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca relatório simplificado
     */
    public function getRelatorioSimplificado($filtros = []) {
        $where = [];
        
        if (!empty($filtros['dt_inicio'])) {
            $dt_inicio = $this->db->escape($filtros['dt_inicio']);
            $where[] = "DATE(dt_resposta) >= '$dt_inicio'";
        }
        
        if (!empty($filtros['dt_fim'])) {
            $dt_fim = $this->db->escape($filtros['dt_fim']);
            $where[] = "DATE(dt_resposta) <= '$dt_fim'";
        }
        
        if (!empty($filtros['cd_usuario'])) {
            $cd_usuario = (int)$filtros['cd_usuario'];
            $where[] = "cd_usuario = $cd_usuario";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM vw_relatorio_simplificado $whereClause ORDER BY dt_resposta DESC LIMIT 100";
        
        return $this->db->fetchAll($sql);
    }
}
