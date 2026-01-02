-- Views materializadas e índices para acelerar relatórios pesados
-- Use REFRESH MATERIALIZED VIEW CONCURRENTLY <view> para atualizações sem travar leitura

-- 1) Vendas diárias (materializada)
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_vendas_diarias AS
SELECT
  DATE(v.dt_venda) AS dia,
  COUNT(*) AS pedidos,
  SUM(v.vl_total) AS total_vendido
FROM venda v
GROUP BY DATE(v.dt_venda);

CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_vendas_diarias_dia ON mv_vendas_diarias(dia);

-- 2) Top produtos (materializada)
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_top_produtos AS
SELECT
  pr.cd_produto,
  pr.nm_produto,
  SUM(vi.qt) AS quantidade_vendida,
  SUM(vi.vl_unit * vi.qt) AS receita
FROM venda_item vi
JOIN venda v ON v.cd_venda = vi.cd_venda
JOIN produto pr ON pr.cd_produto = vi.cd_produto
GROUP BY pr.cd_produto, pr.nm_produto;

CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_top_produtos_produto ON mv_top_produtos(cd_produto);

-- 3) Saldo de estoque por produto (materializada)
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_estoque_saldo AS
SELECT
  p.cd_produto,
  p.nm_produto,
  COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'entrada' THEN em.qt ELSE 0 END),0)
    - COALESCE(SUM(CASE WHEN em.tipo_mov ILIKE 'saida' THEN em.qt ELSE 0 END),0) AS saldo
FROM produto p
LEFT JOIN estoque_movimento em ON em.cd_produto = p.cd_produto
GROUP BY p.cd_produto, p.nm_produto;

CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_estoque_saldo_produto ON mv_estoque_saldo(cd_produto);

-- 4) Recebimentos diários (materializada)
CREATE MATERIALIZED VIEW IF NOT EXISTS mv_recebimentos_diarios AS
SELECT
  DATE(pg.dt_pagto) AS dia,
  SUM(pg.vl_pagto) AS total_recebido
FROM pagamentos pg
GROUP BY DATE(pg.dt_pagto);

CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_recebimentos_diarios_dia ON mv_recebimentos_diarios(dia);

-- 5) Contas a receber vencidas (view simples para leitura em tempo real)
CREATE OR REPLACE VIEW vw_contas_receber_vencidas AS
SELECT
  cr.cd_receber,
  cr.cd_pessoa,
  p.nm_pessoa,
  cr.dt_vencimento,
  cr.vl_parcela,
  cr.situacao
FROM contas_receber cr
LEFT JOIN glb_pessoa p ON p.cd_pessoa = cr.cd_pessoa
WHERE cr.dt_vencimento < CURRENT_DATE
  AND COALESCE(UPPER(cr.situacao),'') NOT IN ('PAGO','LIQUIDADO');

-- Índices adicionais recomendados em tabelas de origem (crie conforme necessidade)
-- Exemplo de criação não bloqueante (quando tabela grande, use CONCURRENTLY manualmente no psql):
-- CREATE INDEX IF NOT EXISTS idx_venda_dt_venda ON venda(dt_venda);
-- CREATE INDEX IF NOT EXISTS idx_venda_cd_pessoa ON venda(cd_pessoa);
-- CREATE INDEX IF NOT EXISTS idx_venda_item_cd_produto ON venda_item(cd_produto);
-- CREATE INDEX IF NOT EXISTS idx_estoque_movimento_dt ON estoque_movimento(dt_mov);
-- CREATE INDEX IF NOT EXISTS idx_contas_receber_venc ON contas_receber(dt_vencimento);

-- Recomendações de refresh (executar via job agendado):
-- REFRESH MATERIALIZED VIEW CONCURRENTLY mv_vendas_diarias;
-- REFRESH MATERIALIZED VIEW CONCURRENTLY mv_top_produtos;
-- REFRESH MATERIALIZED VIEW CONCURRENTLY mv_estoque_saldo;
-- REFRESH MATERIALIZED VIEW CONCURRENTLY mv_recebimentos_diarios;

-- Nota: para usar CONCURRENTLY a materialized view precisa ter um índice único.
-- Fim
