# Configuração do Banco de Dados SysApp

## Pré-requisitos
- PostgreSQL 9.6 ou superior instalado ✅
- Acesso de superuser ao PostgreSQL
- pgAdmin 4 (instalado com PostgreSQL) ou psql

## Passos para Configuração

### 1. Acessar o PostgreSQL

#### Opção A: Usando pgAdmin 4
1. Abra o pgAdmin 4 (instale se não tiver)
2. Conecte ao servidor local (localhost)
3. Digite a senha definida na instalação (geralmente "postgres")

#### Opção B: Usando psql (linha de comando)
```powershell
# No PowerShell
& "C:\Program Files\PostgreSQL\17\bin\psql.exe" -U postgres
# Digite a senha quando solicitado
```

### 2. Criar o Banco de Dados

No pgAdmin:
- Clique com botão direito em "Databases" → "Create" → "Database"
- Nome: `sysapp`
- Owner: `postgres`
- Encoding: `UTF8`
- Clique em "Save"

Ou no psql:
```sql
CREATE DATABASE sysapp WITH ENCODING 'UTF8';
\c sysapp
```

### 3. Executar o Script de Criação das Tabelas

#### No pgAdmin:
1. Selecione o banco `sysapp`
2. Clique em "Tools" → "Query Tool"
3. Abra o arquivo `database_schema.sql` (Arquivo → Abrir)
4. Clique em "Execute/Run" (F5)

#### No psql:
```bash
\c sysapp
\i 'C:/Users/N1/Desktop/projeto teste/Sysapp_20251029_18.6.2/database_schema.sql'
```

### 4. Verificar se as Tabelas foram Criadas

```sql
-- Listar todas as tabelas
\dt

-- Ou
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public';
```

Você deve ver:
- sysapp_config_user
- sysapp_config_empresas
- sysapp_config_user_empresas
- sysapp_controle_interface
- sysapp_config_user_empresas_interfaces
- sysapp_controle_envio_email

### 5. Verificar Usuário Padrão

```sql
SELECT * FROM sysapp_config_user;
```

Deve mostrar o usuário `admin`.

### 6. Atualizar Configurações do Sistema

Edite o arquivo `config/config.php` e ajuste as credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'SUA_SENHA_AQUI'); // Senha do PostgreSQL
```

### 7. Testar a Conexão

Execute este script PHP para testar:

```php
<?php
$conn = pg_connect("host=localhost port=5432 dbname=sysapp user=postgres password=SUA_SENHA");
if ($conn) {
    echo "Conexão bem-sucedida!";
    pg_close($conn);
} else {
    echo "Falha na conexão!";
}
?>
```

## Credenciais Padrão

Após a instalação, use estas credenciais para fazer login:

- **Usuário**: `admin`
- **Senha**: `admin`

⚠️ **IMPORTANTE**: Altere a senha padrão após o primeiro login!

## Estrutura do Banco

### Tabelas de Sistema
- `sysapp_config_user` - Usuários do sistema
- `sysapp_config_empresas` - Empresas/Bancos de dados
- `sysapp_config_user_empresas` - Relação usuário-empresa
- `sysapp_controle_interface` - Interfaces disponíveis
- `sysapp_config_user_empresas_interfaces` - Permissões

### Views
- `vw_login` - View de autenticação
- `vw_login_empresa_interface` - View de permissões

### Tabelas de Negócio (ERP)
As tabelas de negócio (clientes, questionários, etc.) geralmente já existem no banco de dados do ERP. Se você precisar criá-las, descomente as seções comentadas no arquivo `database_schema.sql`.

## Conectando ao Banco do ERP Existente

Se você já tem um banco de dados com as tabelas de clientes, produtos, etc.:

1. No pgAdmin, adicione uma nova empresa em `sysapp_config_empresas`
2. Configure o host, banco, usuário e senha do banco ERP
3. Relacione o usuário com a empresa em `sysapp_config_user_empresas`
4. Configure as permissões em `sysapp_config_user_empresas_interfaces`

O sistema suporta múltiplos bancos de dados!

## Solução de Problemas

### Erro: "password authentication failed"
- Verifique a senha do PostgreSQL
- Certifique-se de que o usuário `postgres` existe
- Verifique o arquivo `pg_hba.conf` (método de autenticação)

### Erro: "could not connect to server"
- Verifique se o serviço PostgreSQL está rodando:
  ```powershell
  Get-Service postgresql*
  ```
- Se não estiver, inicie:
  ```powershell
  Start-Service postgresql-x64-17
  ```

### Erro: "database does not exist"
- Execute o comando CREATE DATABASE novamente
- Verifique se está conectado ao servidor correto

## Backup do Banco

Para fazer backup:
```bash
pg_dump -U postgres -d sysapp -f backup_sysapp.sql
```

Para restaurar:
```bash
psql -U postgres -d sysapp -f backup_sysapp.sql
```

## Próximos Passos

Após configurar o banco:
1. Inicie o Apache no XAMPP
2. Acesse http://localhost/Sysapp_20251029_18.6.2
3. Faça login com admin/admin
4. Configure as empresas e permissões conforme necessário
