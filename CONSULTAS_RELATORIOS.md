# Consultas SQL dos Relatórios - Sistema SysApp

Data: 05/01/2026

Este documento contém todas as consultas SQL utilizadas nos relatórios do sistema, corrigidas para usar as tabelas corretas do ERP.

---

## 1. ESTATÍSTICAS GERAIS (Dashboard Principal)

### Total de Clientes
```sql
SELECT COUNT(DISTINCT cd_pessoa) as total 
FROM dm_orcamento_vendas_consolidadas
```

### Total de Vendas (Geral)
```sql
SELECT COUNT(DISTINCT cd_pedido) as total, 
       COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total 
FROM dm_orcamento_vendas_consolidadas
```

### Vendas Hoje
```sql
SELECT COUNT(DISTINCT cd_pedido) as total, 
       COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_hoje 
FROM dm_orcamento_vendas_consolidadas 
WHERE dt_emi_pedido >= CURRENT_DATE 
AND dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'
```

### Vendas no Mês
```sql
SELECT COUNT(DISTINCT cd_pedido) as total, 
       COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_mes 
FROM dm_orcamento_vendas_consolidadas 
WHERE EXTRACT(MONTH FROM dt_emi_pedido) = EXTRACT(MONTH FROM CURRENT_DATE)
AND EXTRACT(YEAR FROM dt_emi_pedido) = EXTRACT(YEAR FROM CURRENT_DATE)
```

---

## 2. VENDAS POR PERÍODO

```sql
SELECT DATE(dt_emi_pedido) as data, 
       COUNT(DISTINCT cd_pedido) as total,
       COUNT(DISTINCT cd_pessoa) as clientes_unicos,
       COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
FROM dm_orcamento_vendas_consolidadas
WHERE DATE(dt_emi_pedido) BETWEEN :dt_inicio AND :dt_fim
GROUP BY DATE(dt_emi_pedido)
ORDER BY DATE(dt_emi_pedido)
```

---

## 3. TOP CLIENTES POR VENDAS

```sql
SELECT v.cd_pessoa,
       v.nm_cliente,
       COUNT(DISTINCT v.cd_pedido) as total_atendimentos,
       MAX(v.dt_emi_pedido) as ultimo_atendimento,
       COALESCE(SUM(v.vl_tot_it - v.vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
FROM dm_orcamento_vendas_consolidadas v
GROUP BY v.cd_pessoa, v.nm_cliente
ORDER BY valor_total DESC
LIMIT :limite
```

---

## 4. MARCAS MAIS VENDIDAS (Dashboard de Marcas)

```sql
SELECT 
    dm_produto.cd_marca,
    dm_produto.ds_marca,
    COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
    SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
    SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
FROM dm_produto
INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
    ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL ':periodo days'
    AND dm_produto.cd_marca IS NOT NULL
    AND dm_produto.ds_marca IS NOT NULL
GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
ORDER BY quantidade_vendida DESC
LIMIT :limite
```

---

## 5. HISTÓRICO DE VENDAS DE UMA MARCA (Por Dia)

```sql
WITH date_series AS (
    SELECT 
        generate_series(
            CURRENT_DATE - INTERVAL ':periodo days',
            CURRENT_DATE,
            INTERVAL '1 day'
        )::date AS data
),
vendas_marca AS (
    SELECT 
        TO_CHAR(dm_venda.dt_emi_pedido, 'YYYY-MM-DD') as periodo,
        dm_venda.dt_emi_pedido::date as data_base,
        COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
        SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
        SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
    FROM dm_produto
    INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
        ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
    WHERE dm_produto.cd_marca = :cd_marca
        AND dm_venda.dt_emi_pedido >= CURRENT_DATE - INTERVAL ':periodo days'
    GROUP BY periodo, data_base
)
SELECT 
    ds.data,
    TO_CHAR(ds.data, 'YYYY-MM-DD') as periodo,
    COALESCE(vm.total_vendas, 0) as total_vendas,
    COALESCE(vm.quantidade_vendida, 0) as quantidade_vendida,
    COALESCE(vm.valor_total, 0)::NUMERIC(14,2) as valor_total
FROM date_series ds
LEFT JOIN vendas_marca vm ON TO_CHAR(ds.data, 'YYYY-MM-DD') = vm.periodo
ORDER BY ds.data ASC
```

---

## 6. HISTÓRICO DE VENDAS DE UMA MARCA (Por Hora - Hoje)

```sql
WITH hour_series AS (
    SELECT 
        generate_series(
            CURRENT_DATE,
            CURRENT_DATE + INTERVAL '23 hours',
            INTERVAL '1 hour'
        )::timestamp AS hora_inicio
),
vendas_marca AS (
    SELECT 
        date_trunc('hour', dm_venda.dt_emi_pedido) as hora_inicio,
        COUNT(DISTINCT dm_venda.cd_pedido) as total_vendas,
        SUM(COALESCE(dm_venda.qtde_produto, 0)) as quantidade_vendida,
        SUM(COALESCE(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional, 0))::NUMERIC(14,2) as valor_total
    FROM dm_produto
    INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
        ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
    WHERE dm_produto.cd_marca = :cd_marca
        AND dm_venda.dt_emi_pedido >= CURRENT_DATE
        AND dm_venda.dt_emi_pedido < CURRENT_DATE + INTERVAL '1 day'
    GROUP BY hora_inicio
)
SELECT 
    hs.hora_inicio as data,
    TO_CHAR(hs.hora_inicio, 'HH24:00') as periodo,
    COALESCE(vm.total_vendas, 0) as total_vendas,
    COALESCE(vm.quantidade_vendida, 0) as quantidade_vendida,
    COALESCE(vm.valor_total, 0)::NUMERIC(14,2) as valor_total
FROM hour_series hs
LEFT JOIN vendas_marca vm ON hs.hora_inicio = vm.hora_inicio
ORDER BY hs.hora_inicio ASC
```

---

## 7. VENDAS DETALHADAS POR DIA

```sql
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
```

---

## 8. TOTAIS DE VENDAS NO PERÍODO

```sql
SELECT 
    COUNT(DISTINCT cd_pedido) as total_atendimentos,
    COUNT(DISTINCT cd_pessoa) as clientes_unicos,
    COALESCE(SUM(vl_tot_it - vl_devol_proporcional), 0)::NUMERIC(14,2) as valor_total
FROM dm_orcamento_vendas_consolidadas
WHERE DATE(dt_emi_pedido) >= :dt_inicio::date 
  AND DATE(dt_emi_pedido) <= :dt_fim::date
```

---

## 9. VERIFICAR TABELAS DISPONÍVEIS NO BANCO

```sql
-- Ver todas as tabelas
SELECT tablename 
FROM pg_tables 
WHERE schemaname = 'public' 
ORDER BY tablename;

-- Ver colunas de uma tabela específica
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'dm_orcamento_vendas_consolidadas' 
ORDER BY ordinal_position;
```

---

## 10. ESTRUTURA DA TABELA PRINCIPAL DE VENDAS

```sql
SELECT 
    column_name,
    data_type,
    character_maximum_length,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'dm_orcamento_vendas_consolidadas'
ORDER BY ordinal_position;
```

---

## NOTAS IMPORTANTES:

### Tabelas Corretas do ERP:
- ✅ `dm_orcamento_vendas_consolidadas` - Vendas consolidadas
- ✅ `dm_produto` - Produtos e marcas
- ❌ NÃO usar `ped_vd` (tabela antiga)
- ❌ NÃO usar `glb_pessoa` (tabela antiga)

### Campos Principais:
- `cd_pedido` - Código do pedido
- `cd_pessoa` - Código do cliente
- `nm_cliente` - Nome do cliente
- `cd_cpl_tamanho` - Código do produto/tamanho
- `dt_emi_pedido` - Data/hora da emissão
- `qtde_produto` - Quantidade vendida
- `vl_tot_it` - Valor total do item
- `vl_devol_proporcional` - Valor de devolução
- **VALOR REAL = `vl_tot_it - vl_devol_proporcional`**

### Campos de Marca (dm_produto):
- `cd_marca` - Código da marca
- `ds_marca` - Descrição/nome da marca
- `cd_cpl_tamanho` - Join com vendas

### Performance:
- Sempre usar `COUNT(DISTINCT cd_pedido)` para contar vendas
- Usar `COALESCE()` para evitar NULL
- Converter para `NUMERIC(14,2)` para valores monetários
- Usar índices nas colunas de data para melhor performance
