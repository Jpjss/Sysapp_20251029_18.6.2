# SysApp - Documentação Técnica

[![PHP](https://img.shields.io/badge/PHP-8.2.12-777BB4?logo=php&logoColor=white)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-316192?logo=postgresql&logoColor=white)](https://postgresql.org)
[![Next.js](https://img.shields.io/badge/Next.js-16.0.0-000000?logo=next.js&logoColor=white)](https://nextjs.org)
[![License](https://img.shields.io/badge/License-Proprietary-red)](LICENSE)

## Sumário

- [Visão Geral](#visão-geral)
- [Arquitetura do Sistema](#arquitetura-do-sistema)
- [Stack Tecnológica](#stack-tecnológica)
- [Estrutura de Diretórios](#estrutura-de-diretórios)
- [Banco de Dados](#banco-de-dados)
- [Sistema de Autenticação](#sistema-de-autenticação)
- [Padrão MVC](#padrão-mvc)
- [Funcionalidades Principais](#funcionalidades-principais)
- [Configuração e Instalação](#configuração-e-instalação)
- [API e Rotas](#api-e-rotas)
- [Segurança](#segurança)
- [Performance e Otimização](#performance-e-otimização)
- [Troubleshooting](#troubleshooting)

---

## Visão Geral

**SysApp** é um sistema de gestão empresarial multi-tenant que integra funcionalidades de CRM, questionários, análise de clientes e correção de XMLs de NFe. O sistema foi desenvolvido com arquitetura híbrida PHP/Next.js, permitindo escalabilidade e manutenção facilitada.

### Características Principais

- **Multi-tenant**: Suporta múltiplas empresas com bancos de dados isolados
- **Sistema de Permissões**: Controle granular por usuário/empresa/interface
- **Integração ERP**: Conecta-se a sistemas ERP via PostgreSQL
- **Correção de XML NFe**: Ajuste automático de divergências em notas fiscais
- **CRM Integrado**: Gestão completa de clientes e questionários
- **Arquitetura Híbrida**: Backend PHP + Frontend Next.js/React

---

## Arquitetura do Sistema

### Diagrama de Arquitetura

```
┌─────────────────────────────────────────────────────┐
│                   Cliente (Browser)                  │
└────────────────────┬────────────────────────────────┘
                     │
           ┌─────────┴─────────┐
           │                   │
    ┌──────▼──────┐    ┌──────▼──────┐
    │  Next.js    │    │  PHP Server │
    │  (Port 3000)│    │  (Port 8000)│
    └──────┬──────┘    └──────┬──────┘
           │                   │
           │         ┌─────────┴─────────┐
           │         │                   │
           │    ┌────▼────┐        ┌────▼────┐
           │    │ Router  │        │  MVC    │
           │    │ (SEO)   │        │ Core    │
           │    └─────────┘        └────┬────┘
           │                            │
           └────────────────────────────┘
                                        │
                    ┌───────────────────┴───────────────────┐
                    │                                       │
              ┌─────▼─────┐                        ┌───────▼────────┐
              │ PostgreSQL │                        │ PostgreSQL     │
              │  sysapp    │◄───────────────────────│ empresa_1      │
              │  (Master)  │                        │ empresa_2      │
              └────────────┘                        │ empresa_n      │
                                                    └────────────────┘
```

### Fluxo de Requisição

1. **Cliente** → Requisição HTTP/HTTPS
2. **Router.php** → Roteamento de URLs e arquivos estáticos
3. **Controllers** → Lógica de negócio e orquestração
4. **Models** → Acesso a dados e validações
5. **Database** → PostgreSQL (Master + Multi-tenant)
6. **Views** → Renderização de templates PHP
7. **Response** → HTML/JSON para o cliente

---

## Stack Tecnológica

### Backend

| Tecnologia | Versão | Propósito |
|------------|--------|-----------|
| **PHP** | 8.2.12 | Servidor principal e lógica de negócio |
| **PostgreSQL** | 18.x | Banco de dados relacional |
| **CakePHP Components** | 2.x (parcial) | ORM e helpers legados |
| **SimpleXML** | Built-in | Processamento de XMLs NFe |
| **ZipArchive** | Built-in | Compactação de arquivos |

### Frontend

| Tecnologia | Versão | Propósito |
|------------|--------|-----------|
| **Next.js** | 16.0.0 | Framework React com SSR |
| **React** | 19.2.0 | Biblioteca de UI |
| **TypeScript** | 5.x | Tipagem estática |
| **Tailwind CSS** | 4.1.9 | Estilização utilitária |
| **Radix UI** | Vários | Componentes acessíveis |
| **Lucide React** | 0.454.0 | Ícones SVG |

### DevOps e Ferramentas

- **XAMPP** (PHP 8.2.12): Ambiente de desenvolvimento local
- **PowerShell**: Scripts de automação (setup, migrations)
- **Git**: Controle de versão
- **Composer**: Gerenciamento de dependências PHP (legado)
- **pnpm**: Gerenciamento de dependências Node.js

---

## Estrutura de Diretórios

```
Sysapp_20251029_18.6.2/
│
├── app/                          # Next.js Application
│   ├── admin/                    # Painel administrativo
│   ├── globals.css               # Estilos globais
│   ├── layout.tsx                # Layout principal React
│   └── page.tsx                  # Página inicial Next.js
│
├── components/                   # Componentes React reutilizáveis
│   ├── layout/                   # Componentes de layout
│   ├── ui/                       # Biblioteca de UI (Radix)
│   ├── theme-provider.tsx        # Gerenciamento de tema
│   └── theme-toggle.tsx          # Toggle dark/light mode
│
├── config/                       # Configurações do sistema
│   ├── config.php                # Configurações gerais
│   └── database.php              # Classe de conexão DB
│
├── Controller/                   # Controllers CakePHP (Legado)
│   ├── AppController.php         # Controller base CakePHP
│   ├── UsuariosController.php    # Gestão de usuários (legado)
│   ├── RelatoriosController.php  # Relatórios (legado)
│   └── ...
│
├── controllers/                  # Controllers PHP Puro (Moderno)
│   ├── ClientesController.php    # CRUD de clientes
│   ├── UsuariosController.php    # Autenticação e gestão
│   ├── XmlController.php         # Correção de XMLs NFe
│   └── ...
│
├── core/                         # Classes core do sistema
│   ├── Controller.php            # Controller base PHP puro
│   ├── Router.php                # Sistema de roteamento
│   ├── Session.php               # Gerenciamento de sessões
│   └── Security.php              # Funções de segurança
│
├── Model/                        # Models CakePHP (Legado)
│   └── Usuario.php               # Model de usuário
│
├── models/                       # Models PHP Puro (Moderno)
│   └── Cliente.php               # Model de cliente
│
├── View/                         # Views CakePHP (Legado)
│   └── Layouts/                  # Templates de layout
│
├── views/                        # Views PHP Puro (Moderno)
│   ├── layouts/
│   │   └── default.php           # Layout padrão
│   ├── usuarios/
│   │   ├── login.php             # Página de login
│   │   └── empresa.php           # Seleção de empresa
│   ├── clientes/
│   │   └── index.php             # Lista de clientes
│   └── xml/
│       └── index.php             # Interface de correção XML
│
├── public/                       # Arquivos públicos
│   ├── css/
│   │   └── style.css             # Estilos customizados
│   ├── js/
│   │   └── main.js               # JavaScript principal
│   ├── uploads/                  # Uploads de usuários
│   │   ├── xml_temp/             # XMLs temporários
│   │   └── xml_corrigidos/       # XMLs processados
│   └── test_xmls/                # XMLs de teste
│
├── lib/                          # Bibliotecas auxiliares
├── hooks/                        # React hooks customizados
├── styles/                       # Estilos adicionais Next.js
│
├── router.php                    # Roteador PHP dev server
├── index.php                     # Entry point da aplicação
├── database_schema.sql           # Schema do banco master
├── setup_database.ps1            # Script de setup automático
├── vincular_empresas.php         # Script de vinculação
│
├── package.json                  # Dependências Node.js
├── composer.json                 # Dependências PHP
├── tsconfig.json                 # Configuração TypeScript
├── next.config.mjs               # Configuração Next.js
├── tailwind.config.ts            # Configuração Tailwind
│
└── README_TECNICO.md             # Esta documentação
```

---

## Banco de Dados

### Arquitetura Multi-Tenant

O sistema utiliza um modelo de **database-per-tenant**, onde:

- **sysapp** (Master): Armazena usuários, empresas e permissões
- **empresa_1, empresa_2, ..., empresa_n**: Bancos específicos de cada empresa

### Schema do Banco Master (sysapp)

#### Tabelas Principais

**1. sysapp_config_user** - Usuários do sistema
```sql
CREATE TABLE sysapp_config_user (
    cd_usuario SERIAL PRIMARY KEY,
    nome_usuario VARCHAR(100) NOT NULL,
    login_usuario VARCHAR(50) UNIQUE NOT NULL,
    senha_usuario VARCHAR(255) NOT NULL,
    cd_usu_erp INTEGER,
    ativo BOOLEAN DEFAULT true,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**2. sysapp_config_empresas** - Empresas cadastradas
```sql
CREATE TABLE sysapp_config_empresas (
    cd_empresa SERIAL PRIMARY KEY,
    nome_empresa VARCHAR(150) NOT NULL,
    hostname_banco VARCHAR(100) DEFAULT 'localhost',
    nome_banco VARCHAR(100) NOT NULL,
    usuario_banco VARCHAR(50) NOT NULL,
    senha_banco VARCHAR(255) NOT NULL, -- Criptografada
    porta_banco INTEGER DEFAULT 5432,
    ativo BOOLEAN DEFAULT true,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**3. sysapp_config_user_empresas** - Relação usuário-empresa
```sql
CREATE TABLE sysapp_config_user_empresas (
    cd_usuario INTEGER REFERENCES sysapp_config_user(cd_usuario),
    cd_empresa INTEGER REFERENCES sysapp_config_empresas(cd_empresa),
    PRIMARY KEY (cd_usuario, cd_empresa)
);
```

**4. sysapp_controle_interface** - Interfaces/Módulos disponíveis
```sql
CREATE TABLE sysapp_controle_interface (
    cd_interface SERIAL PRIMARY KEY,
    nm_interface VARCHAR(100) NOT NULL,
    ds_interface TEXT,
    url_interface VARCHAR(255),
    ativo BOOLEAN DEFAULT true
);
```

**5. sysapp_config_user_empresas_interfaces** - Permissões
```sql
CREATE TABLE sysapp_config_user_empresas_interfaces (
    cd_usuario INTEGER,
    cd_empresa INTEGER,
    cd_interface INTEGER,
    PRIMARY KEY (cd_usuario, cd_empresa, cd_interface),
    FOREIGN KEY (cd_usuario, cd_empresa) 
        REFERENCES sysapp_config_user_empresas(cd_usuario, cd_empresa),
    FOREIGN KEY (cd_interface) 
        REFERENCES sysapp_controle_interface(cd_interface)
);
```

### Schema dos Bancos de Empresa

Cada banco de empresa contém tabelas do ERP, incluindo:

- **glb_pessoa**: Clientes/Fornecedores
- **glb_pessoa_fone**: Telefones
- **glb_pessoa_obs_contato**: Observações
- **glb_questionario**: Questionários
- **glb_questionario_pergunta**: Perguntas
- **glb_questionario_resposta**: Respostas
- **prc_filial**: Filiais
- **segu_usu_filial**: Usuários por filial

### Conexão Multi-Database

```php
// Conexão ao banco master
$connMaster = pg_connect(
    "host=localhost port=5432 dbname=sysapp user=postgres password=systec"
);

// Conexão ao banco da empresa
$empresa = getEmpresaData($cd_empresa);
$connEmpresa = pg_connect(
    "host={$empresa['hostname_banco']} 
     port={$empresa['porta_banco']} 
     dbname={$empresa['nome_banco']} 
     user={$empresa['usuario_banco']} 
     password={$empresa['senha_banco']}"
);
```

---

## Sistema de Autenticação

### Fluxo de Autenticação

```
┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│  Login   │────▶│  Verify  │────▶│  Select  │────▶│   App    │
│  Page    │     │  User    │     │ Company  │     │  Access  │
└──────────┘     └──────────┘     └──────────┘     └──────────┘
     │                │                 │                │
     │                │                 │                │
   POST           Query vw_login    Query empresas   Session
 user/pass        + password        disponíveis      empresa_id
                  verification                       + DB config
```

### Implementação

**1. Login (UsuariosController::login)**
```php
public function login() {
    if ($this->isPost()) {
        $login = $_POST['login_usuario'] ?? '';
        $senha = $_POST['senha_usuario'] ?? '';
        
        // Busca usuário via view vw_login
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $query = "SELECT * FROM vw_login WHERE login_usuario = $1";
        $result = pg_query_params($conn, $query, [$login]);
        $usuario = pg_fetch_assoc($result);
        
        // Verifica senha (SHA1)
        if ($usuario && sha1($senha) === $usuario['senha_usuario']) {
            // Armazena dados na sessão
            Session::write('Questionarios', [
                'cd_usu' => $usuario['cd_usuario'],
                'nm_usu' => $usuario['nome_usuario'],
                'login_usuario' => $usuario['login_usuario'],
                'cd_usu_erp' => $usuario['cd_usu_erp']
            ]);
            
            $this->redirect('usuarios/empresa');
        }
    }
}
```

**2. Seleção de Empresa (UsuariosController::empresa)**
```php
public function empresa() {
    $this->requireAuth();
    
    $cd_usuario = Session::read('Questionarios.cd_usu');
    
    // Busca empresas disponíveis
    $empresas = $this->Usuario->buscarEmpresasUsuario($cd_usuario);
    
    if ($this->isPost()) {
        $cd_empresa = $_POST['cd_empresa'];
        
        // Carrega configurações da empresa
        $empresa = $this->Usuario->buscarEmpresa($cd_empresa);
        
        // Armazena na sessão
        Session::write('Config.host', $empresa['hostname_banco']);
        Session::write('Config.database', $empresa['nome_banco']);
        Session::write('Config.user', $empresa['usuario_banco']);
        Session::write('Config.password', $this->decrypt($empresa['senha_banco']));
        Session::write('Config.porta', $empresa['porta_banco']);
        
        $this->redirect('relatorios/index');
    }
}
```

### Segurança

- **Hashing de Senha**: SHA1 (legado, recomenda-se migrar para bcrypt)
- **Proteção CSRF**: Token em formulários
- **Session Hijacking**: `session.cookie_httponly = 1`
- **SQL Injection**: Prepared statements com `pg_query_params`
- **XSS Prevention**: `htmlspecialchars()` em outputs

---

## Padrão MVC

### Controller Base

```php
// core/Controller.php
abstract class Controller {
    protected $layout = 'default';
    protected $viewVars = [];
    protected $pageTitle = '';
    
    public function __construct() {
        session_start();
    }
    
    protected function requireAuth() {
        if (!Session::check('Questionarios.cd_usu')) {
            $this->redirect('usuarios/login');
            exit;
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }
    
    protected function render($view = null) {
        // Renderiza view com layout
        extract($this->viewVars);
        
        ob_start();
        include BASE_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();
        
        include BASE_PATH . '/views/layouts/' . $this->layout . '.php';
    }
}
```

### Model Base

```php
// models/Cliente.php
class Cliente {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    public function listar($limit = 20, $offset = 0, $filtro = '') {
        $where = $filtro ? "WHERE nm_pessoa ILIKE $1" : "";
        $params = $filtro ? ["%$filtro%"] : [];
        
        $query = "
            SELECT p.*, pf.nr_fone 
            FROM glb_pessoa p
            LEFT JOIN glb_pessoa_fone pf ON p.cd_pessoa = pf.cd_pessoa
            $where
            ORDER BY p.nm_pessoa
            LIMIT $limit OFFSET $offset
        ";
        
        return pg_query_params($this->conn, $query, $params);
    }
}
```

### Router

```php
// core/Router.php
class Router {
    private $controller = 'usuarios';
    private $action = 'login';
    private $params = [];
    
    public function parseUrl() {
        $url = $_GET['url'] ?? 'usuarios/login';
        $url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
        
        $this->controller = $url[0] ?? 'usuarios';
        $this->action = $url[1] ?? 'login';
        $this->params = array_slice($url, 2);
        
        $this->dispatch();
    }
    
    private function dispatch() {
        $controllerFile = BASE_PATH . '/controllers/' . 
                          ucfirst($this->controller) . 'Controller.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = ucfirst($this->controller) . 'Controller';
            $controller = new $controllerClass();
            
            if (method_exists($controller, $this->action)) {
                call_user_func_array([$controller, $this->action], $this->params);
            }
        }
    }
}
```

---

## Funcionalidades Principais

### 1. Correção de XML NFe

**Objetivo**: Ajustar automaticamente divergências de valores em notas fiscais eletrônicas.

**Localização**: `controllers/XmlController.php`

**Algoritmo de Correção**:

```php
private function corrigirXml($caminhoArquivo, $nomeArquivo) {
    $xml = simplexml_load_file($caminhoArquivo);
    $xml->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
    
    // Extrai valores
    $vNF = (float) $xml->xpath('//nfe:ICMSTot/nfe:vNF')[0];
    $vProdTotal = (float) $xml->xpath('//nfe:ICMSTot/nfe:vProd')[0];
    
    // Calcula total dos itens
    $totalLiquido = 0;
    $itens = $xml->xpath('//nfe:det');
    
    foreach ($itens as $item) {
        $vProd = (float) $item->xpath('.//nfe:vProd')[0];
        $vDesc = (float) ($item->xpath('.//nfe:vDesc')[0] ?? 0);
        $totalLiquido += ($vProd - $vDesc);
    }
    
    // Calcula diferença
    $diferenca = $vNF - $totalLiquido;
    
    if (abs($diferenca) > 0.01) {
        // Ajusta último item
        $ultimoItem = end($itens);
        
        if ($vDesc > 0) {
            // Ajusta desconto
            $novoDesconto = $vDesc - $diferenca;
            $ultimoItem->xpath('.//nfe:vDesc')[0][0] = 
                number_format($novoDesconto, 2, '.', '');
        } else {
            // Ajusta valor do produto
            $vUnCom = (float) $ultimoItem->xpath('.//nfe:vUnCom')[0];
            $qCom = (float) $ultimoItem->xpath('.//nfe:qCom')[0];
            
            $novoVUnCom = $vUnCom - ($diferenca / $qCom);
            $ultimoItem->xpath('.//nfe:vUnCom')[0][0] = 
                number_format($novoVUnCom, 2, '.', '');
        }
        
        $resultado['corrigido'] = true;
    }
    
    return $resultado;
}
```

**Capacidades**:
- Uploads múltiplos (até 20 arquivos simultaneamente)
- Detecção automática de divergências
- Ajuste de valores com e sem desconto
- Geração de ZIP para download
- Logs detalhados por arquivo

**Limites PHP**:
```ini
upload_max_filesize = 40M
post_max_size = 40M
max_file_uploads = 20
```

### 2. Gestão de Clientes (CRM)

**Características**:
- Listagem paginada com filtros
- Visualização de contatos e telefones
- Histórico de questionários
- Observações de contato
- Integração com ERP

**Endpoints**:
- `GET /clientes/index` - Lista de clientes
- `GET /clientes/view/{id}` - Detalhes do cliente
- `POST /clientes/save` - Salvar cliente
- `DELETE /clientes/delete/{id}` - Excluir cliente

### 3. Sistema de Questionários

**Funcionalidades**:
- Criação de questionários customizados
- Parâmetros e faixas de valores
- Perguntas com complementos
- Histórico de respostas
- Agendamento de próximos atendimentos
- Relatórios por período

**Estrutura**:
- `glb_questionario` - Questionário base
- `glb_questionario_parametros` - Configurações
- `glb_questionario_perguntas` - Perguntas
- `glb_questionario_respostas` - Respostas

### 4. Relatórios

**Tipos de Relatórios**:
- Clientes por período
- Questionários respondidos
- Análise de respostas
- Próximos atendimentos
- Estatísticas de vendas
- Inadimplência

**Exportação**: PDF, Excel, CSV

---

## Configuração e Instalação

### Requisitos do Sistema

#### Mínimos
- **PHP**: 8.2 ou superior
- **PostgreSQL**: 16 ou superior
- **Node.js**: 18.x ou superior
- **Memória RAM**: 2GB
- **Disco**: 500MB livres

#### Recomendados
- **PHP**: 8.2.12
- **PostgreSQL**: 18.x
- **Node.js**: 20.x LTS
- **Memória RAM**: 4GB
- **Disco**: 2GB livres (para uploads)

### Extensões PHP Necessárias

```ini
extension=pdo_pgsql
extension=pgsql
extension=mbstring
extension=zip
extension=openssl
extension=curl
extension=fileinfo
extension=gd
```

### Instalação Automatizada

**Windows (PowerShell como Administrador)**:

```powershell
# 1. Clone o repositório
git clone <repository-url> sysapp
cd sysapp

# 2. Execute o setup do banco
.\setup_database.ps1

# 3. Configure a aplicação
# Edite config/config.php com suas credenciais

# 4. Instale dependências Node.js
pnpm install --legacy-peer-deps

# 5. Inicie o servidor PHP
C:\xampp\php\php.exe -S localhost:8000 router.php

# 6. (Opcional) Inicie Next.js
pnpm run dev
```

### Instalação Manual

**1. Criar banco de dados master**:
```sql
CREATE DATABASE sysapp WITH ENCODING 'UTF8';
```

**2. Importar schema**:
```bash
psql -U postgres -d sysapp -f database_schema.sql
```

**3. Criar usuário admin**:
```sql
INSERT INTO sysapp_config_user (nome_usuario, login_usuario, senha_usuario)
VALUES ('Administrador', 'admin', 'f865b53623b121fd34ee5426c792e5c33af8c227');
-- Senha: mudar123
```

**4. Configurar conexão** (`config/config.php`):
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'sua_senha');
```

**5. Configurar permissões**:
```bash
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads
```

**6. Iniciar servidor**:
```bash
php -S localhost:8000 router.php
```

### Credenciais Padrão

| Usuário | Senha | Tipo |
|---------|-------|------|
| admin | mudar123 | Administrador |

**⚠️ IMPORTANTE**: Altere a senha padrão imediatamente após o primeiro acesso!

---

## API e Rotas

### Estrutura de URLs

O sistema utiliza URLs amigáveis no formato:
```
http://localhost:8000/{controller}/{action}/{params}
```

### Rotas Públicas

| Método | URL | Controller | Action | Descrição |
|--------|-----|------------|--------|-----------|
| GET/POST | `/usuarios/login` | UsuariosController | login | Tela de login |
| GET | `/usuarios/logout` | UsuariosController | logout | Encerrar sessão |

### Rotas Autenticadas

#### Usuários
| Método | URL | Action | Descrição |
|--------|-----|--------|-----------|
| GET | `/usuarios/empresa` | empresa | Seleção de empresa |
| GET/POST | `/usuarios/changePassword` | changePassword | Trocar senha |
| GET | `/usuarios/visualizar` | visualizar | Listar usuários |
| POST | `/usuarios/testDbConnection` | testDbConnection | Testar conexão DB |

#### Clientes
| Método | URL | Descrição |
|--------|-----|-----------|
| GET | `/clientes/index?page=1&filtro=nome` | Lista clientes |
| GET | `/clientes/view/{id}` | Detalhes cliente |
| POST | `/clientes/save` | Salvar cliente |

#### Correção XML
| Método | URL | Descrição |
|--------|-----|-----------|
| GET | `/xml/index` | Interface upload |
| POST | `/xml/processar` | Processar XMLs |
| GET | `/xml/download` | Baixar ZIP |

#### Relatórios
| Método | URL | Descrição |
|--------|-----|-----------|
| GET | `/relatorios/index` | Dashboard |
| POST | `/relatorios/{tipo}` | Gerar relatório |

### Formato de Resposta JSON

**Sucesso**:
```json
{
    "success": true,
    "message": "Operação realizada com sucesso",
    "data": {
        "id": 123,
        "nome": "Cliente Exemplo"
    }
}
```

**Erro**:
```json
{
    "success": false,
    "message": "Erro ao processar requisição",
    "errors": [
        "Campo 'nome' é obrigatório",
        "Email inválido"
    ]
}
```

---

## Segurança

### Práticas Implementadas

#### 1. Autenticação e Autorização

- **Session Management**: PHP sessions com `session.cookie_httponly`
- **Password Hashing**: SHA1 (legado) - **RECOMENDA-SE MIGRAR PARA BCRYPT**
- **Multi-factor**: Seleção de empresa após login
- **Timeout**: Sessões expiram após inatividade

#### 2. Proteção contra Injeção

**SQL Injection**:
```php
// ✅ CORRETO - Prepared Statements
$query = "SELECT * FROM usuarios WHERE login = $1";
pg_query_params($conn, $query, [$login]);

// ❌ INCORRETO - String interpolation
$query = "SELECT * FROM usuarios WHERE login = '$login'";
```

**XSS Protection**:
```php
// ✅ CORRETO - Escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ❌ INCORRETO - Raw output
echo $user_input;
```

#### 3. Proteção de Arquivos

**Upload Validation**:
```php
// Valida extensão
$allowedExtensions = ['xml'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    throw new Exception('Arquivo não permitido');
}

// Valida tamanho
if ($filesize > 40 * 1024 * 1024) { // 40MB
    throw new Exception('Arquivo muito grande');
}

// Valida tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $tmpPath);
if ($mimeType !== 'text/xml' && $mimeType !== 'application/xml') {
    throw new Exception('Tipo de arquivo inválido');
}
```

#### 4. Criptografia de Senhas de Banco

```php
// Criptografia (legada - CakePHP Security)
function encrypt($text) {
    $key = SECURITY_SALT;
    $cipher = "aes-256-cbc";
    $iv = substr(hash('sha256', $key), 0, 16);
    return base64_encode(openssl_encrypt($text, $cipher, $key, 0, $iv));
}

// Descriptografia
function decrypt($encrypted) {
    $key = SECURITY_SALT;
    $cipher = "aes-256-cbc";
    $iv = substr(hash('sha256', $key), 0, 16);
    return openssl_decrypt(base64_decode($encrypted), $cipher, $key, 0, $iv);
}
```

### Configurações Recomendadas (php.ini)

```ini
; Desabilitar funções perigosas
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Ocultar versão do PHP
expose_php = Off

; Limitar uploads
upload_max_filesize = 40M
post_max_size = 40M
max_file_uploads = 20

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1  ; Em produção com HTTPS
session.use_only_cookies = 1
session.cookie_samesite = Strict

; Error handling
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### Checklist de Segurança

- [ ] Alterar senha padrão do admin
- [ ] Configurar HTTPS em produção
- [ ] Atualizar `SECURITY_SALT` em `config/config.php`
- [ ] Configurar firewall para PostgreSQL (porta 5432)
- [ ] Implementar rate limiting para login
- [ ] Habilitar auditoria no PostgreSQL
- [ ] Configurar backups automáticos
- [ ] Migrar hashing de SHA1 para bcrypt
- [ ] Implementar CSRF tokens em formulários
- [ ] Configurar Content Security Policy (CSP)

---

## Performance e Otimização

### Otimizações Implementadas

#### 1. Banco de Dados

**Índices**:
```sql
-- Índices em sysapp
CREATE INDEX idx_user_login ON sysapp_config_user(login_usuario);
CREATE INDEX idx_empresa_nome ON sysapp_config_empresas(nome_empresa);
CREATE INDEX idx_user_empresa ON sysapp_config_user_empresas(cd_usuario, cd_empresa);

-- Índices em bancos de empresa
CREATE INDEX idx_pessoa_nome ON glb_pessoa(nm_pessoa);
CREATE INDEX idx_pessoa_cpf ON glb_pessoa(cpf);
CREATE INDEX idx_questionario_pessoa ON glb_questionario(cd_pessoa);
```

**Connection Pooling**:
```php
// Reutilização de conexões
class Database {
    private static $instance = null;
    private $conn;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

#### 2. Caching

**Opções de Cache**:
- **OPcache**: Bytecode PHP (já habilitado)
- **Redis/Memcached**: Cache de sessões e queries (a implementar)
- **Browser Cache**: Headers para assets estáticos

**Configuração OPcache** (php.ini):
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

#### 3. Assets

**Otimizações CSS/JS**:
- Minificação de arquivos
- Concatenação de múltiplos arquivos
- Gzip compression
- CDN para bibliotecas externas

**Headers de Cache**:
```php
// router.php
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff2?)$/', $requestUri)) {
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
}
```

### Monitoramento

**Logs**:
- `php_errors.log`: Erros PHP
- `postgresql.log`: Queries lentas
- `access.log`: Acessos HTTP

**Métricas**:
```sql
-- Queries lentas (PostgreSQL)
SELECT query, calls, total_time, mean_time
FROM pg_stat_statements
ORDER BY mean_time DESC
LIMIT 10;
```

---

## Troubleshooting

### Problemas Comuns

#### 1. Erro de Conexão com Banco

**Sintoma**: "Não foi possível conectar ao banco de dados"

**Soluções**:
```bash
# Verificar se PostgreSQL está rodando
sudo systemctl status postgresql

# Testar conexão manual
psql -U postgres -h localhost -d sysapp

# Verificar credenciais em config/config.php
cat config/config.php | grep DB_

# Verificar pg_hba.conf
sudo nano /etc/postgresql/18/main/pg_hba.conf
# Adicionar: host all all 127.0.0.1/32 md5
```

#### 2. Erro 404 em Recursos Estáticos

**Sintoma**: CSS/JS não carregam (erro 404)

**Solução**:
```php
// Verificar router.php
if (file_exists($file)) {
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        // ... outros tipos
    ];
    
    header('Content-Type: ' . $mimeTypes[$ext]);
    readfile($file);
    exit;
}
```

#### 3. Sessão Não Persiste

**Sintoma**: Logout automático, sessão não mantém dados

**Soluções**:
```bash
# Verificar permissões do diretório de sessões
ls -la /var/lib/php/sessions

# Configurar session.save_path
mkdir -p /tmp/php_sessions
chmod 777 /tmp/php_sessions

# php.ini
session.save_path = "/tmp/php_sessions"
```

#### 4. Upload de Arquivo Falha

**Sintoma**: "Erro ao fazer upload do arquivo"

**Soluções**:
```bash
# Verificar limites no php.ini
php -i | grep upload_max_filesize
php -i | grep post_max_size
php -i | grep max_file_uploads

# Verificar permissões do diretório
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads

# Verificar espaço em disco
df -h
```

#### 5. Extensão ZIP Não Encontrada

**Sintoma**: "Class 'ZipArchive' not found"

**Solução**:
```bash
# Verificar extensão
php -m | grep zip

# Habilitar no php.ini
extension=zip

# Reiniciar PHP
sudo systemctl restart php-fpm
```

### Logs de Debug

**Habilitar modo debug** (`config/config.php`):
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errors.log');
```

**Query logging** (PostgreSQL):
```sql
-- postgresql.conf
log_statement = 'all'
log_duration = on
log_min_duration_statement = 1000  -- Queries > 1s
```

### Comandos Úteis

```bash
# Verificar processos PHP
ps aux | grep php

# Verificar conexões PostgreSQL
sudo -u postgres psql -c "SELECT * FROM pg_stat_activity;"

# Limpar sessões antigas
find /var/lib/php/sessions -type f -mtime +30 -delete

# Verificar uso de memória
free -h
top -p $(pgrep php)

# Testar sintaxe PHP
php -l arquivo.php

# Testar conexão de rede
nc -zv localhost 5432
telnet localhost 8000
```

---

## Licença e Contato

### Licença

Este projeto é **proprietary** e de uso exclusivo da organização. Reprodução, distribuição ou modificação não autorizadas são estritamente proibidas.

### Informações Técnicas

- **Versão**: 18.6.2
- **Data de Lançamento**: Outubro 2025
- **Última Atualização**: Dezembro 2025

### Suporte Técnico

Para questões técnicas, consulte:
- Documentação interna
- Equipe de desenvolvimento
- Issues no repositório interno

---

## Apêndices

### A. Glossário

- **Multi-tenant**: Arquitetura onde múltiplos clientes compartilham a mesma aplicação mas com dados isolados
- **ORM**: Object-Relational Mapping - mapeamento objeto-relacional
- **CRUD**: Create, Read, Update, Delete
- **MVC**: Model-View-Controller
- **NFe**: Nota Fiscal Eletrônica
- **ERP**: Enterprise Resource Planning

### B. Referências

- [PHP Documentation](https://www.php.net/docs.php)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Next.js Documentation](https://nextjs.org/docs)
- [React Documentation](https://react.dev/)

### C. Changelog

**v18.6.2** (Dezembro 2025)
- ✨ Implementado sistema de correção de XML NFe
- ✨ Interface de upload com progress bar
- 🔧 Migração parcial para PHP puro (MVC moderno)
- 🔧 Melhorias de performance em queries
- 🎨 UI modernizada com Tailwind CSS
- 🐛 Correção de bugs no roteamento
- 📝 Documentação técnica completa

**v18.6.1** (Novembro 2025)
- 🔧 Otimizações de banco de dados
- 🐛 Correções de segurança
- 📝 Melhorias na documentação

---

**📌 Nota**: Esta documentação é um documento vivo e deve ser atualizada conforme o sistema evolui. Para contribuir com melhorias, entre em contato com a equipe de desenvolvimento.
