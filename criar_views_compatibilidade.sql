-- =============================================
-- SCRIPT DE COMPATIBILIDADE - VIEWS DO ERP
-- Gerado automaticamente em: 05/01/2026 23:41:04
-- =============================================

-- View: dm_orcamento_vendas_consolidadas
-- Mapeia a tabela real 'sysapp_config_empresas' para o formato esperado

CREATE OR REPLACE VIEW dm_orcamento_vendas_consolidadas AS
SELECT 
    cd_ped as cd_pedido,
    cd_pessoa as cd_pessoa,
    cd_filial as cd_filial,
    dt_hr_ped as dt_emi_pedido,
    -- Adicione outros campos conforme necessário
    NULL as nm_cliente,
    NULL as cd_cpl_tamanho,
    NULL as qtde_produto,
    NULL as vl_tot_it,
    0 as vl_devol_proporcional
FROM sysapp_config_empresas;

