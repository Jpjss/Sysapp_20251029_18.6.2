# 🚀 Sistema de Configuração Automática de Empresas

## 📋 O que foi implementado

Criei um sistema completo que **automatiza** a configuração de bancos de dados de clientes no SysApp. As mesmas correções aplicadas na empresa **Propaso** agora são aplicadas automaticamente em todas as empresas.

---

## ✨ Funcionalidades

### 1. **Configuração Automática ao Cadastrar Nova Empresa**
Quando você cadastra uma nova empresa em `/usuarios/adiciona_database`, o sistema **automaticamente**:

✅ Cria tabela `sysapp_interfaces` no banco do cliente  
✅ Insere as 6 interfaces padrão (Dashboard, Relatórios, Clientes, etc.)  
✅ Cria views necessárias para relatórios (`vw_login`, `vw_clientes_simples`)  
✅ Detecta automaticamente campos disponíveis (nm_fant, cpf_cgc, etc.)  
✅ Cria índices para melhorar performance de consultas  
✅ Verifica estrutura do banco e conta registros  

### 2. **Aplicar em Todas as Empresas Existentes**
Use o script `/aplicar_configuracoes_todas_empresas.php` para aplicar as configurações em **todas as empresas já cadastradas**.

### 3. **Teste Individual**
Use `/testar_configuracao_automatica.php` para testar em **uma empresa** antes de aplicar em todas.

---

## 🔧 Arquivos Criados/Modificados

### Novos Arquivos:
1. **`/core/DatabaseSetup.php`** - Classe principal com toda a lógica de configuração
2. **`/aplicar_configuracoes_todas_empresas.php`** - Script para aplicar em empresas existentes
3. **`/testar_configuracao_automatica.php`** - Script de teste individual

### Modificados:
1. **`/controllers/UsuariosController.php`** - Adicionada chamada automática ao DatabaseSetup
2. **`/config/config.php`** - Adicionado autoloader para classes do core
3. **`/models/Relatorio.php`** - Corrigidas queries para usar tabelas corretas
4. **`/controllers/RelatoriosController.php`** - Adicionado fallback quando não há dados

---

## 📦 O que o DatabaseSetup faz

### 1. **Interfaces do SysApp**
```sql
CREATE TABLE sysapp_interfaces (
    cd_interface INTEGER PRIMARY KEY,
    nm_interface VARCHAR(100) NOT NULL,
    ds_interface TEXT,
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

INSERT INTO sysapp_interfaces VALUES
    (1, 'Dashboard', 'Dashboard principal com estatísticas'),
    (2, 'Relatórios', 'Acesso a relatórios e análises'),
    (3, 'Clientes', 'Gerenciamento de clientes'),
    (4, 'Questionários', 'Questionários e atendimentos'),
    (5, 'Usuários', 'Gerenciamento de usuários'),
    (6, 'Configurações', 'Configurações do sistema')
```

### 2. **Views para Compatibilidade**
```sql
-- View de login
CREATE OR REPLACE VIEW vw_login AS
    SELECT cd_usuario, nm_usuario as nome_usuario, ...
    FROM sysapp_config_user
    WHERE fg_ativo = 'S'

-- View de clientes (detecta campos automaticamente)
CREATE OR REPLACE VIEW vw_clientes_simples AS
    SELECT cd_pessoa, nm_fant as nome_cliente, ...
    FROM glb_pessoa
    WHERE fg_ativo = 'S'
```

### 3. **Índices de Performance**
```sql
CREATE INDEX idx_vendas_data ON dm_orcamento_vendas_consolidadas(dt_emi_pedido);
CREATE INDEX idx_vendas_cliente ON dm_orcamento_vendas_consolidadas(cd_pessoa);
CREATE INDEX idx_produto_marca ON dm_produto(cd_marca);
```

### 4. **Verificação de Estrutura**
- Verifica existência de tabelas essenciais
- Conta registros em cada tabela
- Detecta campos disponíveis automaticamente
- Adapta queries conforme estrutura encontrada

---

## 🎯 Como Usar

### Para Empresas Existentes:

1. **Teste em uma empresa primeiro:**
   ```
   http://localhost:8000/testar_configuracao_automatica.php
   ```

2. **Se o teste passou, aplique em todas:**
   ```
   http://localhost:8000/aplicar_configuracoes_todas_empresas.php
   ```

3. **Confirme a execução** (tem proteção para evitar execução acidental)

### Para Novas Empresas:

1. Cadastre normalmente em `/usuarios/adiciona_database`
2. O sistema **aplica automaticamente** todas as configurações
3. Pronto! A empresa já está 100% configurada

---

## 🔍 Detecção Inteligente

O sistema detecta automaticamente variações nos nomes de campos:

**Nome de Pessoa:**
- `nm_fant` (Propaso)
- `nm_fantasia`
- `nm_pessoa` (padrão)

**CPF/CNPJ:**
- `cpf_cgc` (Propaso)
- `nr_cpf_cnpj`
- `cpf_cnpj`

**Adaptação de Queries:**
O sistema tenta primeiro com a tabela principal (`dm_orcamento_vendas_consolidadas`) e faz fallback para outras (`ped_vd`) se necessário.

---

## ✅ Benefícios

1. **Economia de Tempo** - Não precisa configurar manualmente cada empresa
2. **Padronização** - Todas as empresas têm a mesma estrutura
3. **Menos Erros** - Processo automatizado elimina erros humanos
4. **Fácil Manutenção** - Alterações em um lugar refletem em todos
5. **Compatibilidade** - Detecta e adapta automaticamente às diferenças

---

## 🛠️ Manutenção Futura

Para adicionar novas configurações no futuro:

1. Edite `/core/DatabaseSetup.php`
2. Adicione o código no método apropriado
3. Execute `aplicar_configuracoes_todas_empresas.php` para atualizar empresas existentes
4. Novas empresas receberão automaticamente

---

## 📊 Log Detalhado

O sistema gera logs completos de tudo que faz:

```
✅ Conectado ao banco bd_propasso
--- Configurando Interfaces do SysApp ---
  ✅ Tabela sysapp_interfaces criada
  ✅ 6 interfaces configuradas
--- Configurando Views de Relatórios ---
  ✅ View vw_login criada/atualizada
  ✅ View vw_clientes_simples criada/atualizada
--- Verificando Estrutura do Banco ---
  ✅ Cadastro de pessoas/clientes (glb_pessoa): 1,234 registros
  ✅ Cadastro de produtos (dm_produto): 5,678 registros
  ✅ Vendas consolidadas: 9,012 registros
--- Otimizando Performance ---
  ✅ Índice para consultas por data
  ✅ Índice para consultas por cliente
  ✅ Índice para relatórios por marca
```

---

## 🔐 Segurança

- Usa as credenciais já cadastradas no SysApp
- Descriptografa senhas automaticamente
- Testa conexão antes de aplicar mudanças
- Não expõe senhas nos logs
- Proteção contra execução acidental

---

## 🚨 Importante

- **Sempre teste primeiro** em uma empresa antes de aplicar em todas
- Faça **backup** dos bancos antes de executar em produção
- Verifique os **logs** para identificar possíveis problemas
- O script é **idempotente** - pode executar múltiplas vezes sem problemas

---

## 📞 Suporte

Se encontrar algum problema:

1. Verifique os logs no script de teste
2. Confirme que as credenciais do banco estão corretas
3. Teste a conexão manualmente com as credenciais
4. Verifique se o PostgreSQL está acessível

---

**Criado em:** Janeiro 2026  
**Versão:** 1.0  
**Autor:** Sistema SysApp
