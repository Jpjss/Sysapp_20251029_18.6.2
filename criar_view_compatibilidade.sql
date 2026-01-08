-- =============================================
-- SCRIPT DE COMPATIBILIDADE - VIEWS DO ERP
-- Mapeia as tabelas reais do BD_PROPASSO para os nomes esperados
-- Data: 05/01/2026
-- =============================================

-- ====================
-- VIEW 1: dm_orcamento_vendas_consolidadas
-- ====================
-- A tabela real se chama "dm_orcamento_vendas_consolidadas_cubo"
-- Esta view cria um alias sem o sufixo "_cubo"

-- Remover view antiga
DROP VIEW IF EXISTS dm_orcamento_vendas_consolidadas CASCADE;

-- Criar nova view com todos os campos
CREATE OR REPLACE VIEW dm_orcamento_vendas_consolidadas AS
SELECT 
    *,
    nm_pessoa as nm_cliente  -- Alias adicional para compatibilidade
FROM dm_orcamento_vendas_consolidadas_cubo;

-- ====================
-- COMENTÁRIOS
-- ====================
COMMENT ON VIEW dm_orcamento_vendas_consolidadas IS 
'View de compatibilidade - mapeia dm_orcamento_vendas_consolidadas_cubo para o nome usado nos relatórios';

-- ====================
-- VERIFICAÇÕES
-- ====================

-- 1. Verificar se a view foi criada
SELECT 
    viewname,
    definition
FROM pg_views 
WHERE viewname = 'dm_orcamento_vendas_consolidadas';

-- 2. Testar a view com uma query simples
SELECT COUNT(*) as total_registros 
FROM dm_orcamento_vendas_consolidadas;

-- 3. Verificar campos disponíveis
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'dm_orcamento_vendas_consolidadas'
ORDER BY ordinal_position;
