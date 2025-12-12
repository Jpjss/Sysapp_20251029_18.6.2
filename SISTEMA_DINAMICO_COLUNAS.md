# Sistema Dinâmico de Colunas - Documentação

## Problema Resolvido

O sistema estava quebrandonecessário ao tentar acessar bancos de dados de clientes diferentes, pois as queries SQL usavam nomes de colunas fixos (hardcoded). Quando um banco tinha colunas com nomes diferentes, as queries falhavam.

## Solução Implementada

### 1. DatabaseHelper Class (`core/DatabaseHelper.php`)

Criada uma classe helper que:

- **Detecta colunas disponíveis** em qualquer tabela
- **Constrói queries dinamicamente** baseadas nas colunas existentes
- **Usa cache** para evitar consultas repetidas ao schema
- **Fornece mapeamento flexível** de nomes de colunas

#### Principais Métodos:

```php
// Retorna todas as colunas de uma tabela
$helper->getTableColumns('glb_pessoa');

// Verifica se coluna existe
$helper->columnExists('glb_pessoa', 'nm_fant');

// Retorna primeira coluna disponível de uma lista
$helper->getAvailableColumn('glb_pessoa', ['nm_fant', 'nm_pessoa', 'nome']);

// Constrói SELECT dinâmico
$selectClause = $helper->buildDynamicSelect('glb_pessoa', $columnMap);

// Constrói WHERE para busca
$where = $helper->buildSearchWhere('glb_pessoa', $searchColumns, $filtro);

// Constrói ORDER BY
$orderBy = $helper->buildOrderBy('glb_pessoa', ['nm_fant', 'nm_pessoa']);

// Verifica se tabela existe
$helper->tableExists('glb_pessoa_fone');
```

### 2. Mapeamento Padrão de Colunas

O sistema agora aceita múltiplos nomes para cada campo:

```php
DatabaseHelper::getClientColumnMapping();
```

Retorna mapeamento:
- **cd_pessoa**: ['cd_pessoa', 'cd_cliente', 'codigo']
- **nm_fant**: ['nm_fant', 'nm_fantasia', 'nome_fantasia']
- **nm_razao**: ['nm_razao', 'nm_pessoa', 'razao_social', 'nome']
- **cpf_cnpj**: ['nr_cpf_cnpj', 'cpf_cgc', 'cpf_cnpj', 'documento']
- **endereco**: ['ds_endereco', 'endereco', 'rua', 'logradouro']
- **bairro**: ['ds_bairro', 'bairro']
- **cidade**: ['ds_cidade', 'cidade', 'municipio']
- **uf**: ['ds_uf', 'uf', 'estado']
- **cep**: ['nr_cep', 'cep']
- **fone**: ['fone', 'telefone', 'nr_telefone', 'nr_fone']
- **email**: ['ds_email', 'email', 'e_mail']
- **dt_nascimento**: ['dt_nascimento', 'dt_nasc', 'data_nascimento']
- **dt_cadastro**: ['dt_cadastro', 'dt_cad', 'data_cadastro']

### 3. Model Cliente.php Atualizado

O model `Cliente` agora usa o `DatabaseHelper` em todos os métodos:

#### Antes (hardcoded):
```php
$sql = "SELECT cd_pessoa, 
               nm_fant, 
               nr_cpf_cnpj as cpf_cnpj 
        FROM glb_pessoa";
```

#### Depois (dinâmico):
```php
$columnMap = DatabaseHelper::getClientColumnMapping();
$selectClause = $this->helper->buildDynamicSelect('glb_pessoa', $columnMap);

$sql = "SELECT $selectClause FROM glb_pessoa";
```

#### Métodos Atualizados:
- ✅ `listar()` - Lista clientes com paginação
- ✅ `count()` - Conta total de clientes
- ✅ `findById()` - Busca cliente por ID
- ✅ `search()` - Autocomplete/busca
- ✅ `getTelefones()` - Busca telefones (detecta tabela e colunas)
- ✅ `getObservacoes()` - Busca observações (verifica se tabela existe)
- ✅ `getHistorico()` - Busca histórico de vendas (detecta colunas e tabela)

### 4. Scripts de Teste Criados

#### `debug_client_db.php`
Analisa a estrutura completa do banco do cliente:
- Verifica conexão
- Lista todas as colunas de `glb_pessoa`
- Identifica colunas importantes
- Mostra amostra de registros
- Verifica tabelas relacionadas

#### `test_client_connection.php`
Testa o sistema dinâmico:
- Conecta ao banco do cliente
- Mostra estrutura detectada
- Lista clientes usando queries dinâmicas
- Testa busca com filtro
- Mostra mapeamento de colunas aplicado

## Como Funciona

### 1. Detecção Automática

Quando você acessa um banco de dados, o sistema:
1. Consulta `information_schema.columns` para ver quais colunas existem
2. Armazena em cache para performance
3. Constrói queries usando apenas colunas disponíveis

### 2. Fallback Inteligente

Se uma coluna não existe, o sistema:
- Tenta alternativas do mapeamento
- Usa valor padrão (geralmente `''`) se nenhuma opção existe
- Não quebra a aplicação

### 3. Exemplo Prático

**Banco Propasso** tem coluna `cpf_cgc`  
**Banco Teste** tem coluna `nr_cpf_cnpj`

O sistema detecta automaticamente e usa a coluna correta:

```php
// Sistema escolhe automaticamente:
// Propasso: cpf_cgc as cpf_cnpj
// Teste: nr_cpf_cnpj as cpf_cnpj
```

## Benefícios

✅ **Compatível com qualquer estrutura de banco**  
✅ **Não precisa alterar código ao mudar de cliente**  
✅ **Queries seguras e otimizadas**  
✅ **Cache para performance**  
✅ **Fácil adicionar novos mapeamentos**  
✅ **Código mais limpo e manutenível**

## Como Usar em Novos Models

```php
require_once BASE_PATH . '/core/DatabaseHelper.php';

class MeuModel {
    private $db;
    private $helper;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->helper = new DatabaseHelper();
    }
    
    public function listar() {
        // Define mapeamento
        $columnMap = [
            'id' => ['cd_registro', 'id', 'codigo'],
            'nome' => ['nm_registro', 'nome', 'descricao']
        ];
        
        // Constrói SELECT dinâmico
        $select = $this->helper->buildDynamicSelect('minha_tabela', $columnMap);
        
        $sql = "SELECT $select FROM minha_tabela";
        return $this->db->fetchAll($sql);
    }
}
```

## Teste de Conexão

Para testar com o banco do cliente (Agape):

1. Acesse: `http://127.0.0.1:8000/test_client_connection.php`
2. Veja a estrutura detectada
3. Confirme que os clientes são listados corretamente

## Próximos Passos

- [ ] Aplicar DatabaseHelper em outros models (Relatorio, Questionario, etc)
- [ ] Adicionar suporte para JOINS dinâmicos
- [ ] Criar interface admin para gerenciar mapeamentos customizados
- [ ] Documentar mapeamentos específicos de cada cliente

## Arquivos Modificados

- ✅ `core/DatabaseHelper.php` (NOVO)
- ✅ `models/Cliente.php` (ATUALIZADO)
- ✅ `debug_client_db.php` (NOVO)
- ✅ `test_client_connection.php` (NOVO)

---

**Data:** 08/12/2025  
**Desenvolvedor:** GitHub Copilot + João Paulo
