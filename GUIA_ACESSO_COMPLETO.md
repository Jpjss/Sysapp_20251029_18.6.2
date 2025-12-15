# 🔐 GUIA COMPLETO DE ACESSO - SYSAPP 2025

## ✅ STATUS DO SISTEMA

### Servidores Rodando:
- **Backend API (PHP)**: http://localhost:8000/api
- **Frontend (Next.js)**: http://localhost:3000

### Banco de Dados:
- **Servidor**: localhost:5432
- **Banco Principal**: `sysapp` (controle de usuários e empresas)
- **Usuário**: postgres
- **Senha**: postgres

## 🔑 CREDENCIAIS DE ACESSO

### Para o FRONTEND (Next.js):
**URL**: http://localhost:3000/login

**Opção 1:**
- Login: `admin`
- Senha: `admin`

**Opção 2:**
- Email: `diaazze@sys.io`
- Senha: `c6WUh^xH3H5gH64r2iOIPtHXHVAvRA`

### Para o BACKEND PHP Original:
**IMPORTANTE**: O backend PHP antigo (CakePHP) está desatualizado e usa tabelas que não existem mais no banco atual.

**Solução**: Use o FRONTEND Next.js que está integrado com a nova API REST.

## 📊 EMPRESAS CADASTRADAS

### 1. Empresa Padrão
- **ID**: 1
- **Nome**: Empresa Padrão
- **Banco**: sysapp
- **Host**: localhost
- **Porta**: 5432
- **Usuário**: postgres
- **Senha**: postgres
- ⚠️ **Nota**: Este banco não tem dados comerciais (clientes/vendas)

### 2. Agape  
- **ID**: 2
- **Nome**: Agape
- **Host**: 168.138.144.4
- **Banco**: bd_agape_israel
- **Porta**: 5432
- ⚠️ **Nota**: Banco remoto - pode estar inacessível

## 🚀 COMO USAR O SISTEMA

### Passo 1: Login
1. Acesse: http://localhost:3000/login
2. Use: `admin` / `admin`
3. Clique em "Entrar"

### Passo 2: Escolher Empresa
1. Você será redirecionado para: http://localhost:3000/escolher-empresa
2. Selecione a empresa "Empresa Padrão" ou outra disponível
3. Clique em "Selecionar"

### Passo 3: Acessar Dashboard
1. Após selecionar empresa, será redirecionado para: http://localhost:3000/dashboard
2. Verá estatísticas em tempo real do banco selecionado

## ⚙️ ESTRUTURA DO BANCO

### Tabela: `sysapp_config_user` (Usuários)
```sql
- cd_usuario (ID)
- nm_usuario (Nome)
- ds_login (Login)
- ds_senha (Senha em texto plano)
- ds_email (Email)
- fg_ativo (S/N)
```

### Tabela: `sysapp_config_empresas` (Empresas)
```sql
- cd_empresa (ID)
- nm_empresa (Nome da empresa)
- ds_host (Host do banco)
- ds_banco (Nome do banco)
- ds_usuario (Usuário do banco)
- ds_senha (Senha do banco)
- ds_porta (Porta)
- fg_ativo (S/N)
```

## 🔧 RESOLUÇÃO DE PROBLEMAS

### Problema: "Não consigo logar no backend PHP"
**Solução**: O backend PHP antigo não está compatível. Use o frontend Next.js.

### Problema: "Dashboard não mostra dados"
**Solução**: 
1. Certifique-se de ter selecionado uma empresa
2. Verifique se a empresa tem banco com dados
3. Empresa Padrão (sysapp) não tem dados comerciais

### Problema: "Erro ao selecionar empresa"
**Solução**:
1. Verifique se o servidor PHP está rodando (localhost:8000)
2. Teste: http://localhost:8000/api/auth/session

## 📝 COMANDOS ÚTEIS

### Iniciar Servidor PHP:
```powershell
php -S localhost:8000 router.php
```

### Iniciar Frontend Next.js:
```powershell
npm run dev
```

### Testar Login API:
```powershell
$body = @{login='admin'; senha='admin'} | ConvertTo-Json
Invoke-WebRequest -Uri 'http://localhost:8000/api/auth/login' -Method POST -Body $body -ContentType 'application/json'
```

### Verificar Sessão:
```powershell
Invoke-WebRequest -Uri 'http://localhost:8000/api/auth/session' -Method GET
```

## ✨ FUNCIONALIDADES IMPLEMENTADAS

✅ Sistema de login com autenticação
✅ Seleção de empresas/bancos
✅ Dashboard com estatísticas em tempo real
✅ Detecção automática de tipo de banco (Questionários vs Comercial)
✅ API REST completa (auth, empresas, relatórios, questionários)
✅ Layout moderno idêntico ao backend PHP
✅ Dados reais do banco PostgreSQL
✅ Suporte a múltiplas empresas/bancos

## 🎯 PRÓXIMOS PASSOS

1. **Cadastrar Empresa com Dados Reais**:
   - Ir em: Usuários > Adicionar Database
   - Cadastrar banco com dados comerciais ou questionários

2. **Popular Dados de Teste**:
   - Criar clientes, questionários, vendas no banco

3. **Migrar Backend PHP Antigo** (opcional):
   - Atualizar tabelas antigas para nova estrutura
   - Ou usar apenas o frontend Next.js com API REST
