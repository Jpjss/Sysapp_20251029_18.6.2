-- Relatório: Top produtos (por quantidade) no período
-- Ajuste os nomes de tabelas/colunas conforme mapping_diaazze.json
SELECT
	pr.cd_produto AS cd_produto,
	pr.nm_produto,
	SUM(COALESCE(vi.qt, vi.qtde, 0)) AS quantidade_vendida,
	SUM(COALESCE(vi.vl_unit, vi.vlr_vd, 0) * COALESCE(vi.qt, vi.qtde, 0)) AS receita
FROM est_inventario_item vi
LEFT JOIN produtos pr ON pr.cd_produto = vi.cd_produto
JOIN ped_vd v ON v.cd_ped = vi.cd_venda
WHERE (COALESCE(v.dt_hr_ped::date, v.dt_venda::date)) BETWEEN :start_date::date AND :end_date::date
GROUP BY pr.cd_produto, pr.nm_produto
ORDER BY quantidade_vendida DESC
LIMIT 50;