<?php
/**
 * Model de Relatório - CORRIGIDO COM TABELAS REAIS
 */

class Relatorio {
    private $db;
    private $structureDetector;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Inicializar detector de estrutura
        require_once __DIR__ . '/../helpers/DatabaseStructureDetector.php';
        $this->structureDetector = new DatabaseStructureDetector($this->db);

        // Log inicial da criação da instância
        @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct ENTER\n", FILE_APPEND);

        // Reconecta ao banco da empresa se estiver configurado na sessão
        if (Session::check('Config.database')) {
            $host = Session::read('Config.host');
            $database = Session::read('Config.database');
            $user = Session::read('Config.user');
            $password = Session::read('Config.password');
            $port = Session::read('Config.porta');

            try {
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct connecting to $host/$database\n", FILE_APPEND);
                $this->db->connect($host, $database, $user, $password, $port);
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct connected\n", FILE_APPEND);
                $this->connected = true;
            } catch (Throwable $e) {
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct EXCEPTION: " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
                error_log("Relatorio::__construct EXCEPTION: " . $e->getMessage());
                $this->connected = false;
            }
        } else {
            // SE NÃO HOUVER SESSÃO, CONECTAR COM CONFIGURAÇÃO PADRÃO DO database.php
            try {
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct connecting with DEFAULT config\n", FILE_APPEND);
                $this->db->connect(); // Usa configuração padrão da classe Database
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct connected with DEFAULT\n", FILE_APPEND);
                $this->connected = true;
            } catch (Throwable $e) {
                @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] Relatorio::__construct DEFAULT EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
                error_log("Relatorio::__construct DEFAULT EXCEPTION: " . $e->getMessage());
                $this->connected = false;
            }
        }
    }
    
    /**
     * Busca estatísticas gerais - CORRIGIDO CONFORME CONSULTAS_RELATORIOS.md
     */
    public function getEstatisticas() {
        $stats = [];
        
        // Total de clientes - Query #1 do CONSULTAS_RELATORIOS.md
        try {
            $sql = "SELECT COUNT(DISTINCT cd_pessoa) as total 
                    FROM dm_orcamento_vendas_consolidadas";
            $result = $this->db->fetchOne($sql);
            $stats['total_clientes'] = $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Erro ao buscar total_clientes: " . $e->getMessage());
            $stats['total_clientes'] = 0;
        }
        
        // Sistema ERP comercial - usa dados de vendas
        $stats['total_questionarios'] = 0;
        
        // Total de vendas - Query #1 do CONSULTAS_RELATORIOS.md
        try {
            $sql = "SELECT COUNT(DISTINCT cd_pedido) as total, 
                           COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total 
                    FROM dm_orcamento_vendas_consolidadas";
            $result = $this->db->fetchOne($sql);
            $stats['total_respostas'] = $result ? (int)$result['total'] : 0;
            $stats['valor_total_vendas'] = $result ? (float)$result['valor_total'] : 0;
        } catch (Exception $e) {
            error_log("Erro ao buscar total_respostas: " . $e->getMessage());
            $stats['total_respostas'] = 0;
            $stats['valor_total_vendas'] = 0;
        }
        
        // Vendas hoje - Query #1 do CONSULTAS_RELATORIOS.md
        try {
            $sql = "SELECT COUNT(DISTINCT cd_pedido) as total, 
                           COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_hoje 
                    FROM dm_orcamento_vendas_consolidadas 
                    WHERE dt_emi_pedido >= CURRENT_DATE 
                    AND dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_hoje'] = $result ? (int)$result['total'] : 0;
            $stats['valor_vendas_hoje'] = $result ? (float)$result['valor_hoje'] : 0;
        } catch (Exception $e) {
            error_log("Erro ao buscar atendimentos_hoje: " . $e->getMessage());
            $stats['atendimentos_hoje'] = 0;
            $stats['valor_vendas_hoje'] = 0;
        }
        
        // Vendas no mês - Query #1 do CONSULTAS_RELATORIOS.md
        try {
            $sql = "SELECT COUNT(DISTINCT cd_pedido) as total, 
                           COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_mes 
                    FROM dm_orcamento_vendas_consolidadas 
                    WHERE EXTRACT(MONTH FROM dt_emi_pedido) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM dt_emi_pedido) = EXTRACT(YEAR FROM CURRENT_DATE)";
            $result = $this->db->fetchOne($sql);
            $stats['atendimentos_mes'] = $result ? (int)$result['total'] : 0;
            $stats['valor_vendas_mes'] = $result ? (float)$result['valor_mes'] : 0;
        } catch (Exception $e) {
            error_log("Erro ao buscar atendimentos_mes: " . $e->getMessage());
            $stats['atendimentos_mes'] = 0;
            $stats['valor_vendas_mes'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Busca vendas por período - Query #2 do CONSULTAS_RELATORIOS.md
     */
    public function getAtendimentosPorPeriodo($dt_inicio, $dt_fim) {
        try {
            $sql = "SELECT DATE(dt_emi_pedido) as data, 
                           COUNT(DISTINCT cd_pedido) as total,
                           COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                           COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                    FROM dm_orcamento_vendas_consolidadas
                    WHERE DATE(dt_emi_pedido) BETWEEN :dt_inicio AND :dt_fim
                    GROUP BY DATE(dt_emi_pedido)
                    ORDER BY DATE(dt_emi_pedido)";
            
            return $this->db->fetchAll($sql, [
                ':dt_inicio' => $dt_inicio,
                ':dt_fim' => $dt_fim
            ]) ?: [];
        } catch (Exception $e) {
            error_log("Erro ao buscar atendimentos por período: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca top clientes por vendas - Query #3 do CONSULTAS_RELATORIOS.md
     */
    public function getTopClientes($limit = 10) {
        $limit = (int)$limit;
        
        try {
            $sql = "SELECT v.cd_pessoa,
                           v.nm_cliente as nm_fant,
                           COUNT(DISTINCT v.cd_pedido) as total_atendimentos,
                           MAX(v.dt_emi_pedido) as ultimo_atendimento,
                           COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                    FROM dm_orcamento_vendas_consolidadas v
                    GROUP BY v.cd_pessoa, v.nm_cliente
                    ORDER BY valor_total DESC
                    LIMIT :limite";
            
            return $this->db->fetchAll($sql, [':limite' => $limit]) ?: [];
        } catch (Exception $e) {
            error_log("Erro ao buscar top clientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca filiais disponíveis
     */
    public function getFiliais() {
        $sql = "SELECT cd_filial, 
                       COALESCE(nm_fant, nm_filial, 'Filial ' || cd_filial) as nm_fant, 
                       COALESCE(nm_fant, nm_filial, 'Filial ' || cd_filial) as nm_filial
                FROM prc_filial 
                WHERE sts_filial = 1 
                ORDER BY nm_fant";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Relatório de Estoque Detalhado por Família/Grupo
     */
    public function estoque_detalhado($parametros) {
        $dt_referencia = $parametros['dt_referencia'];
        $cd_filial = $parametros['cd_filial'];
        $tipo_agrupamento = $parametros['tipo_agrupamento'];
        $ordenacao = $parametros['ordenacao'];
        $exibir_estoque_zerado = $parametros['exibir_estoque_zerado'];

        $campo_agrupamento = ($tipo_agrupamento == 'FAMILIA') ? 'familia.ds_familia' : 'grupo.ds_grupo';
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
            COALESCE(SUM(est.qtde_estoque * est.vlr_custo_gerenc), 0)::NUMERIC(14,2) as custo_total,
            COALESCE(SUM(est.qtde_estoque), 0)::NUMERIC(14,2) as qtde_total,
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
     * Busca vendas detalhadas por dia - Query #7 do CONSULTAS_RELATORIOS.md
     */
    public function getAtendimentosDetalhados($dt_inicio, $dt_fim) {
        try {
            $sql = "
                SELECT 
                    DATE(dt_emi_pedido) as data,
                    COUNT(DISTINCT cd_pedido) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                FROM dm_orcamento_vendas_consolidadas
                WHERE DATE(dt_emi_pedido) >= :dt_inicio::date 
                  AND DATE(dt_emi_pedido) <= :dt_fim::date
                GROUP BY DATE(dt_emi_pedido)
                ORDER BY data ASC
            ";
            
            $result = $this->db->fetchAll($sql, [
                ':dt_inicio' => $dt_inicio,
                ':dt_fim' => $dt_fim
            ]);
            
            if ($result) {
                foreach ($result as &$row) {
                    $row['tempo_total_horas'] = 0;
                    $row['tempo_total_formatado'] = '00:00';
                }
            }
            
            return $result ?? [];
        } catch (Exception $e) {
            error_log("Erro ao buscar atendimentos detalhados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca totais de vendas no período - Query #8 do CONSULTAS_RELATORIOS.md
     */
    public function getTotaisAtendimentos($dt_inicio, $dt_fim) {
        try {
            $sql = "
                SELECT 
                    COUNT(DISTINCT cd_pedido) as total_atendimentos,
                    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
                    COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
                FROM dm_orcamento_vendas_consolidadas
                WHERE DATE(dt_emi_pedido) >= :dt_inicio::date 
                  AND DATE(dt_emi_pedido) <= :dt_fim::date
            ";
            
            $result = $this->db->fetchOne($sql, [
                ':dt_inicio' => $dt_inicio,
                ':dt_fim' => $dt_fim
            ]);
            
            if ($result) {
                $result['tempo_total_horas'] = 0;
                $result['tempo_total_formatado'] = '00:00';
                $result['tempo_medio_formatado'] = '00:00';
                
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
        } catch (Exception $e) {
            error_log("Erro ao buscar totais de atendimentos: " . $e->getMessage());
            return [
                'total_atendimentos' => 0,
                'clientes_unicos' => 0,
                'tempo_total_horas' => 0,
                'tempo_total_formatado' => '00:00',
                'tempo_medio_formatado' => '00:00',
                'valor_total' => 0,
                'ticket_medio' => 0
            ];
        }
    }
    
    /**
     * Relatório Entrada x Vendas - ADAPTATIVO
     * Detecta automaticamente a estrutura do banco e usa a query apropriada
     */
    public function getEntradaVendas($filtros) {
        // Detectar estrutura do banco
        $structure = $this->structureDetector->detectStructure();
        
        // Usar query conforme estrutura disponível
        if ($structure['version'] === 'NEW') {
            return $this->getEntradaVendasNew($filtros);
        } elseif ($structure['version'] === 'OLD') {
            return $this->getEntradaVendasOld($filtros);
        } else {
            throw new Exception('Estrutura de banco de dados não reconhecida');
        }
    }
    
    /**
     * Query para estrutura NOVA (dm_*)
     */
    private function getEntradaVendasNew($filtros) {
        $vendaDtInicio = $filtros['venda_dt_inicio'];
        $vendaDtFim = $filtros['venda_dt_fim'];
        $filiais = $filtros['filiais'] ?? ['todas'];
        
        // Monta condição de filiais
        $condicaoFiliais = '';
        if (!in_array('todas', $filiais) && !empty($filiais)) {
            $filiaisPlaceholder = implode(',', array_map(function($f) { return (int)$f; }, $filiais));
            $condicaoFiliais = "AND f.cd_filial IN ($filiaisPlaceholder)";
        }
        
        // Query para estrutura nova
        $sql = "
            WITH vendas_periodo AS (
                SELECT 
                    p.cd_marca,
                    p.ds_marca,
                    v.cd_filial,
                    COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida,
                    COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0) as valor_vendido,
                    COALESCE(AVG(v.vl_tot_it / NULLIF(v.qtde_produto, 0)), 0) as preco_medio
                FROM dm_orcamento_vendas_consolidadas v
                INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
                WHERE DATE(v.dt_emi_pedido) >= :venda_dt_inicio::date
                  AND DATE(v.dt_emi_pedido) <= :venda_dt_fim::date
                  AND p.cd_marca IS NOT NULL
                  AND p.ds_marca IS NOT NULL
                GROUP BY p.cd_marca, p.ds_marca, v.cd_filial
            ),
            estoque_atual AS (
                SELECT 
                    p.cd_marca,
                    est.cd_filial,
                    COALESCE(SUM(est.qtde_estoque), 0) as qtde_atual,
                    COALESCE(AVG(est.vlr_custo), 0) as preco_custo_medio
                FROM dm_estoque_atual est
                INNER JOIN dm_produto p ON p.cd_cpl_tamanho = est.cd_cpl_tamanho
                WHERE p.cd_marca IS NOT NULL
                  AND est.ativo = 1
                GROUP BY p.cd_marca, est.cd_filial
            ),
            filiais_ativas AS (
                SELECT cd_filial, 
                       COALESCE(nm_fant, rz_filial, 'Filial ' || cd_filial) as nm_filial
                FROM prc_filial
                WHERE sts_filial = 1
                $condicaoFiliais
            )
            SELECT 
                f.nm_filial,
                f.cd_filial,
                vd.ds_marca as nm_marca,
                vd.cd_marca,
                COALESCE(est.qtde_atual, 0) as estoque_atual,
                0 as qtde_entradas,
                vd.qtde_vendida,
                COALESCE(est.qtde_atual * est.preco_custo_medio, 0)::NUMERIC(14,2) as valor_estoque,
                vd.valor_vendido::NUMERIC(14,2) as valor_vendido,
                COALESCE(est.preco_custo_medio, 0)::NUMERIC(14,2) as preco_custo,
                vd.preco_medio::NUMERIC(14,2) as preco_venda
            FROM vendas_periodo vd
            INNER JOIN filiais_ativas f ON f.cd_filial = vd.cd_filial
            LEFT JOIN estoque_atual est ON est.cd_marca = vd.cd_marca AND est.cd_filial = vd.cd_filial
            ORDER BY f.nm_filial, vd.ds_marca, vd.qtde_vendida DESC
            LIMIT 1000
        ";
        
        $params = [
            ':venda_dt_inicio' => $vendaDtInicio,
            ':venda_dt_fim' => $vendaDtFim
        ];
        
        return $this->executeEntradaVendas($sql, $params);
    }
    
    /**
     * Query para estrutura ANTIGA (ped_vd, est_produto)
     */
    private function getEntradaVendasOld($filtros) {
        $vendaDtInicio = $filtros['venda_dt_inicio'];
        $vendaDtFim = $filtros['venda_dt_fim'];
        $filiais = $filtros['filiais'] ?? ['todas'];
        
        // Monta condição de filiais
        $condicaoFiliais = '';
        if (!in_array('todas', $filiais) && !empty($filiais)) {
            $filiaisPlaceholder = implode(',', array_map(function($f) { return (int)$f; }, $filiais));
            $condicaoFiliais = "AND f.cd_filial IN ($filiaisPlaceholder)";
        }
        
        // Query para estrutura antiga
        $sql = "
            WITH vendas_periodo AS (
                SELECT 
                    p.cd_marca,
                    m.ds_marca,
                    pv.cd_filial,
                    COALESCE(SUM(pvt.qtde_produto), 0) as qtde_vendida,
                    COALESCE(SUM(pvt.vlr_vd), 0) as valor_vendido,
                    COALESCE(AVG(pvt.vlr_vd / NULLIF(pvt.qtde_produto, 0)), 0) as preco_medio
                FROM ped_vd pv
                INNER JOIN ped_vd_produto_cpl_tamanho pvt ON pvt.cd_ped = pv.cd_ped AND pvt.cd_filial = pv.cd_filial
                INNER JOIN est_produto p ON p.cd_produto = pvt.cd_produto
                INNER JOIN est_produto_marca m ON m.cd_marca = p.cd_marca
                WHERE DATE(pv.dt_hr_ped) >= :venda_dt_inicio::date
                  AND DATE(pv.dt_hr_ped) <= :venda_dt_fim::date
                  AND pv.sts_ped = 1
                  AND p.cd_marca IS NOT NULL
                GROUP BY p.cd_marca, m.ds_marca, pv.cd_filial
            ),
            filiais_ativas AS (
                SELECT cd_filial, 
                       COALESCE(nm_fant, rz_filial, 'Filial ' || cd_filial) as nm_filial
                FROM prc_filial
                WHERE sts_filial = 1
                $condicaoFiliais
            )
            SELECT 
                f.nm_filial,
                f.cd_filial,
                vd.ds_marca as nm_marca,
                vd.cd_marca,
                0 as estoque_atual,
                0 as qtde_entradas,
                vd.qtde_vendida,
                0::NUMERIC(14,2) as valor_estoque,
                vd.valor_vendido::NUMERIC(14,2) as valor_vendido,
                0::NUMERIC(14,2) as preco_custo,
                vd.preco_medio::NUMERIC(14,2) as preco_venda
            FROM vendas_periodo vd
            INNER JOIN filiais_ativas f ON f.cd_filial = vd.cd_filial
            ORDER BY f.nm_filial, vd.ds_marca, vd.qtde_vendida DESC
            LIMIT 1000
        ";
        
        $params = [
            ':venda_dt_inicio' => $vendaDtInicio,
            ':venda_dt_fim' => $vendaDtFim
        ];
        
        return $this->executeEntradaVendas($sql, $params);
    }
    
    /**
     * Executa a query e organiza os resultados
     */
    private function executeEntradaVendas($sql, $params) {
        try {
            $resultados = $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            error_log("Erro em getEntradaVendas: " . $e->getMessage());
            $resultados = [];
        }
        
        if ($resultados === false) {
            $resultados = [];
        }
        
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
            $nmFilial = $row['nm_filial'];
            
            if (!isset($dados[$nmFilial])) {
                $dados[$nmFilial] = [];
            }
            
            $dados[$nmFilial][] = $row;
            
            // Acumula totais
            $totaisGerais['estoque_atual'] += (float)$row['estoque_atual'];
            $totaisGerais['qtde_entradas'] += (float)$row['qtde_entradas'];
            $totaisGerais['qtde_vendida'] += (float)$row['qtde_vendida'];
            $totaisGerais['valor_estoque'] += (float)$row['valor_estoque'];
            $totaisGerais['valor_vendido'] += (float)$row['valor_vendido'];
        }
        
        return [
            'dados' => $dados,
            'totais' => $totaisGerais
        ];
    }
        // Marca de entrada no método para debug imediato
        @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] EntradaVendas ENTER\n", FILE_APPEND);

        $vendaDtInicio = $filtros['venda_dt_inicio'];
        $vendaDtFim = $filtros['venda_dt_fim'];
        $entradaDtInicio = $filtros['entrada_dt_inicio'];
        $entradaDtFim = $filtros['entrada_dt_fim'];
        $filiais = $filtros['filiais'] ?? ['todas'];
        $estPositivo = $filtros['est_positivo'] ?? false;
        $estZerado = $filtros['est_zerado'] ?? false;
        $estNegativo = $filtros['est_negativo'] ?? false;
        
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
        
        // Monta condição de estoque - AJUSTADA para não filtrar se estoque estiver vazio
        // Se todas as 3 opções estão marcadas, não aplica filtro (mostra tudo)
        $condicaoEstoque = '';
        if (!($estPositivo && $estZerado && $estNegativo)) {
            // Se nem todas estão marcadas, aplica filtro específico
            $condicoesEstoque = [];
            if ($estPositivo) $condicoesEstoque[] = 'COALESCE(est.qtde_atual, 0) > 0';
            if ($estZerado) $condicoesEstoque[] = 'COALESCE(est.qtde_atual, 0) = 0';
            if ($estNegativo) $condicoesEstoque[] = 'COALESCE(est.qtde_atual, 0) < 0';
            
            if (!empty($condicoesEstoque)) {
                $condicaoEstoque = 'AND (' . implode(' OR ', $condicoesEstoque) . ')';
            }
        }
        // Se todas estão marcadas, $condicaoEstoque fica vazio = sem filtro
        
        // Query principal - BASEADA EM VENDAS (não em estoque)
        $sql = "
            WITH vendas_periodo AS (
                -- Vendas por marca e filial no período
                SELECT 
                    p.cd_marca,
                    p.ds_marca,
                    v.cd_filial,
                    COALESCE(SUM(v.qtde_produto), 0) as qtde_vendida,
                    COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0) as valor_vendido,
                    COALESCE(AVG(v.vl_tot_it / NULLIF(v.qtde_produto, 0)), 0) as preco_medio
                FROM dm_orcamento_vendas_consolidadas v
                INNER JOIN dm_produto p ON p.cd_cpl_tamanho = v.cd_cpl_tamanho
                WHERE DATE(v.dt_emi_pedido) >= :venda_dt_inicio::date
                  AND DATE(v.dt_emi_pedido) <= :venda_dt_fim::date
                  AND p.cd_marca IS NOT NULL
                  AND p.ds_marca IS NOT NULL
                GROUP BY p.cd_marca, p.ds_marca, v.cd_filial
            ),
            estoque_atual AS (
                -- Estoque atual por marca e filial (opcional)
                SELECT 
                    p.cd_marca,
                    est.cd_filial,
                    COALESCE(SUM(est.qtde_estoque), 0) as qtde_atual,
                    COALESCE(AVG(est.vlr_custo), 0) as preco_custo_medio
                FROM dm_estoque_atual est
                INNER JOIN dm_produto p ON p.cd_cpl_tamanho = est.cd_cpl_tamanho
                WHERE p.cd_marca IS NOT NULL
                  AND est.ativo = 1
                GROUP BY p.cd_marca, est.cd_filial
            ),
            filiais_ativas AS (
                -- Filiais ativas
                SELECT cd_filial, 
                       COALESCE(nm_fant, rz_filial, 'Filial ' || cd_filial) as nm_filial
                FROM prc_filial
                WHERE sts_filial = 1
                $condicaoFiliais
            )
            SELECT 
                f.nm_filial,
                f.cd_filial,
                vd.ds_marca as nm_marca,
                vd.cd_marca,
                COALESCE(est.qtde_atual, 0) as estoque_atual,
                0 as qtde_entradas,
                vd.qtde_vendida,
                COALESCE(est.qtde_atual * est.preco_custo_medio, 0)::NUMERIC(14,2) as valor_estoque,
                vd.valor_vendido::NUMERIC(14,2) as valor_vendido,
                COALESCE(est.preco_custo_medio, 0)::NUMERIC(14,2) as preco_custo,
                vd.preco_medio::NUMERIC(14,2) as preco_venda
            FROM vendas_periodo vd
            INNER JOIN filiais_ativas f ON f.cd_filial = vd.cd_filial
            LEFT JOIN estoque_atual est ON est.cd_marca = vd.cd_marca AND est.cd_filial = vd.cd_filial
            ORDER BY f.nm_filial, vd.ds_marca, vd.qtde_vendida DESC
            LIMIT 1000
        ";
        
        $params = [
            ':venda_dt_inicio' => $vendaDtInicio,
            ':venda_dt_fim' => $vendaDtFim
        ];
        
        // Debug: log da query
        error_log("SQL Entrada x Vendas: " . $sql);
        error_log("Params: " . print_r($params, true));
        @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] EntradaVendas SQL prepared\n", FILE_APPEND);
        @file_put_contents(__DIR__ . '/../login_debug.log', "Params: " . print_r($params, true) . "\n", FILE_APPEND);
        
        try {
            @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] EntradaVendas BEFORE fetchAll\n", FILE_APPEND);
            $resultados = $this->db->fetchAll($sql, $params);
            @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] EntradaVendas AFTER fetchAll\n", FILE_APPEND);
        } catch (Throwable $e) {
            $errMsg = sprintf("[%s] EntradaVendas EXCEPTION: %s\nTrace:\n%s\n", date('Y-m-d H:i:s'), $e->getMessage(), $e->getTraceAsString());
            @file_put_contents(__DIR__ . '/../login_debug.log', $errMsg, FILE_APPEND);
            error_log("EntradaVendas EXCEPTION: " . $e->getMessage());
            $resultados = false;
        }

        // Debug: verifica se houve erro

        if ($resultados === false) {
            error_log("ERRO ao executar query de Entrada x Vendas");
            @file_put_contents(__DIR__ . '/../login_debug.log', "[" . date('Y-m-d H:i:s') . "] EntradaVendas: resultado false\n", FILE_APPEND);
            // Evita foreach em false
            $resultados = [];
        } else {
            error_log("Query retornou " . count($resultados) . " linhas");
        }

        // Log temporário adicional em login_debug.log (facilita debug local)
        $cnt = is_array($resultados) ? count($resultados) : 0;
        $msg = sprintf("[%s] EntradaVendas result: %d linhas | params: %s\n", date('Y-m-d H:i:s'), $cnt, json_encode($params));
        @file_put_contents(__DIR__ . '/../login_debug.log', $msg, FILE_APPEND);
        
        try {
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
            
            $relEstoqueValor = $row['valor_vendido'] > 0 ? $row['valor_estoque'] / $row['valor_vendido'] : 0;
            $relEstoqueQtde = $row['qtde_vendida'] > 0 ? $row['estoque_atual'] / $row['qtde_vendida'] : 0;
            
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
                'perc_qtde_estoque' => 0,
                'perc_qtde_venda' => 0,
                'perc_valor_estoque' => 0,
                'perc_valor_venda' => 0
            ];
            
            $dados[$filial]['itens'][] = $item;
            
            $dados[$filial]['subtotal']['estoque_atual'] += $item['estoque_atual'];
            $dados[$filial]['subtotal']['qtde_entradas'] += $item['qtde_entradas'];
            $dados[$filial]['subtotal']['qtde_vendida'] += $item['qtde_vendida'];
            $dados[$filial]['subtotal']['valor_estoque'] += $item['valor_estoque'];
            $dados[$filial]['subtotal']['valor_vendido'] += $item['valor_vendido'];
            
            $totaisGerais['estoque_atual'] += $item['estoque_atual'];
            $totaisGerais['qtde_entradas'] += $item['qtde_entradas'];
            $totaisGerais['qtde_vendida'] += $item['qtde_vendida'];
            $totaisGerais['valor_estoque'] += $item['valor_estoque'];
            $totaisGerais['valor_vendido'] += $item['valor_vendido'];
        }
        } catch (Throwable $e) {
            $errMsg = sprintf("[%s] EntradaVendas PROCESSING EXCEPTION: %s\nTrace:\n%s\n", date('Y-m-d H:i:s'), $e->getMessage(), $e->getTraceAsString());
            @file_put_contents(__DIR__ . '/../login_debug.log', $errMsg, FILE_APPEND);
            error_log("EntradaVendas PROCESSING EXCEPTION: " . $e->getMessage());
            return [
                'dados' => [],
                'totais' => [
                    'estoque_atual' => 0,
                    'qtde_entradas' => 0,
                    'qtde_vendida' => 0,
                    'valor_estoque' => 0,
                    'valor_vendido' => 0,
                    'margem' => 0
                ]
            ];
        }
        
        // Calcula percentuais
        foreach ($dados as $filial => &$filialData) {
            $subtotal = $filialData['subtotal'];
            
            if ($subtotal['valor_vendido'] > 0) {
                $custoTotal = $subtotal['valor_vendido'] - ($subtotal['valor_estoque'] > 0 ? $subtotal['valor_estoque'] : 0);
                $filialData['subtotal']['margem'] = (($subtotal['valor_vendido'] - $custoTotal) / $subtotal['valor_vendido']) * 100;
            } else {
                $filialData['subtotal']['margem'] = 0;
            }
            
            foreach ($filialData['itens'] as &$item) {
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
