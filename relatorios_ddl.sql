-- DDL sugerido para relatórios SysApp
-- Cria as tabelas de negócio faltantes esperadas pelos relatórios
-- Compatível com PostgreSQL (usa SERIAL conforme schema existente)

-- Pessoas / Clientes
CREATE TABLE IF NOT EXISTS glb_pessoa (
    cd_pessoa SERIAL PRIMARY KEY,
    nm_pessoa VARCHAR(200) NOT NULL,
    nr_cpf_cnpj VARCHAR(20),
    ds_email VARCHAR(200),
    tipo_pessoa VARCHAR(50),
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS glb_pessoa_endereco (
    cd_endereco SERIAL PRIMARY KEY,
    cd_pessoa INTEGER NOT NULL REFERENCES glb_pessoa(cd_pessoa) ON DELETE CASCADE,
    ds_logradouro VARCHAR(300),
    nr VARCHAR(50),
    bairro VARCHAR(100),
    cep VARCHAR(20),
    cidade VARCHAR(100),
    uf VARCHAR(2),
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS glb_pessoa_fone (
    cd_fone SERIAL PRIMARY KEY,
    cd_pessoa INTEGER NOT NULL REFERENCES glb_pessoa(cd_pessoa) ON DELETE CASCADE,
    nr_fone VARCHAR(30),
    ds_tipo VARCHAR(50),
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS glb_pessoa_obs_contato (
    cd_obs SERIAL PRIMARY KEY,
    cd_pessoa INTEGER NOT NULL REFERENCES glb_pessoa(cd_pessoa) ON DELETE CASCADE,
    ds_observacao TEXT,
    dt_contato TIMESTAMP,
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produtos / Estoque
CREATE TABLE IF NOT EXISTS categoria_produto (
    cd_categoria SERIAL PRIMARY KEY,
    nm_categoria VARCHAR(200) NOT NULL
);

CREATE TABLE IF NOT EXISTS produto (
    cd_produto SERIAL PRIMARY KEY,
    nm_produto VARCHAR(300) NOT NULL,
    cd_categoria INTEGER REFERENCES categoria_produto(cd_categoria),
    sku VARCHAR(100),
    vl_custo NUMERIC(14,2) DEFAULT 0,
    vl_venda NUMERIC(14,2) DEFAULT 0,
    fg_ativo CHAR(1) DEFAULT 'S'
);

CREATE TABLE IF NOT EXISTS estoque_movimento (
    cd_mov SERIAL PRIMARY KEY,
    cd_produto INTEGER NOT NULL REFERENCES produto(cd_produto) ON DELETE RESTRICT,
    dt_mov TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    qt NUMERIC(14,4) NOT NULL,
    tipo_mov VARCHAR(20) NOT NULL,
    origem VARCHAR(100),
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa)
);

-- Vendas / Faturamento
CREATE TABLE IF NOT EXISTS venda (
    cd_venda SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    dt_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vl_total NUMERIC(14,2) DEFAULT 0,
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa),
    situacao VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS venda_item (
    cd_item SERIAL PRIMARY KEY,
    cd_venda INTEGER NOT NULL REFERENCES venda(cd_venda) ON DELETE CASCADE,
    cd_produto INTEGER REFERENCES produto(cd_produto),
    qt NUMERIC(14,4) NOT NULL,
    vl_unit NUMERIC(14,4) DEFAULT 0,
    vl_total NUMERIC(14,2) GENERATED ALWAYS AS (qt * vl_unit) STORED
);

CREATE TABLE IF NOT EXISTS nota_fiscal (
    cd_nf SERIAL PRIMARY KEY,
    cd_venda INTEGER REFERENCES venda(cd_venda),
    nr_nf VARCHAR(50),
    dt_emissao TIMESTAMP,
    vl_total NUMERIC(14,2),
    chave_nf VARCHAR(100)
);

-- Compras
CREATE TABLE IF NOT EXISTS compra (
    cd_compra SERIAL PRIMARY KEY,
    cd_fornecedor INTEGER REFERENCES glb_pessoa(cd_pessoa),
    dt_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vl_total NUMERIC(14,2),
    situacao VARCHAR(50),
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa)
);

CREATE TABLE IF NOT EXISTS compra_item (
    cd_item SERIAL PRIMARY KEY,
    cd_compra INTEGER NOT NULL REFERENCES compra(cd_compra) ON DELETE CASCADE,
    cd_produto INTEGER REFERENCES produto(cd_produto),
    qt NUMERIC(14,4) NOT NULL,
    vl_unit NUMERIC(14,4) DEFAULT 0
);

-- Financeiro
CREATE TABLE IF NOT EXISTS contas_receber (
    cd_receber SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    dt_vencimento DATE,
    vl_parcela NUMERIC(14,2),
    dt_pagamento DATE,
    forma_pagto VARCHAR(100),
    situacao VARCHAR(50),
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa)
);

CREATE TABLE IF NOT EXISTS pagamentos (
    cd_pagto SERIAL PRIMARY KEY,
    referencia_id INTEGER,
    tipo_referencia VARCHAR(50),
    dt_pagto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vl_pagto NUMERIC(14,2),
    meio_pagto VARCHAR(100)
);

-- Questionários / Pesquisas
CREATE TABLE IF NOT EXISTS glb_questionario (
    cd_questionario SERIAL PRIMARY KEY,
    nm_questionario VARCHAR(200),
    ds_questionario TEXT,
    fg_ativo CHAR(1) DEFAULT 'S'
);

CREATE TABLE IF NOT EXISTS glb_questionario_pergunta (
    cd_pergunta SERIAL PRIMARY KEY,
    cd_questionario INTEGER REFERENCES glb_questionario(cd_questionario),
    ds_pergunta TEXT,
    nr_ordem INTEGER
);

CREATE TABLE IF NOT EXISTS glb_questionario_resposta (
    cd_resposta SERIAL PRIMARY KEY,
    cd_pergunta INTEGER REFERENCES glb_questionario_pergunta(cd_pergunta),
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    ds_resposta TEXT,
    dt_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Auxiliares para relatórios e auditoria
CREATE TABLE IF NOT EXISTS log_acesso (
    id SERIAL PRIMARY KEY,
    cd_usuario INTEGER REFERENCES sysapp_config_user(cd_usuario),
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa),
    rota VARCHAR(300),
    dt_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(100),
    status INTEGER
);

CREATE TABLE IF NOT EXISTS relatorio_snapshot (
    id SERIAL PRIMARY KEY,
    nome_relatorio VARCHAR(200),
    params_hash VARCHAR(100),
    dt_geracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    arquivo_path VARCHAR(1000),
    resumo_dados JSONB
);

CREATE TABLE IF NOT EXISTS config_relatorio (
    id SERIAL PRIMARY KEY,
    nome_relatorio VARCHAR(200) UNIQUE,
    parametros_default JSONB,
    visibilidade VARCHAR(50)
);

-- Índices recomendados
CREATE INDEX IF NOT EXISTS idx_glb_pessoa_cpf ON glb_pessoa(nr_cpf_cnpj);
CREATE INDEX IF NOT EXISTS idx_produto_sku ON produto(sku);
CREATE INDEX IF NOT EXISTS idx_venda_dt ON venda(dt_venda);
CREATE INDEX IF NOT EXISTS idx_contas_receber_venc ON contas_receber(dt_vencimento);
CREATE INDEX IF NOT EXISTS idx_estoque_produto ON estoque_movimento(cd_produto);

-- Fim do DDL
