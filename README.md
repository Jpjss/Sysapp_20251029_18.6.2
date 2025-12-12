# SysApp v18.6.2 - Sistema de Gestão Empresarial Multi-Tenant

[![PHP](https://img.shields.io/badge/PHP-8.2.12-777BB4?logo=php&logoColor=white)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-316192?logo=postgresql&logoColor=white)](https://postgresql.org)
[![Next.js](https://img.shields.io/badge/Next.js-16.0.0-000000?logo=next.js&logoColor=white)](https://nextjs.org)

## 1. Visão Geral

O SysApp é um sistema de gestão empresarial robusto e escalável, desenvolvido com arquitetura multi-tenant para suportar múltiplas empresas com isolamento de dados. Combina um backend PHP customizado com frontend moderno em Next.js/React, oferecendo funcionalidades completas de CRM, gestão de questionários, análise de clientes e **correção automática de XMLs de NFe**.

### Características Principais

- **Multi-Tenant Architecture**: Isolamento completo de dados por empresa com bancos PostgreSQL dedicados
- **Sistema de Permissões Granular**: Controle por usuário/empresa/interface
- **Correção de XML NFe**: Ajuste automático de divergências em notas fiscais eletrônicas (funcionalidade implementada em dezembro/2025)
- **CRM Integrado**: Gestão completa de clientes, contatos e histórico
- **Sistema de Questionários**: Questionários customizáveis com parametrização avançada
- **Integração ERP**: Conexão direta com sistemas ERP via PostgreSQL
- **Arquitetura Híbrida**: PHP 8.2 + PostgreSQL 18 + Next.js 16 + React 19

## 2. Arquitetura Técnica

### 2.1 Padrão Multi-Tenant

```
┌─────────────────────────────────────────────────────┐
│                   Cliente (Browser)                  │
└────────────────────┬────────────────────────────────┘
                     │
           ┌─────────┴─────────┐
           │                   │
    ┌──────▼──────┐    ┌──────▼──────┐
    │  Next.js    │    │  PHP 8.2    │
    │  Frontend   │    │  MVC Core   │
    │  (Port 3000)│    │  (Port 8000)│
    └──────┬──────┘    └──────┬──────┘
           │                   │
           │         ┌─────────┴─────────┐
           │         │  Router.php       │
           │         │  Custom MVC       │
           │         └────┬──────────────┘
           │              │
           └──────────────┘
                          │
      ┌───────────────────┴───────────────────┐
      │                                       │
┌─────▼─────┐                        ┌───────▼────────┐
│ PostgreSQL│                        │ PostgreSQL     │
│  sysapp   │◄───────────────────────│ empresa_1      │
│  (Master) │   Dynamic Switching    │ empresa_2      │
└───────────┘                        │ empresa_n      │
                                     └────────────────┘
```

### 2.2 Componentes Principais

*   **Backend (PHP 8.2.12):** Framework MVC customizado com roteamento dinâmico, gerenciamento de sessões e conexão multi-database via `ConnectionManager`. Utiliza PostgreSQL nativo (`pg_connect`, `pg_query_params`) para operações de banco de dados.

*   **Frontend (Next.js 16.0.0):** Aplicação React 19 com Server-Side Rendering (SSR), TypeScript e Tailwind CSS 4. Biblioteca completa de componentes Radix UI para interface moderna e acessível.

*   **Banco de Dados (PostgreSQL 18):** Arquitetura multi-tenant com banco master (`sysapp`) para controle de usuários/empresas/permissões e bancos isolados por empresa para dados operacionais.

*   **Sistema de Rotas:** `router.php` customizado para reescrita de URLs, servindo tanto arquivos estáticos quanto requisições dinâmicas do MVC.

## 3. Stack Tecnológica Completa

### Backend
| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **PHP** | 8.2.12 | Core do backend, MVC customizado |
| **PostgreSQL** | 18.x | SGBD principal com suporte multi-tenant |
| **SimpleXML** | Built-in | Processamento de XML NFe |
| **DOMDocument** | Built-in | Manipulação avançada de XML |
| **ZipArchive** | Built-in | Compactação de arquivos processados |
| **OpenSSL** | Built-in | Criptografia de credenciais de banco |

### Frontend
| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **Next.js** | 16.0.0 | Framework React com SSR/SSG |
| **React** | 19.2.0 | Biblioteca de UI declarativa |
| **TypeScript** | 5.x | Superset tipado do JavaScript |
| **Tailwind CSS** | 4.1.9 | Framework CSS utilitário |
| **Radix UI** | Latest | Componentes acessíveis headless |
| **Lucide React** | 0.454.0 | Biblioteca de ícones SVG |
| **React Hook Form** | 7.54.2 | Gerenciamento de formulários |
| **Zod** | 3.24.1 | Validação e parsing de schemas |

### DevOps & Ferramentas
*   **XAMPP**: Ambiente de desenvolvimento local (PHP 8.2.12 + Apache)
*   **PowerShell**: Scripts de automação de banco de dados (`.ps1`)
*   **pnpm**: Gerenciador de pacotes Node.js (performance otimizada)
*   **Git**: Controle de versão distribuído

## 4. Estrutura de Diretórios Detalhada

```
Sysapp_20251029_18.6.2/
│
├── app/                          # Next.js Application (Frontend Moderno)
│   ├── admin/                    # Painel administrativo React
│   ├── globals.css               # Estilos globais com Tailwind
│   ├── layout.tsx                # Layout raiz do Next.js
│   └── page.tsx                  # Home page (SSR)
│
├── components/                   # Componentes React Reutilizáveis
│   ├── layout/                   # Header, Footer, Sidebar
│   ├── ui/                       # Radix UI wrappers (Button, Dialog, etc)
│   ├── theme-provider.tsx        # Context API para tema dark/light
│   └── theme-toggle.tsx          # Toggle de tema
│
├── config/                       # Configurações PHP
│   ├── config.php                # Constantes (DB, SECURITY_SALT, BASE_URL)
│   └── database.php              # Singleton de conexão PostgreSQL
│
├── Controller/                   # Controllers CakePHP (Legado)
│   ├── AppController.php         # Controller base CakePHP 2.x
│   ├── RelatoriosController.php  # Relatórios com multi-database
│   └── GlbQuestionario*.php      # Controllers de questionários
│
├── controllers/                  # Controllers PHP Puro (Moderno)
│   ├── ClientesController.php    # CRUD de clientes com paginação
│   ├── UsuariosController.php    # Autenticação e gestão de usuários
│   ├── XmlController.php         # ⭐ NOVO: Correção de XML NFe
│   └── VendedorController.php    # Gestão de vendedores
│
├── core/                         # Framework MVC Customizado
│   ├── Controller.php            # Controller base PHP puro
│   ├── Router.php                # Sistema de roteamento dinâmico
│   ├── Session.php               # Gerenciamento de sessões
│   └── Security.php              # Criptografia e sanitização
│
├── Model/                        # Models CakePHP (Legado)
│   └── Usuario.php               # Model de usuário com ConnectionManager
│
├── models/                       # Models PHP Puro (Moderno)
│   └── Cliente.php               # Model de cliente com pg_query_params
│
├── View/                         # Views CakePHP (Legado)
│   └── Layouts/                  # Layouts CakePHP
│
├── views/                        # Views PHP Puro (Moderno)
│   ├── layouts/
│   │   └── default.php           # Layout HTML padrão
│   ├── usuarios/
│   │   ├── login.php             # Tela de login
│   │   └── empresa.php           # Seleção de empresa (multi-tenant)
│   ├── clientes/
│   │   └── index.php             # Lista de clientes com filtros
│   └── xml/                      # ⭐ NOVO: Sistema de XML
│       └── index.php             # Interface de upload e correção
│
├── public/                       # Assets Públicos
│   ├── css/style.css             # Estilos customizados
│   ├── js/main.js                # JavaScript principal
│   ├── uploads/                  # ⭐ NOVO: Uploads de usuários
│   │   ├── xml_temp/             # XMLs temporários (processamento)
│   │   └── xml_corrigidos/       # XMLs corrigidos (download)
│   └── test_xmls/                # XMLs de teste (3 cenários)
│
├── lib/                          # Bibliotecas auxiliares
├── hooks/                        # React hooks customizados
├── styles/                       # CSS modules Next.js
│
├── router.php                    # ⭐ Roteador PHP dev server
├── index.php                     # Entry point da aplicação
├── database_schema.sql           # Schema do banco master (sysapp)
├── setup_database.ps1            # ⭐ Setup automático completo
├── vincular_empresas.php         # Script de vinculação usuário-empresa
│
├── package.json                  # Dependências Node.js
├── tsconfig.json                 # Configuração TypeScript
├── next.config.mjs               # Configuração Next.js
├── tailwind.config.ts            # Configuração Tailwind CSS
├── components.json               # Configuração shadcn/ui
│
├── README.md                     # Esta documentação
├── README_TECNICO.md             # ⭐ Documentação técnica completa
└── GUIA_TESTE_XML.md             # ⭐ Guia de testes do sistema XML
```

### Arquivos de Diagnóstico e Teste

*   `test_connection.php`: Validação de conexão PostgreSQL
*   `test_db_connection.php`: Teste avançado de conexões multi-tenant
*   `test_login.php`: Formulário de teste de autenticação
*   `diagnostico_banco.php`: Diagnóstico completo do banco de dados
*   `listar_tabelas_propasso.php`: Lista tabelas do sistema Propasso
*   `verificar_estrutura_*.php`: Validação de schemas de tabelas
*   `debug_empresas.php`: Debug de configurações de empresas

## 5. Configuração do Banco de Dados

### 5.1 Arquitetura Multi-Tenant

O sistema utiliza um modelo **database-per-tenant**, onde:

- **`sysapp` (Master Database)**: Armazena usuários, empresas, permissões e configurações globais
- **`empresa_1`, `empresa_2`, ..., `empresa_n`**: Bancos de dados isolados por empresa com dados operacionais (clientes, vendas, questionários)

### 5.2 Schema do Banco Master

**Tabelas Principais:**

```sql
-- Usuários do sistema
sysapp_config_user (cd_usuario, nome_usuario, login_usuario, senha_usuario, cd_usu_erp, ativo)

-- Empresas cadastradas com credenciais de banco
sysapp_config_empresas (cd_empresa, nome_empresa, hostname_banco, nome_banco, 
                        usuario_banco, senha_banco, porta_banco, ativo)

-- Relação usuário-empresa (multi-tenant)
sysapp_config_user_empresas (cd_usuario, cd_empresa)

-- Interfaces/módulos disponíveis
sysapp_controle_interface (cd_interface, nm_interface, url_interface, ativo)

-- Matriz de permissões
sysapp_config_user_empresas_interfaces (cd_usuario, cd_empresa, cd_interface)
```

### 5.3 Scripts de Configuração

| Script | Descrição |
|--------|-----------|
| `database_schema.sql` | Schema completo do banco master |
| `setup_database.ps1` | **Setup automático completo** (recomendado) |
| `setup_banco_cliente.ps1` | Configuração de banco de empresa específica |
| `criar_banco_cliente_exemplo.ps1` | Criação de banco de exemplo para testes |
| `vincular_propasso.ps1` | Vinculação com sistema Propasso (ERP) |
| `vincular_usuario_banco.ps1` | Vinculação de usuários a bancos |
| `vincular_empresas.php` | Interface web de vinculação |

### 5.4 Setup Rápido

**PowerShell (Administrador):**

```powershell
# 1. Criar banco master
.\setup_database.ps1

# 2. Criar banco de empresa de exemplo
.\criar_banco_cliente_exemplo.ps1

# 3. Vincular usuário ao banco
.\vincular_usuario_banco.ps1
```

**Credenciais Padrão:**
- **Usuário**: admin
- **Senha**: mudar123
- **Banco Master**: sysapp (localhost:5432)

⚠️ **Altere a senha padrão imediatamente após o primeiro acesso!**

## 6. Backend (PHP 8.2.12)

### 6.1 Arquitetura MVC Customizada

O backend utiliza um framework MVC customizado (não é CakePHP completo, mas utiliza alguns componentes):

```php
// Fluxo de Requisição
index.php → Router::parseUrl() → {Controller}Controller::{action}() → Model → View
```

**Componentes Core:**

- **`core/Router.php`**: Roteamento dinâmico baseado em URL (`/controller/action/params`)
- **`core/Controller.php`**: Controller base com métodos de renderização e redirecionamento
- **`core/Session.php`**: Gerenciamento de sessões com namespace `Questionarios`
- **`core/Security.php`**: Criptografia (AES-256-CBC) e sanitização
- **`config/database.php`**: Singleton de conexão PostgreSQL com suporte multi-database

### 6.2 Sistema de Autenticação

**Fluxo Multi-Step:**

```
1. Login (POST /usuarios/login)
   ↓ Valida credenciais via view vw_login
   ↓ Armazena cd_usu, nm_usu, login_usuario na sessão
   
2. Seleção de Empresa (GET/POST /usuarios/empresa)
   ↓ Lista empresas disponíveis para o usuário
   ↓ Armazena credenciais do banco da empresa na sessão
   
3. Acesso ao Sistema
   ↓ Todas as queries usam conexão da empresa selecionada
   ↓ ConnectionManager gerencia troca dinâmica de bancos
```

**Implementação (`controllers/UsuariosController.php`):**

```php
public function login() {
    if ($this->isPost()) {
        $login = $_POST['login_usuario'];
        $senha = $_POST['senha_usuario'];
        
        // Query via prepared statement
        $query = "SELECT * FROM vw_login WHERE login_usuario = $1";
        $result = pg_query_params($conn, $query, [$login]);
        $usuario = pg_fetch_assoc($result);
        
        // Validação SHA1 (legado - migrar para bcrypt)
        if ($usuario && sha1($senha) === $usuario['senha_usuario']) {
            Session::write('Questionarios', [
                'cd_usu' => $usuario['cd_usuario'],
                'nm_usu' => $usuario['nome_usuario'],
                'cd_usu_erp' => $usuario['cd_usu_erp']
            ]);
            $this->redirect('usuarios/empresa');
        }
    }
}
```

### 6.3 Conexão Multi-Database

**Troca Dinâmica de Bancos:**

```php
// Após seleção de empresa
Session::write('Config.host', $empresa['hostname_banco']);
Session::write('Config.database', $empresa['nome_banco']);
Session::write('Config.user', $empresa['usuario_banco']);
Session::write('Config.password', $this->decrypt($empresa['senha_banco']));

// ConnectionManager usa sessão para conectar ao banco correto
$conn = ConnectionManager::getDataSource('default');
```

### 6.4 Testes de Autenticação

**Teste de Login:**

1. Acesse `http://localhost:8000/test_login.php`
2. Credenciais de teste: `admin` / `mudar123`
3. O formulário exibe os dados POST enviados

**Teste de Conexão:**

```bash
# Validar conexão PostgreSQL
php test_connection.php

# Diagnóstico completo de bancos
php diagnostico_banco.php
```

## 7. Frontend (Next.js 16.0.0 + React 19.2.0)

### 7.1 Configuração e Execução

O frontend é uma aplicação Next.js moderna com SSR, TypeScript e Tailwind CSS.

**Requisitos:**
- Node.js 18.x ou superior
- pnpm 8.x ou superior

**Inicialização:**

```bash
# Instalar dependências (com legacy peer deps devido ao React 19)
pnpm install --legacy-peer-deps

# Servidor de desenvolvimento (hot reload)
pnpm dev
# Acesso: http://localhost:3000

# Build de produção
pnpm build

# Servidor de produção
pnpm start
```

### 7.2 Estrutura de Componentes

**Componentes Radix UI Disponíveis:**

```typescript
// components/ui/
- Button: Botões com variantes (default, destructive, outline, ghost)
- Dialog: Modais acessíveis com overlay
- Card: Containers de conteúdo estilizados
- Input: Campos de entrada customizados
- Label: Labels semânticos
- Select: Dropdowns nativos estilizados
- Checkbox: Checkboxes acessíveis
- RadioGroup: Radio buttons em grupo
- Toast: Notificações temporárias
```

**Layout Responsivo:**

```typescript
// app/layout.tsx
export default function RootLayout({ children }) {
  return (
    <html lang="pt-BR">
      <body>
        <ThemeProvider>
          <Header />
          {children}
          <Footer />
        </ThemeProvider>
      </body>
    </html>
  );
}
```

### 7.3 Integração Backend-Frontend

**Opções de Integração:**

1. **API Routes Next.js** (`app/api/`): Endpoints serverless
2. **Server Actions**: Mutações server-side no React 19
3. **Fetch direto ao PHP**: `fetch('http://localhost:8000/clientes/index')`

**Exemplo de Server Action:**

```typescript
// app/actions/clientes.ts
'use server'

export async function getClientes(filtro: string) {
  const response = await fetch(
    `http://localhost:8000/clientes/index?filtro=${filtro}`,
    { cache: 'no-store' }
  );
  return response.json();
}
```

## 8. Funcionalidades Implementadas

### 8.1 ⭐ Sistema de Correção de XML NFe (NOVO - Dez/2025)

**Objetivo**: Ajustar automaticamente divergências de valores em notas fiscais eletrônicas.

**Localização**: `controllers/XmlController.php` + `views/xml/index.php`

**Capacidades:**
- ✅ Upload múltiplo de XMLs (até 20 arquivos, 40MB cada)
- ✅ Detecção automática de divergências (vNF vs soma de itens)
- ✅ Correção inteligente (ajusta último item - vUnCom ou vDesc)
- ✅ Processamento de XMLs com desconto
- ✅ Geração de ZIP para download em lote
- ✅ Interface moderna com drag-and-drop
- ✅ Progress bar e logs em tempo real
- ✅ Estatísticas de processamento

**Algoritmo de Correção:**

```php
// controllers/XmlController.php - Método corrigirXml()
1. Extrai vNF (valor total da nota)
2. Calcula soma líquida dos itens (vProd - vDesc)
3. Compara diferença absoluta > 0.01
4. Se divergente:
   - Se último item tem desconto → Ajusta vDesc
   - Senão → Ajusta vUnCom e recalcula vProd
5. Salva XML corrigido em public/uploads/xml_temp/
```

**Endpoints:**

| Método | URL | Descrição |
|--------|-----|-----------|
| GET | `/xml/index` | Interface de upload |
| POST | `/xml/processar` | Processar XMLs enviados |
| GET | `/xml/download` | Baixar ZIP com XMLs corrigidos |

**Limites Configurados:**

```ini
; php.ini (C:\xampp\php\php.ini)
upload_max_filesize = 40M
post_max_size = 40M
max_file_uploads = 20
memory_limit = 512M
max_execution_time = 120
```

**Como Usar:**

1. Acesse `http://localhost:8000/xml/index`
2. Arraste XMLs ou clique para selecionar (máx. 20 arquivos)
3. Clique em "Processar XMLs"
4. Acompanhe logs e estatísticas em tempo real
5. Clique em "Baixar XMLs Corrigidos" (gera ZIP)
6. Use "Nova Correção" para resetar e processar novos arquivos

**Testes Disponíveis:**

```
public/test_xmls/
├── teste_divergencia.xml       # Divergência de 0.50
├── teste_com_desconto.xml      # Divergência com desconto
└── teste_correto.xml           # Sem divergência
```

Consulte `GUIA_TESTE_XML.md` para cenários de teste detalhados.

### 8.2 Sistema de Clientes (CRM)

**Funcionalidades:**
- Listagem paginada (20 registros por página)
- Filtros por nome, CPF/CNPJ
- Visualização de contatos e telefones
- Histórico de questionários
- Observações de contato
- Integração com ERP

**Endpoints:**
- `GET /clientes/index?page=1&filtro=nome`
- `GET /clientes/view/{id}`
- `POST /clientes/save`
- `DELETE /clientes/delete/{id}`

### 8.3 Sistema de Questionários

**Características:**
- Questionários customizáveis por empresa
- Parâmetros e faixas de valores configuráveis
- Perguntas com complementos (texto livre)
- Histórico completo de respostas
- Agendamento de próximos atendimentos
- Relatórios por período e tipo

**Estrutura de Tabelas:**
```
glb_questionario → Questionário base
glb_questionario_parametros → Configurações
glb_questionario_perguntas → Perguntas
glb_questionario_pergunta_cpls → Complementos
glb_questionario_respostas → Respostas
glb_questionario_resposta_historicos → Histórico
```

### 8.4 Relatórios e Análises

**Tipos de Relatórios:**
- Clientes por período de cadastro
- Questionários respondidos (mensal/anual)
- Análise de respostas por pergunta
- Próximos atendimentos agendados
- Estatísticas de vendas por vendedor
- Inadimplência e cobranças

**Exportação**: PDF, Excel (XLSX), CSV

## 9. Scripts de Diagnóstico

| Script | Descrição |
|--------|-----------|
| `test_connection.php` | Valida conexão PostgreSQL master |
| `test_db_connection.php` | Testa conexões multi-tenant |
| `diagnostico_banco.php` | Diagnóstico completo de todos os bancos |
| `listar_tabelas_propasso.php` | Lista tabelas do sistema Propasso |
| `ver_estrutura_empresas.php` | Exibe schema da tabela de empresas |
| `verificar_estrutura_propasso.php` | Valida estrutura Propasso |
| `verificar_estrutura_vendas.php` | Valida estrutura de vendas |
| `verificar_interfaces.php` | Lista interfaces/módulos disponíveis |
| `debug_empresas.php` | Debug de configurações de empresas |

## 10. Inicialização do Sistema

### 10.1 Backend PHP (Porta 8000)

**Servidor de Desenvolvimento:**

```powershell
# Windows (XAMPP)
C:\xampp\php\php.exe -S localhost:8000 router.php

# Linux/Mac
php -S localhost:8000 router.php
```

**Servidor em Background (PowerShell):**

```powershell
# Iniciar como job
$job = Start-Job -ScriptBlock {
    Set-Location "C:\Users\Lenovo01\OneDrive\Área de Trabalho\Projeto\Sysapp 2025\Sysapp_20251029_18.6.2"
    & "C:\xampp\php\php.exe" -S localhost:8000 router.php
}

# Verificar status
Get-Job
Receive-Job -Id $job.Id -Keep

# Parar servidor
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
```

**Verificar Servidor:**

```powershell
# Testar conectividade
Test-NetConnection -ComputerName localhost -Port 8000

# Acessar via browser
Start-Process "http://localhost:8000/usuarios/login"
```

### 10.2 Frontend Next.js (Porta 3000)

```bash
# Servidor de desenvolvimento
pnpm dev

# Servidor de produção
pnpm build && pnpm start
```

### 10.3 Acesso ao Sistema

**URLs Principais:**

- 🏠 **Home**: `http://localhost:8000/`
- 🔐 **Login**: `http://localhost:8000/usuarios/login`
- 👥 **Clientes**: `http://localhost:8000/clientes/index`
- 📄 **Correção XML**: `http://localhost:8000/xml/index`
- 📊 **Relatórios**: `http://localhost:8000/relatorios/index`

**Credenciais Padrão:**
- Usuário: `admin`
- Senha: `mudar123`

## 11. Configurações Avançadas

### 11.1 Configuração PHP (php.ini)

**Localização**: `C:\xampp\php\php.ini`

**Configurações Críticas:**

```ini
; Extensões necessárias
extension=pdo_pgsql
extension=pgsql
extension=mbstring
extension=zip        ; Necessário para XmlController
extension=openssl
extension=fileinfo

; Upload de arquivos (XML)
upload_max_filesize = 40M
post_max_size = 40M
max_file_uploads = 20

; Performance
memory_limit = 512M
max_execution_time = 120

; OPcache (recomendado)
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000

; Segurança
expose_php = Off
session.cookie_httponly = 1
session.use_only_cookies = 1
```

### 11.2 Configuração do Sistema (config/config.php)

```php
// Banco de Dados Master
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'systec');

// Segurança
define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

// Aplicação
define('APP_NAME', 'SysApp');
define('APP_VERSION', '18.6.2');
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
```

### 11.3 Troubleshooting Comum

**Problema: Extensão ZIP não encontrada**

```powershell
# Verificar extensão
php -m | Select-String "zip"

# Se não encontrado, editar php.ini
# Descomentar: extension=zip
# Reiniciar servidor PHP
```

**Problema: Erro de conexão PostgreSQL**

```powershell
# Verificar se PostgreSQL está rodando
Get-Service postgresql*

# Testar conexão
psql -U postgres -h localhost -d sysapp

# Verificar credenciais em config/config.php
```

**Problema: Upload falha**

```powershell
# Verificar limites PHP
php -i | Select-String "upload_max_filesize|post_max_size|max_file_uploads"

# Verificar permissões do diretório
icacls "public\uploads\xml_temp"
```

## 12. Segurança

### Práticas Implementadas

✅ **Prepared Statements**: Todas as queries usam `pg_query_params`  
✅ **Password Hashing**: SHA1 (legado, recomenda-se bcrypt)  
✅ **Session Management**: `session.cookie_httponly = 1`  
✅ **XSS Protection**: `htmlspecialchars()` em outputs  
✅ **CSRF Protection**: Tokens em formulários críticos  
✅ **Database Encryption**: Senhas de banco criptografadas (AES-256-CBC)  
✅ **File Validation**: Extensão, tamanho e MIME type  
✅ **Multi-factor**: Seleção de empresa após login  

### Checklist de Segurança em Produção

- [ ] Alterar `SECURITY_SALT` em `config/config.php`
- [ ] Alterar senha padrão do admin
- [ ] Configurar HTTPS com certificado SSL/TLS
- [ ] Configurar firewall PostgreSQL (porta 5432)
- [ ] Desabilitar `display_errors` em php.ini
- [ ] Implementar rate limiting para login
- [ ] Configurar backups automáticos diários
- [ ] Migrar hashing de SHA1 para bcrypt
- [ ] Habilitar auditoria no PostgreSQL
- [ ] Configurar Content Security Policy (CSP)

## 13. Performance

### Otimizações Implementadas

**Banco de Dados:**
- Índices em campos de busca (`login_usuario`, `nm_pessoa`, `cpf`)
- Connection pooling via Singleton
- Prepared statements para queries parametrizadas

**PHP:**
- OPcache habilitado (bytecode caching)
- Singleton para conexões de banco
- Lazy loading de models

**Frontend:**
- Server-Side Rendering (SSR) com Next.js
- Code splitting automático
- Tailwind CSS com JIT compiler
- Imagens otimizadas com next/image

**Assets:**
- Cache headers para CSS/JS/imagens (31536000s)
- Gzip compression para text/plain
- CDN para bibliotecas externas

## 14. Documentação Adicional

📖 **README_TECNICO.md**: Documentação técnica completa (1.100+ linhas)  
📖 **GUIA_TESTE_XML.md**: Guia de testes do sistema de correção XML  
📖 **CONFIGURACAO_BANCO.md**: Configuração detalhada de bancos  
📖 **INSTALACAO.md**: Guia passo a passo de instalação  

## 15. Suporte e Contribuição

### Logs do Sistema

- **PHP Errors**: `logs/php_errors.log`
- **PostgreSQL**: `/var/log/postgresql/postgresql-18-main.log`
- **Apache/XAMPP**: `C:\xampp\apache\logs\error.log`

### Comandos Úteis

```powershell
# Limpar cache do sistema
Remove-Item -Path "public\uploads\xml_temp\*" -Force -Recurse

# Verificar processos PHP
Get-Process php | Select-Object Id, CPU, WorkingSet

# Backup do banco master
pg_dump -U postgres -d sysapp -f backup_sysapp_$(Get-Date -Format 'yyyyMMdd').sql

# Análise de queries lentas (PostgreSQL)
# Habilitar em postgresql.conf: log_min_duration_statement = 1000
```

## 16. Licença e Versão

- **Versão**: 18.6.2
- **Data de Lançamento**: Outubro 2025
- **Última Atualização**: Dezembro 2025
- **Licença**: Proprietary (uso interno)

---

**📌 Nota**: Esta é uma aplicação em produção. Para documentação técnica detalhada sobre arquitetura, API endpoints, schema de banco de dados e troubleshooting avançado, consulte [README_TECNICO.md](README_TECNICO.md).