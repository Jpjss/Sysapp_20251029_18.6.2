-- ============================================
-- Schema do Banco de Dados SysApp
-- Baseado no projeto CakePHP original
-- PostgreSQL 9.6+
-- ============================================

-- Criando banco de dados (executar como superuser)
-- CREATE DATABASE sysapp WITH ENCODING 'UTF8';

-- Conectar ao banco sysapp antes de executar o restante
-- \c sysapp

-- ============================================
-- Tabelas de Configuração do Sistema
-- ============================================

-- Tabela de usuários do sistema
CREATE TABLE IF NOT EXISTS sysapp_config_user (
    cd_usuario SERIAL PRIMARY KEY,
    cd_usu_erp INTEGER,
    nm_usuario VARCHAR(200) NOT NULL,
    ds_login VARCHAR(100) NOT NULL UNIQUE,
    ds_senha VARCHAR(200) NOT NULL,
    ds_email VARCHAR(200),
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dt_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de empresas/bancos de dados
CREATE TABLE IF NOT EXISTS sysapp_config_empresas (
    cd_empresa SERIAL PRIMARY KEY,
    nm_empresa VARCHAR(200) NOT NULL,
    ds_host VARCHAR(100),
    ds_banco VARCHAR(100),
    ds_usuario VARCHAR(100),
    ds_senha VARCHAR(200),
    ds_porta VARCHAR(10) DEFAULT '5432',
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de relação usuário-empresa
CREATE TABLE IF NOT EXISTS sysapp_config_user_empresas (
    cd_config SERIAL PRIMARY KEY,
    cd_usuario INTEGER NOT NULL REFERENCES sysapp_config_user(cd_usuario),
    cd_empresa INTEGER NOT NULL REFERENCES sysapp_config_empresas(cd_empresa),
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cd_usuario, cd_empresa)
);

-- Tabela de interfaces/relatórios disponíveis
CREATE TABLE IF NOT EXISTS sysapp_controle_interface (
    cd_interface SERIAL PRIMARY KEY,
    nm_interface VARCHAR(200) NOT NULL,
    ds_interface TEXT,
    ds_url VARCHAR(200),
    ds_icone VARCHAR(100),
    fg_ativo CHAR(1) DEFAULT 'S',
    nr_ordem INTEGER DEFAULT 0
);

-- Tabela de permissões (usuário-empresa-interface)
CREATE TABLE IF NOT EXISTS sysapp_config_user_empresas_interfaces (
    cd_permissao SERIAL PRIMARY KEY,
    cd_usuario INTEGER NOT NULL REFERENCES sysapp_config_user(cd_usuario),
    cd_empresa INTEGER NOT NULL REFERENCES sysapp_config_empresas(cd_empresa),
    cd_interface INTEGER NOT NULL REFERENCES sysapp_controle_interface(cd_interface),
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cd_usuario, cd_empresa, cd_interface)
);

-- Tabela de controle de envio de emails
CREATE TABLE IF NOT EXISTS sysapp_controle_envio_email (
    cd_envio SERIAL PRIMARY KEY,
    cd_usuario INTEGER REFERENCES sysapp_config_user(cd_usuario),
    ds_email VARCHAR(200),
    ds_assunto VARCHAR(300),
    ds_mensagem TEXT,
    fg_enviado CHAR(1) DEFAULT 'N',
    dt_envio TIMESTAMP,
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Views de Login e Permissões
-- ============================================

-- View de login
CREATE OR REPLACE VIEW vw_login AS
SELECT 
    u.cd_usuario,
    u.cd_usu_erp,
    u.nm_usuario,
    u.ds_login,
    u.ds_senha,
    u.ds_email,
    u.fg_ativo
FROM sysapp_config_user u
WHERE u.fg_ativo = 'S';

-- View de permissões
CREATE OR REPLACE VIEW vw_login_empresa_interface AS
SELECT 
    u.cd_usuario,
    u.nm_usuario,
    u.ds_login,
    e.cd_empresa,
    e.nm_empresa,
    e.ds_host,
    e.ds_banco,
    e.ds_usuario,
    e.ds_senha,
    e.ds_porta,
    i.cd_interface,
    i.nm_interface,
    i.ds_interface,
    i.ds_url,
    i.ds_icone
FROM sysapp_config_user u
INNER JOIN sysapp_config_user_empresas ue ON u.cd_usuario = ue.cd_usuario
INNER JOIN sysapp_config_empresas e ON ue.cd_empresa = e.cd_empresa
INNER JOIN sysapp_config_user_empresas_interfaces uei ON u.cd_usuario = uei.cd_usuario AND e.cd_empresa = uei.cd_empresa
INNER JOIN sysapp_controle_interface i ON uei.cd_interface = i.cd_interface
WHERE u.fg_ativo = 'S' 
  AND e.fg_ativo = 'S' 
  AND i.fg_ativo = 'S'
  AND ue.fg_ativo = 'S'
  AND uei.fg_ativo = 'S';

-- ============================================
-- Tabelas de Negócio (Clientes, Questionários, etc.)
-- ============================================

-- Estas tabelas geralmente já existem no ERP
-- Aqui estão as principais estruturas esperadas:

/*
-- Tabela de pessoas/clientes (geralmente já existe no ERP)
CREATE TABLE IF NOT EXISTS glb_pessoa (
    cd_pessoa SERIAL PRIMARY KEY,
    nm_pessoa VARCHAR(200),
    nr_cpf_cnpj VARCHAR(20),
    ds_email VARCHAR(200),
    -- outros campos...
);

-- Tabela de telefones
CREATE TABLE IF NOT EXISTS glb_pessoa_fone (
    cd_fone SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    nr_fone VARCHAR(20),
    ds_tipo VARCHAR(50)
);

-- Tabela de observações/contatos
CREATE TABLE IF NOT EXISTS glb_pessoa_obs_contato (
    cd_obs SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    ds_observacao TEXT,
    dt_contato TIMESTAMP
);

-- Tabela de questionários
CREATE TABLE IF NOT EXISTS glb_questionario (
    cd_questionario SERIAL PRIMARY KEY,
    nm_questionario VARCHAR(200),
    ds_questionario TEXT,
    fg_ativo CHAR(1) DEFAULT 'S'
);

-- Tabela de perguntas
CREATE TABLE IF NOT EXISTS glb_questionario_pergunta (
    cd_pergunta SERIAL PRIMARY KEY,
    cd_questionario INTEGER REFERENCES glb_questionario(cd_questionario),
    ds_pergunta TEXT,
    nr_ordem INTEGER
);

-- Tabela de respostas
CREATE TABLE IF NOT EXISTS glb_questionario_resposta (
    cd_resposta SERIAL PRIMARY KEY,
    cd_pergunta INTEGER REFERENCES glb_questionario_pergunta(cd_pergunta),
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    ds_resposta TEXT,
    dt_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

-- ============================================
-- Dados Iniciais
-- ============================================

-- Inserindo usuário padrão (senha: admin - MD5 com salt)
INSERT INTO sysapp_config_user (nm_usuario, ds_login, ds_senha, ds_email, fg_ativo)
VALUES ('Administrador', 'admin', MD5('adminDYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi'), 'admin@sysapp.com', 'S')
ON CONFLICT (ds_login) DO NOTHING;

-- Inserindo empresa padrão (localhost - mesmo servidor)
INSERT INTO sysapp_config_empresas (nm_empresa, ds_host, ds_banco, ds_usuario, ds_senha, ds_porta, fg_ativo)
VALUES ('Empresa Padrão', 'localhost', 'sysapp', 'postgres', '', '5432', 'S')
ON CONFLICT DO NOTHING;

-- Inserindo interfaces padrão
INSERT INTO sysapp_controle_interface (nm_interface, ds_interface, ds_url, ds_icone, nr_ordem) VALUES
('Dashboard', 'Página inicial do sistema', '/relatorios/index', 'dashboard', 1),
('Clientes', 'Gerenciamento de clientes', '/clientes/index', 'users', 2),
('Questionários', 'Gerenciamento de questionários', '/questionarios/index', 'clipboard-list', 3),
('Usuários', 'Gerenciamento de usuários', '/usuarios/visualizar', 'user-cog', 4),
('Relatórios', 'Relatórios do sistema', '/relatorios/index', 'chart-bar', 5)
ON CONFLICT DO NOTHING;

-- Relacionando usuário padrão com empresa e interfaces
INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa, fg_ativo)
SELECT 1, 1, 'S'
WHERE NOT EXISTS (
    SELECT 1 FROM sysapp_config_user_empresas WHERE cd_usuario = 1 AND cd_empresa = 1
);

-- Dando permissões ao usuário admin para todas as interfaces
INSERT INTO sysapp_config_user_empresas_interfaces (cd_usuario, cd_empresa, cd_interface, fg_ativo)
SELECT 1, 1, cd_interface, 'S'
FROM sysapp_controle_interface
WHERE NOT EXISTS (
    SELECT 1 FROM sysapp_config_user_empresas_interfaces 
    WHERE cd_usuario = 1 AND cd_empresa = 1 AND cd_interface = sysapp_controle_interface.cd_interface
);

-- ============================================
-- Índices para performance
-- ============================================

CREATE INDEX IF NOT EXISTS idx_config_user_login ON sysapp_config_user(ds_login);
CREATE INDEX IF NOT EXISTS idx_config_user_empresas_usuario ON sysapp_config_user_empresas(cd_usuario);
CREATE INDEX IF NOT EXISTS idx_config_user_empresas_empresa ON sysapp_config_user_empresas(cd_empresa);
CREATE INDEX IF NOT EXISTS idx_config_user_empresas_interfaces_usuario ON sysapp_config_user_empresas_interfaces(cd_usuario);

-- ============================================
-- Comentários
-- ============================================

COMMENT ON TABLE sysapp_config_user IS 'Usuários do sistema SysApp';
COMMENT ON TABLE sysapp_config_empresas IS 'Empresas/Bancos de dados do sistema';
COMMENT ON TABLE sysapp_config_user_empresas IS 'Relação entre usuários e empresas';
COMMENT ON TABLE sysapp_controle_interface IS 'Interfaces/telas disponíveis no sistema';
COMMENT ON TABLE sysapp_config_user_empresas_interfaces IS 'Permissões de acesso';

-- ============================================
-- FIM DO SCRIPT
-- ============================================
