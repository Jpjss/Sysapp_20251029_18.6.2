-- Relatório: Contas a receber vencidas
-- Ajuste os nomes de colunas conforme mapping_diaazze.json (ex.: dt_vencimento, situacao)
SELECT
		cr.*,
		p.nm_pessoa
FROM erp_importacao_receber cr
LEFT JOIN dm_cliente p ON p.cd_pessoa = cr.cd_pessoa
WHERE COALESCE(cr.dt_vencimento, cr.dt_venc, cr.vencimento)::date < CURRENT_DATE
	AND COALESCE(UPPER(COALESCE(cr.situacao, cr.status)), '') NOT IN ('PAGO','LIQUIDADO')
ORDER BY COALESCE(cr.dt_vencimento, cr.dt_venc, cr.vencimento)::date ASC, p.nm_pessoa;