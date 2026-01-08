<?php
// SNIPPET: Método adaptativo para getEntradaVendas()
// Este código deve substituir o método atual

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
