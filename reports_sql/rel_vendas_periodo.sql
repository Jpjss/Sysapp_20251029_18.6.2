-- Relatório: Vendas por período
-- Parâmetros: :start_date, :end_date
-- Ajuste os nomes de colunas se necessário de acordo com mapping_diaazze.json
SELECT
	DATE(COALESCE(dt_hr_ped, dt_venda, now())) AS dia,
	COUNT(*) AS pedidos,
	SUM(COALESCE(vl_total, total, valor_vendido, 0)) AS total_vendido
FROM ped_vd
WHERE (COALESCE(dt_hr_ped::date, dt_venda::date)) BETWEEN :start_date::date AND :end_date::date
GROUP BY DATE(COALESCE(dt_hr_ped, dt_venda, now()))
ORDER BY dia;
