-- Consultas modelo para relatórios SysApp
-- Substitua :start_date e :end_date por parâmetros ou use prepared statements

-- 1) Vendas por período (total diário)
-- Parâmetros: :start_date, :end_date
SELECT
    DATE(v.dt_venda) AS dia,
    COUNT(*) AS pedidos,
    SUM(v.vl_total) AS total_vendido
FROM venda v
WHERE v.dt_venda::date BETWEEN :start_date::date AND :end_date::date
GROUP BY DATE(v.dt_venda)
ORDER BY DATE(v.dt_venda);

-- 2) Vendas por cliente (total no período)
SELECT
    p.cd_pessoa,
    p.nm_pessoa,
    COUNT(v.cd_venda) AS pedidos,
    SUM(v.vl_total) AS total_vendido
FROM venda v
LEFT JOIN glb_pessoa p ON p.cd_pessoa = v.cd_pessoa
WHERE v.dt_venda::date BETWEEN :start_date::date AND :end_date::date
GROUP BY p.cd_pessoa, p.nm_pessoa
ORDER BY total_vendido DESC;

-- 3) Top produtos vendidos (por quantidade) no período
SELECT
    pr.cd_produto,
    pr.nm_produto,
    SUM(vi.qt) AS quantidade_vendida,
    SUM(vi.vl_unit * vi.qt) AS receita
FROM venda_item vi
INNER JOIN venda v ON v.cd_venda = vi.cd_venda
INNER JOIN produto pr ON pr.cd_produto = vi.cd_produto
WHERE v.dt_venda::date BETWEEN :start_date::date AND :end_date::date
GROUP BY pr.cd_produto, pr.nm_produto
ORDER BY quantidade_vendida DESC
LIMIT 50;

-- 4) Estoque atual por produto (soma de movimentos: entradas - saídas)
-- Pressupõe que estoque_movimento.tipo_mov é 'entrada' ou 'saida'
SELECT
    p.cd_produto,
    p.nm_produto,
    COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'entrada' THEN em.qt ELSE 0 END),0)
      - COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'saida' THEN em.qt ELSE 0 END),0) AS saldo
FROM produto p
LEFT JOIN estoque_movimento em ON em.cd_produto = p.cd_produto
GROUP BY p.cd_produto, p.nm_produto
ORDER BY saldo ASC;

-- 5) Contas a receber vencidas (até hoje)
SELECT
    cr.cd_receber,
    p.cd_pessoa,
    p.nm_pessoa,
    cr.dt_vencimento,
    cr.vl_parcela,
    cr.situacao
FROM contas_receber cr
LEFT JOIN glb_pessoa p ON p.cd_pessoa = cr.cd_pessoa
WHERE cr.dt_vencimento < CURRENT_DATE
  AND COALESCE(UPPER(cr.situacao),'') NOT IN ('PAGO','LIQUIDADO')
ORDER BY cr.dt_vencimento;

-- 6) Clientes ativos
SELECT cd_pessoa, nm_pessoa, nr_cpf_cnpj, ds_email
FROM glb_pessoa
WHERE fg_ativo = 'S'
ORDER BY nm_pessoa;

-- 7) Produtos com estoque baixo (threshold parâmetro)
-- Substitua :threshold pelo número mínimo desejado
WITH saldo_prod AS (
    SELECT
        p.cd_produto,
        p.nm_produto,
        COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'entrada' THEN em.qt ELSE 0 END),0)
          - COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'saida' THEN em.qt ELSE 0 END),0) AS saldo
    FROM produto p
    LEFT JOIN estoque_movimento em ON em.cd_produto = p.cd_produto
    GROUP BY p.cd_produto, p.nm_produto
)
SELECT * FROM saldo_prod WHERE saldo <= :threshold ORDER BY saldo ASC;

-- 8) Resumo financeiro (receitas por período a partir de pagamentos)
SELECT
    DATE(pg.dt_pagto) AS dia,
    SUM(pg.vl_pagto) AS total_recebido
FROM pagamentos pg
WHERE pg.dt_pagto::date BETWEEN :start_date::date AND :end_date::date
GROUP BY DATE(pg.dt_pagto)
ORDER BY DATE(pg.dt_pagto);

-- 9) Questionário: respostas por questionário
SELECT
    q.cd_questionario,
    q.nm_questionario,
    COUNT(r.cd_resposta) AS total_respostas
FROM glb_questionario q
LEFT JOIN glb_questionario_pergunta qp ON qp.cd_questionario = q.cd_questionario
LEFT JOIN glb_questionario_resposta r ON r.cd_pergunta = qp.cd_pergunta
WHERE q.fg_ativo = 'S'
GROUP BY q.cd_questionario, q.nm_questionario
ORDER BY total_respostas DESC;

-- 10) Notas fiscais emitidas por período
SELECT
    nf.cd_nf,
    nf.nr_nf,
    nf.dt_emissao,
    nf.vl_total,
    v.cd_venda,
    p.cd_pessoa,
    p.nm_pessoa
FROM nota_fiscal nf
LEFT JOIN venda v ON v.cd_venda = nf.cd_venda
LEFT JOIN glb_pessoa p ON p.cd_pessoa = v.cd_pessoa
WHERE nf.dt_emissao::date BETWEEN :start_date::date AND :end_date::date
ORDER BY nf.dt_emissao DESC;

-- Execução rápida via psql (exemplo):
-- psql -h localhost -U postgres -d sysapp -f relatorios_queries.sql
