# TABELAS DE PRODUTOS, ESTOQUE E VENDAS

## Resumo da Exploração
- **Total de tabelas encontradas:** 1197
- **Banco analisado:** bd_propasso
- **Data:** 12/12/2025

---

## 📦 PRODUTOS

### Principais Tabelas de Produtos:

1. **est_produto** / **est_produtos**
   - Tabela principal de cadastro de produtos
   - Contém: código, descrição, marca, modelo, categoria, família

2. **est_produto_cpl**
   - Complemento de informações do produto
   - Dados adicionais e configurações específicas

3. **est_produto_cpl_tamanho**
   - Grades e tamanhos dos produtos
   - Variações de tamanho/cor por produto

4. **est_produto_bar**
   - Códigos de barras (EAN) dos produtos
   - Vincula código de barras ao produto

5. **est_produto_marca**
   - Cadastro de marcas
   - Informações sobre fabricantes/marcas

6. **est_produto_modelo**
   - Modelos de produtos
   - Variações de modelo por marca

7. **est_produto_foto**
   - Imagens dos produtos
   - Fotos para catálogo/sistema

---

## 📊 ESTOQUE

### Tabelas de Controle de Estoque:

1. **est_saldo**
   - **PRINCIPAL** - Saldo atual de estoque por produto/filial
   - Colunas importantes:
     - cd_produto
     - cd_filial
     - quantidade em estoque
     - valor do estoque

2. **est_saldo_cpl_tamanho**
   - Saldo de estoque por grade/tamanho
   - Quantidade disponível por variação

3. **est_balanco**
   - Balanços/inventários realizados
   - Contagens físicas de estoque

4. **est_balanco_it**
   - Itens do balanço
   - Produtos contados no inventário

5. **est_entrada**
   - Entradas de mercadorias no estoque
   - Compras, transferências recebidas

6. **est_entrada_it**
   - Itens das entradas de estoque
   - Produtos e quantidades recebidas

7. **est_saida**
   - Saídas de mercadorias do estoque
   - Vendas, transferências enviadas

8. **est_saida_it**
   - Itens das saídas de estoque
   - Produtos e quantidades saídas

9. **est_transf**
   - Transferências entre filiais
   - Movimentação de estoque entre lojas

10. **est_transf_it**
    - Itens das transferências
    - Produtos transferidos entre filiais

---

## 💰 VENDAS

### Tabelas de Vendas e Pedidos:

1. **ped_vd**
   - **PRINCIPAL** - Pedidos de venda
   - Cabeçalho dos pedidos

2. **ped_vd_it**
   - **PRINCIPAL** - Itens dos pedidos de venda
   - **CONTÉM:** quantidade vendida por produto
   - Colunas importantes:
     - cd_produto
     - quantidade
     - vlr_unitario
     - vlr_desconto
     - vlr_total

3. **ped_vd_cpl**
   - Complemento dos pedidos
   - Informações adicionais de venda

4. **nf_saida**
   - Notas fiscais de saída
   - NFe/NFCe emitidas

5. **nf_saida_it**
   - Itens das notas fiscais
   - Produtos faturados

6. **pdv_nf**
   - Notas fiscais do PDV
   - Cupons fiscais emitidos

7. **pdv_nf_it**
   - Itens das notas do PDV
   - Produtos vendidos no PDV

---

## 🏷️ MARCAS E MODELOS

### Tabelas de Classificação:

1. **est_produto_marca**
   - Cadastro de marcas
   - Informações sobre fabricantes

2. **est_produto_modelo**
   - Modelos de produtos
   - Variações por marca

3. **glb_etiqueta_modelo**
   - Modelos de etiquetas
   - Layouts de impressão

4. **est_fabricante**
   - Cadastro de fabricantes
   - Fornecedores/indústrias

---

## 📏 TAMANHOS E GRADES

### Tabelas de Variações:

1. **est_produto_cpl_tamanho**
   - **PRINCIPAL** - Grades e tamanhos
   - Variações de produtos (P, M, G, cores, etc.)

2. **est_saldo_cpl_tamanho**
   - Saldo de estoque por tamanho
   - Quantidade disponível por variação

3. **est_tamanho**
   - Cadastro de tamanhos
   - Tabela de tamanhos disponíveis

4. **est_grade**
   - Grades de produtos
   - Configurações de grade

---

## 📈 QUANTIDADES

### Principais Campos de Quantidade:

#### Estoque:
- **est_saldo.qtde_estoque** - Quantidade em estoque atual
- **est_saldo_cpl_tamanho.qtde** - Quantidade por tamanho
- **est_balanco_it.qtde_sistema** - Quantidade no sistema (inventário)
- **est_balanco_it.qtde_fisica** - Quantidade física contada

#### Vendas:
- **ped_vd_it.quantidade** - Quantidade vendida no pedido
- **nf_saida_it.quantidade** - Quantidade faturada na NF
- **pdv_nf_it.quantidade** - Quantidade vendida no PDV

#### Movimentações:
- **est_entrada_it.quantidade** - Quantidade recebida
- **est_saida_it.quantidade** - Quantidade saída
- **est_transf_it.quantidade** - Quantidade transferida

---

## 🔗 RELACIONAMENTOS IMPORTANTES

### Estrutura de Produto:
```
est_produto (produto principal)
    ├── est_produto_cpl (complemento)
    ├── est_produto_cpl_tamanho (grades/tamanhos)
    ├── est_produto_bar (códigos de barras)
    ├── est_produto_foto (imagens)
    └── est_saldo (estoque por filial)
        └── est_saldo_cpl_tamanho (estoque por grade)
```

### Estrutura de Venda:
```
ped_vd (pedido)
    └── ped_vd_it (itens vendidos)
        └── est_produto_cpl_tamanho (variação do produto)
            └── est_produto (produto base)
```

### Estrutura de Estoque:
```
est_saldo (saldo geral)
    └── est_saldo_cpl_tamanho (saldo por variação)
        └── est_produto_cpl_tamanho (variação)
            └── est_produto (produto)
```

---

## 📋 CATEGORIZAÇÃO

### Tabelas de Classificação de Produtos:

1. **est_categoria**
   - Categorias de produtos
   
2. **est_familia**
   - Famílias de produtos

3. **est_grupo**
   - Grupos de produtos

4. **est_sub_grupo**
   - Subgrupos de produtos

5. **est_departamento**
   - Departamentos

6. **est_linha**
   - Linhas de produtos

7. **est_secao**
   - Seções

---

## 💡 CONSULTAS ÚTEIS

### 1. Estoque Atual por Produto:
```sql
SELECT 
    p.cd_produto,
    p.ds_produto,
    m.ds_marca,
    s.qtde_estoque,
    s.vlr_estoque
FROM est_produto p
LEFT JOIN est_produto_marca m ON p.cd_marca = m.cd_marca
LEFT JOIN est_saldo s ON p.cd_produto = s.cd_produto
WHERE s.cd_filial = ? 
  AND s.qtde_estoque > 0
ORDER BY p.ds_produto;
```

### 2. Vendas por Produto (Período):
```sql
SELECT 
    p.cd_produto,
    p.ds_produto,
    SUM(pi.quantidade) as qtde_vendida,
    SUM(pi.vlr_total) as vlr_total_vendido
FROM ped_vd pv
INNER JOIN ped_vd_it pi ON pv.cd_ped = pi.cd_ped
INNER JOIN est_produto p ON pi.cd_produto = p.cd_produto
WHERE pv.dt_emissao BETWEEN ? AND ?
  AND pv.cd_filial = ?
GROUP BY p.cd_produto, p.ds_produto
ORDER BY qtde_vendida DESC;
```

### 3. Estoque por Grade/Tamanho:
```sql
SELECT 
    p.cd_produto,
    p.ds_produto,
    pt.ds_tamanho,
    st.qtde as qtde_estoque
FROM est_produto p
INNER JOIN est_produto_cpl_tamanho pt ON p.cd_produto = pt.cd_produto
LEFT JOIN est_saldo_cpl_tamanho st ON pt.cd_cpl_tamanho = st.cd_cpl_tamanho
WHERE st.cd_filial = ?
  AND st.qtde > 0
ORDER BY p.ds_produto, pt.ds_tamanho;
```

---

## 📊 OUTRAS TABELAS RELACIONADAS

### Precificação:
- **est_produto_preco** - Preços dos produtos
- **est_preco_tabela** - Tabelas de preço
- **preco_pendencia_exportacao_ecommerce** - Pendências de preço para e-commerce

### Fornecedores:
- **glb_pessoa** - Cadastro de pessoas/fornecedores
- **est_fabricante** - Fabricantes

### Custos:
- **est_produto_custo** - Custos dos produtos
- **est_entrada_it** - Custo nas entradas

### Impostos:
- **est_produto_tributacao** - Tributação dos produtos
- **nf_saida_it** - Impostos nas notas fiscais

### E-commerce:
- **import_ecommerce_pedido** - Pedidos do e-commerce
- **import_ecommerce_pedido_entrega** - Dados de entrega

---

## 🎯 CAMPOS PRINCIPAIS POR TABELA

### est_produto:
- cd_produto (código)
- ds_produto (descrição)
- cd_marca (marca)
- cd_modelo (modelo)
- cd_categoria (categoria)
- cd_familia (família)
- cd_grupo (grupo)
- fg_ativo (ativo/inativo)

### est_saldo:
- cd_produto
- cd_filial
- qtde_estoque (quantidade em estoque)
- vlr_estoque (valor do estoque)
- qtde_reservada (quantidade reservada)

### ped_vd_it:
- cd_ped (código do pedido)
- cd_produto
- quantidade (quantidade vendida)
- vlr_unitario (valor unitário)
- vlr_desconto (desconto)
- vlr_total (total do item)

---

**Observações:**
- Os nomes exatos das tabelas podem variar entre bancos
- Algumas tabelas podem ter prefixos específicos por filial
- Consulte sempre o banco de dados atual para confirmar estruturas
