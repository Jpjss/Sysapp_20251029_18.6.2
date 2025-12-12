<?php
/**
 * Model de Relatório
 */

class Relatorio {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Reconecta ao banco da empresa se estiver configurado na sessão
        if (Session::check('Config.database')) {
            $host = Session::read('Config.host');
            $database = Session::read('Config.database');
            $user = Session::read('Config.user');
            $password = Session::read('Config.password');
            $port = Session::read('Config.porta');
            
            $this->db->connect($host, $database, $user, $password, $port);
        }
    }
    
    /**
     * Busca estatísticas gerais
     */
    public function getEstatisticas() {
        $stats = [];
        
        // Detecta tipo de banco (se tem dados de questionários)
        $temQuestionarios = $this->detectarDadosQuestionarios();
        
        // Total de clientes
        $sql = "SELECT COUNT(*) as total FROM glb_pessoa";
        $result = $this->db->fetchOne($sql);
        $stats['total_clientes'] = $result ? (int)$result['total'] : 0;
        
        if ($temQuestionarios) {
            // Banco com sistema de questionários
            $campoData = $this->detectarCampoData();
            
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
                    WHERE DATE($campoData) = CURRENT_DATE";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_hoje'] = $result ? (int)$result['total'] : 0;
            
            // Atendimentos mês
            $sql = "SELECT COUNT(*) as total FROM glb_questionario_resposta 
                    WHERE EXTRACT(MONTH FROM $campoData) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM $campoData) = EXTRACT(YEAR FROM CURRENT_DATE)";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_mes'] = $result ? (int)$result['total'] : 0;
        } else {
            // Banco ERP comercial - mostra dados de vendas baseado em ped_vd
            $stats['total_questionarios'] = 0;
            $stats['total_respostas'] = 0;
            
            // Total de pedidos/vendas (conta registros distintos de ped_vd)
            $sql = "SELECT COUNT(*) as total, SUM(vlr_vd)::NUMERIC(14,2) as valor_total FROM ped_vd";
            $result = $this->db->fetchOne($sql);
            $stats['total_respostas'] = $result ? (int)$result['total'] : 0;
            $stats['valor_total_vendas'] = $result ? (float)$result['valor_total'] : 0;
            
            // Total de pedidos/vendas hoje
            $sql = "SELECT COUNT(*) as total, SUM(vlr_vd)::NUMERIC(14,2) as valor_hoje FROM ped_vd 
                    WHERE DATE(dt_hr_ped) = CURRENT_DATE";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_hoje'] = $result ? (int)$result['total'] : 0;
            $stats['valor_vendas_hoje'] = $result ? (float)$result['valor_hoje'] : 0;
            
            // Total de pedidos/vendas no mês
            $sql = "SELECT COUNT(*) as total, SUM(vlr_vd)::NUMERIC(14,2) as valor_mes FROM ped_vd 
                    WHERE EXTRACT(MONTH FROM dt_hr_ped) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM dt_hr_ped) = EXTRACT(YEAR FROM CURRENT_DATE)";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_mes'] = $result ? (int)$result['total'] : 0;
            $stats['valor_vendas_mes'] = $result ? (float)$result['valor_mes'] : 0;
        }
        
        return $stats;
    }
    
    /**
     * Detecta se o banco tem dados de questionários
     */
    private function detectarDadosQuestionarios() {
        $sql = "SELECT EXISTS (
            SELECT 1 FROM glb_questionario LIMIT 1
        ) as tem_dados";
        
        $result = $this->db->fetchOne($sql);
        return ($result && $result['tem_dados'] === 't');
    }
    
    /**
     * Detecta qual campo de data usar
     */
    private function detectarCampoData() {
        $sql = "SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'glb_questionario_resposta' 
                AND column_name IN ('dt_resposta', 'dt_cad')";
        
        $result = $this->db->fetchOne($sql);
        
        if ($result && $result['column_name'] === 'dt_resposta') {
            return 'dt_resposta';
        }
        
        return 'dt_cad';
    }
    
    /**
     * Busca atendimentos por período
     */
    public function getAtendimentosPorPeriodo($dt_inicio, $dt_fim) {
        $dt_inicio = $this->db->escape($dt_inicio);
        $dt_fim = $this->db->escape($dt_fim);
        
        $temQuestionarios = $this->detectarDadosQuestionarios();
        
        if ($temQuestionarios) {
            $campoData = $this->detectarCampoData();
            
            $sql = "SELECT DATE(qr.$campoData) as data, 
                           COUNT(*) as total,
                           COUNT(DISTINCT qr.cd_pessoa) as clientes_unicos
                    FROM glb_questionario_resposta qr
                    WHERE DATE(qr.$campoData) BETWEEN '$dt_inicio' AND '$dt_fim'
                    GROUP BY DATE(qr.$campoData)
                    ORDER BY DATE(qr.$campoData)";
        } else {
            // Banco ERP - usa dados de vendas de ped_vd
            $sql = "SELECT DATE(pv.dt_hr_ped) as data, 
                           COUNT(*) as total,
                           COUNT(DISTINCT pv.cd_cli) as clientes_unicos
                    FROM ped_vd pv
                    WHERE DATE(pv.dt_hr_ped) BETWEEN '$dt_inicio' AND '$dt_fim'
                    GROUP BY DATE(pv.dt_hr_ped)
                    ORDER BY DATE(pv.dt_hr_ped)";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca top clientes atendidos
     */
    public function getTopClientes($limit = 10) {
        $limit = (int)$limit;
        
        $temQuestionarios = $this->detectarDadosQuestionarios();
        
        // Detecta estrutura da tabela glb_pessoa
        $sql_detect = "SELECT column_name 
                       FROM information_schema.columns 
                       WHERE table_name = 'glb_pessoa' 
                       AND column_name IN ('nm_fant', 'nm_pessoa')";
        $result = $this->db->fetchOne($sql_detect);
        $campoNome = ($result && $result['column_name'] === 'nm_fant') ? 'nm_fant' : 'nm_pessoa';
        
        if ($temQuestionarios) {
            $campoData = $this->detectarCampoData();
            
            $sql = "SELECT p.cd_pessoa, p.$campoNome as nm_fant, COUNT(*) as total_atendimentos,
                           MAX(qr.$campoData) as ultimo_atendimento
                    FROM glb_questionario_resposta qr
                    INNER JOIN glb_pessoa p ON qr.cd_pessoa::text = p.cd_pessoa::text
                    GROUP BY p.cd_pessoa, p.$campoNome
                    ORDER BY total_atendimentos DESC
                    LIMIT $limit";
        } else {
            // Banco ERP - top clientes por compras em ped_vd
            $sql = "SELECT p.cd_pessoa, p.$campoNome as nm_fant, COUNT(*) as total_atendimentos,
                           MAX(pv.dt_hr_ped) as ultimo_atendimento
                    FROM ped_vd pv
                    INNER JOIN glb_pessoa p ON pv.cd_cli = p.cd_pessoa
                    GROUP BY p.cd_pessoa, p.$campoNome
                    ORDER BY total_atendimentos DESC
                    LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Busca atendimentos por usuário
     */
    public function getAtendimentosPorUsuario($dt_inicio = null, $dt_fim = null) {
        $where = '';
        $campoData = $this->detectarCampoData();
        
        if ($dt_inicio && $dt_fim) {
            $dt_inicio = $this->db->escape($dt_inicio);
            $dt_fim = $this->db->escape($dt_fim);
            $where = "WHERE DATE(qr.$campoData) BETWEEN '$dt_inicio' AND '$dt_fim'";
        }
        
        // Detecta campo de usuário
        $sql_detect = "SELECT column_name 
                       FROM information_schema.columns 
                       WHERE table_name = 'glb_questionario_resposta' 
                       AND column_name IN ('cd_usuario', 'cd_usu_cad')";
        $result = $this->db->fetchOne($sql_detect);
        $campoUsuario = ($result && $result['column_name'] === 'cd_usuario') ? 'cd_usuario' : 'cd_usu_cad';
        
        $sql = "SELECT u.nome_usuario, COUNT(*) as total_atendimentos
                FROM glb_questionario_resposta qr
                LEFT JOIN vw_login u ON qr.$campoUsuario = u.cd_usuario
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
    
    /**
     * Busca filiais disponíveis
     */
    public function getFiliais() {
        $sql = "SELECT cd_filial, nm_fant FROM prc_filial WHERE sts_filial = 1 ORDER BY nm_fant";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Relatório de Estoque Detalhado por Família/Grupo
     */
    public function estoque_detalhado($parametros) {
        $dt_referencia = $parametros['dt_referencia'];
        $cd_filial = $parametros['cd_filial'];
        $tipo_agrupamento = $parametros['tipo_agrupamento']; // 'FAMILIA' ou 'GRUPO'
        $ordenacao = $parametros['ordenacao'];
        $exibir_estoque_zerado = $parametros['exibir_estoque_zerado'];

        // Define campo de agrupamento
        $campo_agrupamento = ($tipo_agrupamento == 'FAMILIA') ? 'familia.ds_familia' : 'grupo.ds_grupo';
        $campo_agrupamento_alias = 'ds_categoria';
        $tabela_agrupamento = ($tipo_agrupamento == 'FAMILIA') ? 'est_produto_familia familia' : 'est_produto_grupo grupo';
        $join_campo = ($tipo_agrupamento == 'FAMILIA') ? 'familia.cd_familia = prod.cd_familia' : 'grupo.cd_grupo = prod.cd_grupo';

        $operadorEstoque = $exibir_estoque_zerado ? '>=' : '>';

        $sql = "
        WITH totais_gerais AS (
            SELECT 
                SUM(est.qtde_estoque * est.vlr_custo_gerenc) as total_valor_geral,
                SUM(est.qtde_estoque) as total_qtde_geral
            FROM est_produto_cpl_tamanho_prc_filial_estoque est
            INNER JOIN est_produto_cpl_tamanho tam ON tam.cd_cpl_tamanho = est.cd_cpl_tamanho
            INNER JOIN est_produto prod ON prod.cd_produto = tam.cd_produto
            WHERE est.cd_filial IN ($cd_filial)
            AND est.qtde_estoque $operadorEstoque 0
        )
        SELECT 
            $campo_agrupamento as ds_categoria,
            SUM(est.qtde_estoque * est.vlr_custo_gerenc)::NUMERIC(14,2) as custo_total,
            SUM(est.qtde_estoque)::NUMERIC(14,2) as qtde_total,
            COUNT(DISTINCT prod.cd_produto) as total_skus,
            CASE 
                WHEN totais.total_qtde_geral > 0 
                THEN (SUM(est.qtde_estoque) / totais.total_qtde_geral * 100)::NUMERIC(14,2)
                ELSE 0 
            END as perc_qtde,
            CASE 
                WHEN totais.total_valor_geral > 0 
                THEN (SUM(est.qtde_estoque * est.vlr_custo_gerenc) / totais.total_valor_geral * 100)::NUMERIC(14,2)
                ELSE 0 
            END as perc_valor
        FROM est_produto_cpl_tamanho_prc_filial_estoque est
        INNER JOIN est_produto_cpl_tamanho tam ON tam.cd_cpl_tamanho = est.cd_cpl_tamanho
        INNER JOIN est_produto prod ON prod.cd_produto = tam.cd_produto
        INNER JOIN $tabela_agrupamento ON $join_campo
        CROSS JOIN totais_gerais totais
        WHERE est.cd_filial IN ($cd_filial)
        AND est.qtde_estoque $operadorEstoque 0
        GROUP BY $campo_agrupamento, totais.total_valor_geral, totais.total_qtde_geral";

        // Adiciona ordenação
        switch ($ordenacao) {
            case 'VALOR_ASC':
                $sql .= " ORDER BY custo_total ASC";
                break;
            case 'QTDE_DESC':
                $sql .= " ORDER BY qtde_total DESC";
                break;
            case 'QTDE_ASC':
                $sql .= " ORDER BY qtde_total ASC";
                break;
            case 'NOME':
                $sql .= " ORDER BY ds_categoria ASC";
                break;
            case 'VALOR_DESC':
            default:
                $sql .= " ORDER BY custo_total DESC";
                break;
        }

        return $this->db->fetchAll($sql);
    }
}
