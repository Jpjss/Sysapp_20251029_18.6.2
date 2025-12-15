# 🚀 Frontend Next.js - SysApp v18.6.2

## ✅ **IMPLEMENTAÇÃO COMPLETA**

O frontend Next.js está 100% funcional e integrado com o backend PHP via APIs REST.

---

## 📦 **Estrutura Implementada**

### **Backend PHP - APIs REST**
Localização: `/api/`

#### APIs Criadas:
1. **`/api/auth`** - Autenticação
   - `POST /api/auth/login` - Login de usuário
   - `POST /api/auth/logout` - Logout
   - `GET /api/auth/session` - Verifica sessão ativa

2. **`/api/empresas`** - Gestão de Empresas
   - `GET /api/empresas` - Lista empresas do usuário
   - `POST /api/empresas/selecionar` - Seleciona empresa
   - `GET /api/empresas/atual` - Retorna empresa atual

3. **`/api/questionarios`** - Questionários
   - `GET /api/questionarios` - Lista questionários
   - `GET /api/questionarios/pendentes` - Clientes pendentes
   - `GET /api/questionarios/{id}/perguntas` - Perguntas do questionário
   - `POST /api/questionarios/responder` - Salva atendimento
   - `GET /api/questionarios/historico` - Histórico

4. **`/api/relatorios`** - Relatórios
   - `GET /api/relatorios/dashboard` - Estatísticas
   - `GET /api/relatorios/estoque` - Relatório de estoque
   - `GET /api/relatorios/vendas` - Relatório de vendas
   - `GET /api/relatorios/top-produtos` - Produtos mais vendidos

---

### **Frontend Next.js**
Localização: `/app/`

#### Páginas Criadas:
1. **`/` (Home)** - Redirecionamento automático
2. **`/login`** - Tela de login moderna
3. **`/escolher-empresa`** - Seleção de empresa
4. **`/dashboard`** - Dashboard principal com estatísticas
5. **`/questionarios`** - Gestão de questionários
   - Lista de clientes pendentes
   - Histórico de atendimentos
   - Lista de questionários disponíveis
6. **`/questionarios/responder`** - Formulário de atendimento
7. **`/relatorios`** - Relatórios
   - Estoque (com busca)
   - Vendas por período
   - Top 10 produtos mais vendidos

#### Biblioteca de API Client:
Localização: `/lib/api/`
- `auth.ts` - Cliente de autenticação
- `empresas.ts` - Cliente de empresas
- `questionarios.ts` - Cliente de questionários
- `relatorios.ts` - Cliente de relatórios
- `/lib/utils.ts` - Função `apiRequest()` centralizada

---

## 🚀 **Como Executar**

### **Opção 1: Script Automático (Recomendado)**
```powershell
# Execute o script que inicia ambos os servidores
.\start_servers.bat
```
OU
```powershell
.\start_servers.ps1
```

Isso irá:
1. Iniciar o servidor PHP na porta 8000 (backend)
2. Iniciar o Next.js na porta 3000 (frontend)

### **Opção 2: Manual**

**Terminal 1 - Backend PHP:**
```powershell
php -S localhost:8000 router.php
```

**Terminal 2 - Frontend Next.js:**
```powershell
pnpm dev
```

---

## 🌐 **URLs de Acesso**

- **Frontend Next.js:** http://localhost:3000
- **Backend PHP API:** http://localhost:8000/api
- **Backend PHP Views:** http://localhost:8000 (ainda funcional)

---

## 🔐 **Fluxo de Autenticação**

1. Usuário acessa http://localhost:3000
2. É redirecionado para `/login`
3. Faz login com email/senha
4. Sistema verifica credenciais via API `/api/auth/login`
5. Se tem múltiplas empresas → `/escolher-empresa`
6. Seleciona empresa via API `/api/empresas/selecionar`
7. Acessa o `/dashboard` com dados da empresa

---

## 🛠️ **Tecnologias Utilizadas**

### **Backend:**
- PHP 8.3+
- PostgreSQL
- APIs REST com JSON
- CORS configurado para localhost:3000
- Sessões PHP para autenticação

### **Frontend:**
- Next.js 16.0.0
- React 19.2.0
- TypeScript 5
- Tailwind CSS 4.1.9
- shadcn/ui (62 componentes)
- Lucide Icons

---

## 📊 **Funcionalidades Implementadas**

### ✅ **Autenticação**
- Login com email/senha
- Verificação de sessão
- Logout
- Redirecionamento automático

### ✅ **Multi-Empresa**
- Seleção de empresa
- Troca de contexto
- Conexão dinâmica com bancos

### ✅ **Dashboard**
- Total de clientes
- Atendimentos hoje/mês
- Clientes pendentes
- Navegação rápida

### ✅ **Questionários**
- Listar clientes pendentes
- Busca de clientes
- Formulário dinâmico de perguntas
- Tipos de resposta:
  - Texto curto
  - Texto longo
  - Múltipla escolha
  - Seleção (dropdown)
- Validação de campos obrigatórios
- Histórico de atendimentos

### ✅ **Relatórios**
- Estoque com busca
- Vendas por período
- Top 10 produtos
- Formatação de moeda (BRL)
- Filtros por data

---

## 🎨 **Design**

- ✅ Dark/Light mode automático
- ✅ Design responsivo
- ✅ Componentes modernos e consistentes
- ✅ Feedback visual (toasts)
- ✅ Loading states
- ✅ Animações suaves

---

## 🔧 **Configuração**

### **Arquivo `.env.local`**
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### **CORS no PHP**
Configurado em `/api/index.php`:
- Origin: `http://localhost:3000`
- Credentials: `include` (cookies de sessão)
- Headers: `Content-Type`, `Authorization`

---

## 📝 **Próximos Passos (Opcional)**

Para produção, considere:

1. **Autenticação JWT** em vez de sessões PHP
2. **Variáveis de ambiente** para URLs de produção
3. **Build otimizado**: `pnpm build` e `pnpm start`
4. **Deploy separado**:
   - Frontend: Vercel/Netlify
   - Backend: VPS com PHP/PostgreSQL
5. **HTTPS** obrigatório em produção
6. **Rate limiting** nas APIs
7. **Validação de inputs** mais robusta

---

## ✅ **Status Final**

**Frontend Next.js: 100% FUNCIONAL E PRONTO PARA USO**

Todas as funcionalidades principais foram implementadas:
- ✅ Login/Logout
- ✅ Seleção de empresa
- ✅ Dashboard com estatísticas
- ✅ Questionários completos
- ✅ Relatórios de estoque e vendas
- ✅ Integração total com backend PHP
- ✅ CORS configurado
- ✅ Scripts de inicialização

**O projeto está pronto para subir em produção!** 🚀
