# 📚 GUIA: Como Cadastrar Clientes no Sistema

## 🎯 Conceito

**Sistema Multi-Tenant:**
- **1 Admin** = acesso a TODAS as empresas
- **N Clientes** = cada um acessa APENAS sua empresa

---

## 🔐 Tipos de Usuário

### 👑 Administrador
- ✅ Acesso a múltiplas empresas
- ✅ Menu "Admin" visível
- ✅ Pode cadastrar usuários e empresas
- ✅ Escolhe qual empresa acessar no login

### 👤 Cliente
- ✅ Acesso a UMA única empresa
- ❌ Menu "Admin" oculto
- ❌ NÃO pode cadastrar usuários/empresas
- ✅ Entra direto no dashboard (sem escolher empresa)

---

## 📋 Processo de Cadastro de Cliente

### **Método 1: Pelo Menu Admin (Interface Web)**

1. **Faça login como admin:**
   - Login: `admin`
   - Senha: `admin`

2. **Acesse:** Menu Admin → Gerenciar Usuários

3. **Clique em:** "Novo Usuário"

4. **Preencha:**
   ```
   Nome: Nome do Cliente
   Login: login.cliente
   E-mail: cliente@empresa.com
   Senha: senha_inicial
   ```

5. **Vincule à empresa:**
   - Selecione APENAS UMA empresa
   - Se o cliente pode acessar várias, ele vira "admin"

6. **Defina permissões:**
   - ✅ relatorios (dashboard)
   - ✅ clientes (gerenciar clientes)
   - ✅ questionarios (fazer questionários)
   - ❌ admin (NÃO marcar)
   - ❌ usuarios (NÃO marcar)
   - ❌ empresas (NÃO marcar)

7. **Salve!**

---

### **Método 2: Por Script PHP**

Edite o arquivo `cadastrar_usuario_cliente.php` e altere:

```php
$dados_usuario = [
    'nome' => 'Nome do Seu Cliente',
    'login' => 'login_cliente',
    'email' => 'email@cliente.com',
    'senha' => 'senha123',
    'cd_empresa' => 1  // ID da empresa do cliente
];
```

Execute:
```bash
php cadastrar_usuario_cliente.php
```

---

### **Método 3: SQL Direto**

```sql
-- 1. Criar usuário
INSERT INTO sysapp_config_user 
(nm_usuario, ds_login, ds_email, ds_senha, fg_ativo) 
VALUES ('Cliente Teste', 'cliente', 'cliente@email.com', '123456', 'S')
RETURNING cd_usuario;

-- 2. Vincular à empresa (substitua X pelo cd_usuario retornado)
INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa) 
VALUES (X, 1);  -- 1 = ID da empresa

-- 3. Dar permissões
INSERT INTO sysapp_config_user_interfaces (cd_usuario, nm_interface) VALUES
(X, 'relatorios'),
(X, 'clientes'),
(X, 'questionarios');
```

---

## 🏢 Cadastrando Nova Empresa para Cliente

### **Via Interface Admin:**

1. **Menu Admin** → **Gerenciar Empresas**

2. **Novo Empresa**

3. **Preencha:**
   ```
   Nome: Nome da Empresa Cliente
   Host: localhost (ou IP/domínio do banco)
   Banco: nome_do_banco
   Usuário: usuario_postgres
   Senha: senha_banco
   Porta: 5432
   ```

4. **Teste Conexão** (botão azul)

5. **Salve!**

6. **Anote o ID da empresa** criada

---

## 👥 Exemplo Prático: Cliente "Drill"

Você tem a empresa "Drill" cadastrada (ID: 7)

### Criar usuário para Drill:

```php
// Arquivo: cadastrar_usuario_drill.php
$dados_usuario = [
    'nome' => 'Gestor Drill',
    'login' => 'drill',
    'email' => 'admin@drill.com',
    'senha' => 'drill123',
    'cd_empresa' => 7  // ID da empresa Drill
];
```

Execute:
```bash
php cadastrar_usuario_drill.php
```

**Credenciais para entregar ao cliente:**
- Login: `drill`
- Senha: `drill123`
- URL: `http://seudominio.com:8000`

---

## 🔄 Fluxo de Login

### Admin (múltiplas empresas):
```
Login → Escolhe Empresa → Dashboard
```

### Cliente (uma empresa):
```
Login → Dashboard (direto)
```

---

## ✅ Checklist Final

Para cada cliente:

- [ ] Empresa cadastrada no sistema
- [ ] Banco de dados da empresa configurado
- [ ] Usuário criado
- [ ] Usuário vinculado à empresa (apenas UMA)
- [ ] Permissões configuradas (sem "admin", "usuarios", "empresas")
- [ ] Credenciais enviadas ao cliente
- [ ] Orientação de primeiro acesso

---

## 🚀 Usuários Atualmente Cadastrados

1. **admin** (Administrador)
   - 2 empresas
   - Acesso total
   - Login: `admin` / Senha: `admin`

2. **diaazze** 
   - Status: incompleto (sem empresas vinculadas)
   - Precisa configurar

3. **joao.silva** (Cliente Exemplo)
   - 1 empresa: Empresa Padrão
   - Apenas consulta/relatórios
   - Login: `joao.silva` / Senha: `123456`

---

## 📞 Suporte

Para dúvidas ou problemas com cadastro de clientes:
- Verifique os logs do servidor PHP
- Teste a conexão com o banco da empresa
- Confirme se o usuário tem ao menos uma permissão

