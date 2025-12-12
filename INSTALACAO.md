# Guia de Instalação - SysApp PHP Puro

## Requisitos do Sistema

- PHP 7.4 ou superior
- PostgreSQL 9.6 ou superior
- Apache 2.4 ou superior com mod_rewrite
- Extensões PHP necessárias:
  - pgsql
  - mbstring
  - json

## Passo a Passo

### 1. Configurar o Banco de Dados

O sistema utiliza o banco de dados PostgreSQL existente. Certifique-se de que as seguintes tabelas existem:

**Tabelas principais:**
- `sysapp_config_user` - Usuários do sistema
- `sysapp_config_empresas` - Empresas/Bancos de dados
- `sysapp_config_user_empresas` - Relação usuário-empresa
- `sysapp_config_user_empresas_interfaces` - Permissões
- `sysapp_controle_interface` - Interfaces/Relatórios disponíveis
- `vw_login` - View de login
- `vw_login_empresa_interface` - View de permissões
- `glb_pessoa` - Clientes
- `glb_pessoa_fone` - Telefones
- `glb_pessoa_obs_contato` - Observações de contato
- `glb_questionario` - Questionários
- `glb_questionario_pergunta` - Perguntas
- `glb_questionario_resposta` - Respostas
- Views de relatórios (vw_questionario_*, vw_relatorio_*)

### 2. Configurar o Sistema

Edite o arquivo `config/config.php`:

\`\`\`php
// Configurações de banco de dados
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'sua_senha_aqui');
\`\`\`

### 3. Configurar o Apache

**Habilitar mod_rewrite:**
\`\`\`bash
sudo a2enmod rewrite
sudo systemctl restart apache2
\`\`\`

**Configurar VirtualHost (opcional):**
\`\`\`apache
<VirtualHost *:80>
    ServerName sysapp.local
    DocumentRoot /var/www/sysapp
    
    <Directory /var/www/sysapp>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/sysapp_error.log
    CustomLog \${APACHE_LOG_DIR}/sysapp_access.log combined
</VirtualHost>
\`\`\`

### 4. Permissões de Arquivos

\`\`\`bash
# Dar permissão de leitura para o Apache
sudo chown -R www-data:www-data /var/www/sysapp
sudo chmod -R 755 /var/www/sysapp
\`\`\`

### 5. Testar a Instalação

1. Acesse: `http://localhost/` ou `http://sysapp.local/`
2. Faça login com suas credenciais existentes
3. O sistema deve carregar o dashboard

## Estrutura de URLs

O sistema usa URLs amigáveis:

- `/usuarios/login` - Login
- `/relatorios/index` - Dashboard
- `/clientes/index` - Lista de clientes
- `/questionarios/index` - Questionários
- `/usuarios/visualizar` - Gerenciar usuários

## Solução de Problemas

### Erro 404 em todas as páginas
- Verifique se o mod_rewrite está habilitado
- Verifique se o arquivo .htaccess existe na raiz
- Verifique as permissões do .htaccess

### Erro de conexão com banco
- Verifique as credenciais em config/config.php
- Teste a conexão: `psql -h localhost -U postgres -d sysapp`
- Verifique se o PostgreSQL está rodando

### Página em branco
- Ative display_errors no PHP
- Verifique os logs do Apache: `/var/log/apache2/error.log`
- Verifique permissões dos arquivos

### Sessão não funciona
- Verifique permissões da pasta de sessões do PHP
- Verifique configurações de sessão no php.ini

## Migração de Dados

O sistema é compatível com o banco de dados existente do CakePHP. Não é necessário migrar dados, apenas:

1. Aponte para o mesmo banco de dados
2. As senhas são compatíveis (MD5 + Salt)
3. Todas as tabelas são utilizadas diretamente

## Segurança

- Altere o SECURITY_SALT em config/config.php
- Use HTTPS em produção
- Mantenha o PHP atualizado
- Configure firewall para PostgreSQL
- Faça backups regulares do banco

## Suporte

Para problemas ou dúvidas, consulte a documentação ou entre em contato com o suporte técnico.
\`\`\`
