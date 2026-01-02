-- Relatório: Saldo de estoque por produto
-- Ajuste os nomes de tabelas/colunas conforme mapping_diaazze.json
SELECT
	p.cd_produto,
	p.nm_produto,
	COALESCE(
		SUM(
			CASE WHEN COALESCE(em.tipo_mov, em.tipo, '') ILIKE 'entrada' THEN COALESCE(em.qt, em.qtde, em.quantidade, 0) ELSE 0 END
		), 0
	) - COALESCE(
		SUM(
			CASE WHEN COALESCE(em.tipo_mov, em.tipo, '') ILIKE 'saida' OR COALESCE(em.tipo_mov, em.tipo, '') ILIKE 'saída' THEN COALESCE(em.qt, em.qtde, em.quantidade, 0) ELSE 0 END
		), 0
	) AS saldo
FROM produtos p
LEFT JOIN prc_filial em ON em.cd_produto = p.cd_produto
GROUP BY p.cd_produto, p.nm_produto
ORDER BY saldo ASC;