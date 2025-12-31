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
    
    /**
     * Busca atendimentos detalhados por dia
     */
    public function getAtendimentosDetalhados($dt_inicio, $dt_fim) {
        // Detecta se é banco de questionários ou ERP comercial
        $temQuestionarios = $this->detectarDadosQuestionarios();
        
        if ($temQuestionarios) {
            $campoData = $this->detectarCampoData();
            
            $sql = "
                SELECT 
                    DATE($campoData) as data,
                    COUNT(*) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    COALESCE(SUM(EXTRACT(EPOCH FROM (dt_fim - dt_inicio))/3600), 0)::NUMERIC(10,2) as tempo_total_horas,
                    COALESCE(SUM(vl_atendimento), 0)::NUMERIC(14,2) as valor_total
                FROM glb_questionario_resposta
                WHERE $campoData >= :dt_inicio::date 
                  AND $campoData <= :dt_fim::date
                GROUP BY DATE($campoData)
                ORDER BY data ASC
            ";
        } else {
            // Banco ERP - usa dados de ped_vd
            $sql = "
                SELECT 
                    DATE(dt_hr_ped) as data,
                    COUNT(*) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    0 as tempo_total_horas,
                    COALESCE(SUM(vlr_vd), 0)::NUMERIC(14,2) as valor_total
                FROM ped_vd
                WHERE DATE(dt_hr_ped) >= :dt_inicio::date 
                  AND DATE(dt_hr_ped) <= :dt_fim::date
                GROUP BY DATE(dt_hr_ped)
                ORDER BY data ASC
            ";
        }
        
        $result = $this->db->fetchAll($sql, [
            ':dt_inicio' => $dt_inicio,
            ':dt_fim' => $dt_fim
        ]);
        
        // Formatar tempo total
        if ($result) {
            foreach ($result as &$row) {
                $horas = floor($row['tempo_total_horas']);
                $minutos = round(($row['tempo_total_horas'] - $horas) * 60);
                $row['tempo_total_formatado'] = sprintf('%02d:%02d', $horas, $minutos);
            }
        }
        
        return $result ?? [];
    }
    
    /**
     * Busca totais de atendimentos no período
     */
    public function getTotaisAtendimentos($dt_inicio, $dt_fim) {
        $temQuestionarios = $this->detectarDadosQuestionarios();
        
        if ($temQuestionarios) {
            $campoData = $this->detectarCampoData();
            
            $sql = "
                SELECT 
                    COUNT(*) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    COALESCE(SUM(EXTRACT(EPOCH FROM (dt_fim - dt_inicio))/3600), 0)::NUMERIC(10,2) as tempo_total_horas,
                    COALESCE(SUM(vl_atendimento), 0)::NUMERIC(14,2) as valor_total
                FROM glb_questionario_resposta
                WHERE $campoData >= :dt_inicio::date 
                  AND $campoData <= :dt_fim::date
            ";
        } else {
            // Banco ERP
            $sql = "
                SELECT 
                    COUNT(*) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    0 as tempo_total_horas,
                    COALESCE(SUM(vlr_vd), 0)::NUMERIC(14,2) as valor_total
                FROM ped_vd
                WHERE DATE(dt_hr_ped) >= :dt_inicio::date 
                  AND DATE(dt_hr_ped) <= :dt_fim::date
            ";
        }
        
        $result = $this->db->fetchOne($sql, [
            ':dt_inicio' => $dt_inicio,
            ':dt_fim' => $dt_fim
        ]);
        
        if ($result) {
            // Formatar tempo total
            $horas = floor($result['tempo_total_horas']);
            $minutos = round(($result['tempo_total_horas'] - $horas) * 60);
            $result['tempo_total_formatado'] = sprintf('%02d:%02d', $horas, $minutos);
            
            // Calcular tempo médio
            if ($result['total_atendimentos'] > 0) {
                $tempo_medio_horas = $result['tempo_total_horas'] / $result['total_atendimentos'];
                $horas_medio = floor($tempo_medio_horas);
                $minutos_medio = round(($tempo_medio_horas - $horas_medio) * 60);
                $result['tempo_medio_formatado'] = sprintf('%02d:%02d', $horas_medio, $minutos_medio);
            } else {
                $result['tempo_medio_formatado'] = '00:00';
            }
            
            // Calcular ticket médio
            if ($result['total_atendimentos'] > 0) {
                $result['ticket_medio'] = $result['valor_total'] / $result['total_atendimentos'];
            } else {
                $result['ticket_medio'] = 0;
            }
        }
        
        return $result ?? [
            'total_atendimentos' => 0,
            'clientes_unicos' => 0,
            'tempo_total_horas' => 0,
            'tempo_total_formatado' => '00:00',
            'tempo_medio_formatado' => '00:00',
            'valor_total' => 0,
            'ticket_medio' => 0
        ];
    }
    
    /**
     * Relatório Entrada x Vendas
     * Retorna dados comparativos entre entradas de estoque e vendas por marca e filial
     */
    public function getEntradaVendas($filtros) {
        // Extrai filtros
        $vendaDtInicio = $filtros['venda_dt_inicio'];
        $vendaDtFim = $filtros['venda_dt_fim'];
        $entradaDtInicio = $filtros['entrada_dt_inicio'];
        $entradaDtFim = $filtros['entrada_dt_fim'];
        $filiais = $filtros['filiais'] ?? ['todas'];
        $estPositivo = $filtros['est_positivo'] ?? false;
        $estZerado = $filtros['est_zerado'] ?? false;
        $estNegativo = $filtros['est_negativo'] ?? false;
        
        // Se nenhum filtro de estoque foi marcado, considera todos
        if (!$estPositivo && !$estZerado && !$estNegativo) {
            $estPositivo = true;
            $estZerado = true;
            $estNegativo = true;
        }
        
        // Monta condição de filiais
        $condicaoFiliais = '';
        if (!in_array('todas', $filiais) && !empty($filiais)) {
            $filiaisPlaceholder = implode(',', array_map(function($f) { return (int)$f; }, $filiais));
            $condicaoFiliais = "AND f.cd_filial IN ($filiaisPlaceholder)";
        }
        
        // Monta condição de estoque
        $condicoesEstoque = [];
        if ($estPositivo) $condicoesEstoque[] = 'est_atual.qtde_atual > 0';
        if ($estZerado) $condicoesEstoque[] = 'est_atual.qtde_atual = 0';
        if ($estNegativo) $condicoesEstoque[] = 'est_atual.qtde_atual < 0';
        
        $condicaoEstoque = '';
        if (!empty($condicoesEstoque)) {
            $condicaoEstoque = 'AND (' . implode(' OR ', $condicoesEstoque) . ')';
        }
        
        // Query principal
        $sql = "
            WITH estoque_atual AS (
                SELECT 
                    p.cd_produto,
                    p.cd_marca,
                    p.cd_filial,
                    COALESCE(SUM(me.qt_pd), 0) as qtde_atual,
                    COALESCE(AVG(p.vl_custo), 0) as preco_custo_medio,
                    COALESCE(AVG(p.vl_vd), 0) as preco_venda_medio
                FROM glb_produto p
                LEFT JOIN mov_estoque me ON me.cd_produto = p.cd_produto
                GROUP BY p.cd_produto, p.cd_marca, p.cd_filial
            ),
            entradas AS (
                SELECT 
                    me.cd_produto,
                    p.cd_marca,
                    p.cd_filial,
                    COALESCE(SUM(me.qt_pd), 0) as qtde_entradas
                FROM mov_estoque me
                INNER JOIN glb_produto p ON p.cd_produto = me.cd_produto
                WHERE me.tp_mov = 'E'
                  AND DATE(me.dt_hr_mov) >= :entrada_dt_inicio::date
                  AND DATE(me.dt_hr_mov) <= :entrada_dt_fim::date
                GROUP BY me.cd_produto, p.cd_marca, p.cd_filial
            ),
            vendas AS (
                SELECT 
                    pv.cd_produto,
                    p.cd_marca,
                    p.cd_filial,
                    COALESCE(SUM(pv.qt_produto), 0) as qtde_vendida,
                    COALESCE(SUM(pv.vl_total), 0) as valor_vendido
                FROM ped_vd_produto pv
                INNER JOIN ped_vd v ON v.cd_ped_vd = pv.cd_ped_vd
                INNER JOIN glb_produto p ON p.cd_produto = pv.cd_produto
                WHERE DATE(v.dt_hr_ped) >= :venda_dt_inicio::date
                  AND DATE(v.dt_hr_ped) <= :venda_dt_fim::date
                GROUP BY pv.cd_produto, p.cd_marca, p.cd_filial
            )
            SELECT 
                f.nm_filial,
                f.cd_filial,
                m.nm_marca,
                m.cd_marca,
                COALESCE(est_atual.qtde_atual, 0) as estoque_atual,
                COALESCE(ent.qtde_entradas, 0) as qtde_entradas,
                COALESCE(vd.qtde_vendida, 0) as qtde_vendida,
                COALESCE(est_atual.qtde_atual * est_atual.preco_custo_medio, 0) as valor_estoque,
                COALESCE(vd.valor_vendido, 0) as valor_vendido,
                COALESCE(est_atual.preco_custo_medio, 0) as preco_custo,
                COALESCE(est_atual.preco_venda_medio, 0) as preco_venda
            FROM glb_filial f
            CROSS JOIN glb_marca m
            LEFT JOIN estoque_atual est_atual ON est_atual.cd_marca = m.cd_marca AND est_atual.cd_filial = f.cd_filial
            LEFT JOIN entradas ent ON ent.cd_marca = m.cd_marca AND ent.cd_filial = f.cd_filial
            LEFT JOIN vendas vd ON vd.cd_marca = m.cd_marca AND vd.cd_filial = f.cd_filial
            WHERE 1=1
              $condicaoFiliais
              $condicaoEstoque
              AND (
                  COALESCE(est_atual.qtde_atual, 0) != 0 
                  OR COALESCE(ent.qtde_entradas, 0) != 0 
                  OR COALESCE(vd.qtde_vendida, 0) != 0
              )
            ORDER BY f.nm_filial, m.nm_marca
        ";
        
        $params = [
            ':venda_dt_inicio' => $vendaDtInicio,
            ':venda_dt_fim' => $vendaDtFim,
            ':entrada_dt_inicio' => $entradaDtInicio,
            ':entrada_dt_fim' => $entradaDtFim
        ];
        
        $resultados = $this->db->fetchAll($sql, $params);
        
        // Organiza dados por filial
        $dados = [];
        $totaisGerais = [
            'estoque_atual' => 0,
            'qtde_entradas' => 0,
            'qtde_vendida' => 0,
            'valor_estoque' => 0,
            'valor_vendido' => 0
        ];
        
        foreach ($resultados as $row) {
            $filial = $row['nm_filial'];
            
            if (!isset($dados[$filial])) {
                $dados[$filial] = [
                    'itens' => [],
                    'subtotal' => [
                        'estoque_atual' => 0,
                        'qtde_entradas' => 0,
                        'qtde_vendida' => 0,
                        'valor_estoque' => 0,
                        'valor_vendido' => 0
                    ]
                ];
            }
            
            // Calcula relações e margem
            $relEstoqueValor = $row['valor_vendido'] > 0 ? $row['valor_estoque'] / $row['valor_vendido'] : 0;
            $relEstoqueQtde = $row['qtde_vendida'] > 0 ? $row['estoque_atual'] / $row['qtde_vendida'] : 0;
            
            // Margem = ((Preço Venda - Preço Custo) / Preço Venda) * 100
            $margem = 0;
            if ($row['preco_venda'] > 0) {
                $margem = (($row['preco_venda'] - $row['preco_custo']) / $row['preco_venda']) * 100;
            }
            
            $item = [
                'marca' => $row['nm_marca'],
                'estoque_atual' => (int)$row['estoque_atual'],
                'qtde_entradas' => (int)$row['qtde_entradas'],
                'qtde_vendida' => (int)$row['qtde_vendida'],
                'valor_estoque' => (float)$row['valor_estoque'],
                'valor_vendido' => (float)$row['valor_vendido'],
                'rel_estoque_valor' => $relEstoqueValor,
                'rel_estoque_qtde' => $relEstoqueQtde,
                'preco_custo' => (float)$row['preco_custo'],
                'preco_venda' => (float)$row['preco_venda'],
                'margem' => $margem,
                'perc_qtde_estoque' => 0, // Será calculado depois
                'perc_qtde_venda' => 0,
                'perc_valor_estoque' => 0,
                'perc_valor_venda' => 0
            ];
            
            $dados[$filial]['itens'][] = $item;
            
            // Acumula subtotais da filial
            $dados[$filial]['subtotal']['estoque_atual'] += $item['estoque_atual'];
            $dados[$filial]['subtotal']['qtde_entradas'] += $item['qtde_entradas'];
            $dados[$filial]['subtotal']['qtde_vendida'] += $item['qtde_vendida'];
            $dados[$filial]['subtotal']['valor_estoque'] += $item['valor_estoque'];
            $dados[$filial]['subtotal']['valor_vendido'] += $item['valor_vendido'];
            
            // Acumula totais gerais
            $totaisGerais['estoque_atual'] += $item['estoque_atual'];
            $totaisGerais['qtde_entradas'] += $item['qtde_entradas'];
            $totaisGerais['qtde_vendida'] += $item['qtde_vendida'];
            $totaisGerais['valor_estoque'] += $item['valor_estoque'];
            $totaisGerais['valor_vendido'] += $item['valor_vendido'];
        }
        
        // Calcula percentuais
        foreach ($dados as $filial => &$filialData) {
            $subtotal = $filialData['subtotal'];
            
            // Calcula margem média do subtotal
            if ($subtotal['valor_vendido'] > 0) {
                $custoTotal = $subtotal['valor_vendido'] - ($subtotal['valor_estoque'] > 0 ? $subtotal['valor_estoque'] : 0);
                $filialData['subtotal']['margem'] = (($subtotal['valor_vendido'] - $custoTotal) / $subtotal['valor_vendido']) * 100;
            } else {
                $filialData['subtotal']['margem'] = 0;
            }
            
            foreach ($filialData['itens'] as &$item) {
                // Percentuais em relação ao subtotal da filial
                $item['perc_qtde_estoque'] = $subtotal['estoque_atual'] > 0 
                    ? ($item['estoque_atual'] / $subtotal['estoque_atual']) * 100 
                    : 0;
                
                $item['perc_qtde_venda'] = $subtotal['qtde_vendida'] > 0 
                    ? ($item['qtde_vendida'] / $subtotal['qtde_vendida']) * 100 
                    : 0;
                
                $item['perc_valor_estoque'] = $subtotal['valor_estoque'] > 0 
                    ? ($item['valor_estoque'] / $subtotal['valor_estoque']) * 100 
                    : 0;
                
                $item['perc_valor_venda'] = $subtotal['valor_vendido'] > 0 
                    ? ($item['valor_vendido'] / $subtotal['valor_vendido']) * 100 
                    : 0;
            }
        }
        
        // Calcula margem geral
        if ($totaisGerais['valor_vendido'] > 0) {
            $custoTotalGeral = $totaisGerais['valor_vendido'] - ($totaisGerais['valor_estoque'] > 0 ? $totaisGerais['valor_estoque'] : 0);
            $totaisGerais['margem'] = (($totaisGerais['valor_vendido'] - $custoTotalGeral) / $totaisGerais['valor_vendido']) * 100;
        } else {
            $totaisGerais['margem'] = 0;
        }
        
        return [
            'dados' => $dados,
            'totais' => $totaisGerais
        ];
    }
}

