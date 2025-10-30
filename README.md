# SysApp - Análise de Projeto

## 1. Visão Geral

Este documento fornece uma análise técnica detalhada do projeto SysApp, com base na estrutura de arquivos e scripts fornecidos. O projeto parece ser um sistema de gestão ou CRM, com funcionalidades voltadas para clientes, vendas e questionários. A arquitetura é híbrida, combinando um backend em PHP com um frontend moderno em Next.js.

## 2. Arquitetura

O projeto é composto por duas partes principais:

*   **Backend (PHP):** Um backend tradicional em PHP, estruturado em um padrão que se assemelha ao MVC (Model-View-Controller). Ele lida com a lógica de negócios, acesso ao banco de dados e possivelmente uma API para o frontend.
*   **Frontend (Next.js):** Um frontend moderno construído com Next.js e React, localizado no diretório `app/`. Isso sugere uma abordagem de Single Page Application (SPA) ou Server-Side Rendering (SSR) para a interface do usuário.

## 3. Tecnologias Utilizadas

*   **Backend:** PHP
*   **Frontend:** Next.js, React, TypeScript, PostCSS, Tailwind CSS (inferido a partir de `postcss.config.mjs` e `globals.css`)
*   **Banco de Dados:** SQL (provavelmente SQL Server ou MySQL, a ser confirmado pelos scripts de banco de dados)
*   **Shell Scripts:** PowerShell (`.ps1`) para automação de tarefas de banco de dados.

## 4. Estrutura do Projeto

A seguir, uma descrição dos diretórios e arquivos mais importantes:

*   `app/`: Contém a aplicação Next.js.
    *   `app/layout.tsx`: Layout principal da aplicação.
    *   `app/page.tsx`: Página inicial da aplicação.
    *   `app/globals.css`: Estilos globais da aplicação.
*   `components/`: Componentes React reutilizáveis.
*   `config/`: Arquivos de configuração do PHP.
    *   `config.php`: Configurações gerais da aplicação.
    *   `database.php`: Configurações de conexão com o banco de dados.
*   `Controller/` e `controllers/`: Classes de controller do PHP, que orquestram as requisições.
*   `Model/` e `models/`: Classes de modelo do PHP, que representam as entidades do banco de dados e a lógica de negócios.
*   `View/` e `views/`: Arquivos de template do PHP, responsáveis pela apresentação.
*   `core/`: Arquivos do núcleo do framework PHP, como `Router.php` e `Controller.php`.
*   `public/`: Arquivos públicos acessíveis pela web, como imagens, CSS e JS.
*   `scripts/` (implícito pelos arquivos `.ps1`): Scripts de automação.

## 5. Configuração do Banco de Dados

O banco de dados é uma parte central deste projeto. Os seguintes arquivos são relevantes para sua configuração:

*   `database_schema.sql`: Contém o esquema do banco de dados, com as definições de tabelas e relacionamentos.
*   `setup_database.ps1`: Script PowerShell para configurar o banco de dados principal.
*   `setup_banco_cliente.ps1`: Script para configurar um banco de dados de cliente.
*   `criar_banco_cliente_exemplo.ps1`: Script para criar um banco de dados de exemplo.
*   `vincular_propasso.ps1` e `vincular_usuario_banco.ps1`: Scripts para vincular dados entre diferentes partes do sistema.

Para configurar o banco de dados, é provável que seja necessário executar os scripts PowerShell em um ambiente Windows com acesso ao servidor de banco de dados.

## 6. Backend (PHP)

O backend é construído em PHP e segue uma estrutura que parece ser um framework customizado.

### Autenticação

O arquivo `test_login.php` fornece um formulário de teste de login simples. Ele envia um POST com `email` and `senha` para si mesmo.

**Para testar o login:**

1.  Acesse `test_login.php` em seu navegador.
2.  Preencha os campos "Usuário" e "Senha".
3.  Clique em "Testar Login".
4.  A página irá recarregar e exibir os dados enviados via POST.

**Observação:** Este script não realiza a autenticação real, apenas exibe os dados enviados. A lógica de autenticação real provavelmente está localizada em `UsuariosController.php` e `Security.php`.

## 7. Frontend (Next.js)

O frontend é uma aplicação Next.js. Para executá-lo, você precisará do Node.js e do `pnpm` instalados.

**Passos para iniciar o frontend:**

1.  Instale as dependências:
    ```bash
    pnpm install
    ```
2.  Inicie o servidor de desenvolvimento:
    ```bash
    pnpm dev
    ```
3.  Acesse a aplicação em `http://localhost:3000`.

## 8. Scripts Relevantes

*   `test_connection.php`: Testa a conexão com o banco de dados.
*   `diagnostico_banco.php`: Executa um diagnóstico no banco de dados.
*   `listar_tabelas_propasso.php`: Lista as tabelas relacionadas ao "Propasso".
*   `ver_estrutura_empresas.php`: Exibe a estrutura da tabela de empresas.

## 9. Próximos Passos e Recomendações

*   **Analisar os arquivos de configuração:** É crucial conseguir ler os arquivos em `config/` para entender como o sistema se conecta ao banco de dados e outras configurações importantes.
*   **Mapear o banco de dados:** Analisar o `database_schema.sql` para entender completamente o modelo de dados.
*   **Integrar Frontend e Backend:** Investigar como o frontend Next.js se comunica com o backend PHP. Provavelmente, o backend expõe uma API que é consumida pelo frontend.
*   **Documentar a API:** Se uma API existir, documentá-la usando ferramentas como o Swagger ou o OpenAPI para facilitar o desenvolvimento e a manutenção.