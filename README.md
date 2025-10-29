# SysApp - Sistema de Questionários

Versão 18.6.2 - PHP Puro

## Descrição

Sistema de gerenciamento de questionários, clientes e relatórios convertido de CakePHP para PHP puro.

## Requisitos

- PHP 7.4 ou superior
- PostgreSQL 9.6 ou superior
- Apache com mod_rewrite habilitado
- Extensão PHP: pgsql

## Instalação

1. **Configure o banco de dados**
   - Edite o arquivo `config/config.php`
   - Defina as credenciais do PostgreSQL:
     \`\`\`php
     define('DB_HOST', 'localhost');
     define('DB_PORT', '5432');
     define('DB_NAME', 'sysapp');
     define('DB_USER', 'postgres');
     define('DB_PASS', 'sua_senha');
     \`\`\`

2. **Configure o Apache**
   - Certifique-se de que o mod_rewrite está habilitado
   - Aponte o DocumentRoot para a pasta do projeto

3. **Permissões**
   - Dê permissão de escrita para logs (se necessário)

4. **Acesse o sistema**
   - Abra o navegador em: `http://localhost/`
   - Faça login com suas credenciais

## Estrutura do Projeto

\`\`\`
/
├── config/          # Configurações
│   ├── config.php   # Configurações gerais
│   └── database.php # Conexão com banco
├── core/            # Classes principais
│   ├── Controller.php
│   ├── Router.php
│   ├── Security.php
│   └── Session.php
├── controllers/     # Controllers
├── models/          # Models
├── views/           # Views
│   ├── layouts/     # Layouts
│   └── [controller]/
├── public/          # Arquivos públicos
│   ├── css/
│   ├── js/
│   └── images/
├── .htaccess        # Configuração Apache
└── index.php        # Arquivo principal
\`\`\`

## Funcionalidades

- Sistema de autenticação
- Gerenciamento de usuários
- Gerenciamento de clientes
- Questionários e pesquisas
- Relatórios
- Controle de permissões
- Multi-empresa

## Banco de Dados

O sistema utiliza as mesmas tabelas do sistema original:

- `sysapp_config_user` - Usuários
- `sysapp_config_empresas` - Empresas
- `sysapp_config_user_empresas` - Relação usuário-empresa
- `sysapp_config_user_empresas_interfaces` - Permissões
- `vw_login` - View de login
- `glb_pessoa` - Pessoas/Clientes
- `glb_questionario*` - Tabelas de questionários

## Segurança

- Senhas criptografadas com MD5 + Salt (compatível com sistema anterior)
- Proteção contra SQL Injection
- Sanitização de dados
- Sessões seguras
- Proteção CSRF (a implementar)

## Suporte

Para dúvidas ou problemas, consulte a documentação ou entre em contato com o suporte.
