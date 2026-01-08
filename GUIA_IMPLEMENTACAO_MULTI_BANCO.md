# SISTEMA MULTI-BANCO ADAPTATIVO - GUIA DE IMPLEMENTAÇÃO

## ✅ O QUE FOI CRIADO

### 1. DatabaseStructureDetector.php
**Local:** `helpers/DatabaseStructureDetector.php`
**Status:** ✅ Criado e pronto

Esta classe detecta automaticamente quais tabelas existem no banco conectado:
- Detecta estrutura NOVA (dm_*) ou ANTIGA (ped_vd)
- Faz cache da estrutura por 1 hora
- Fornece métodos para saber qual query usar

### 2. Métodos Adaptativos no Relatorio.php
**Status:** ⚠️ Precisa ser implementado manualmente

## 🔧 IMPLEMENTAÇÃO MANUAL

### PASSO 1: Adicionar detector no construtor

No arquivo `models/Relatorio.php`, localize o construtor (linha ~8):

```php
class Relatorio {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
```

**ADICIONE** essas linhas após `private $db;`:

```php
class Relatorio {
    private $db;
    private $structureDetector;  // ← ADICIONAR
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Inicializar detector de estrutura  // ← ADICIONAR
        require_once __DIR__ . '/../helpers/DatabaseStructureDetector.php';  // ← ADICIONAR
        $this->structureDetector = new DatabaseStructureDetector($this->db);  // ← ADICIONAR
```

### PASSO 2: Substituir método getEntradaVendas()

Localize o método `public function getEntradaVendas($filtros)` (linha ~350).

**SUBSTITUA** TODO o método (das ~350 linhas até o final do método - cerca de 200 linhas) por este código:

```php
    /**
     * Relatório Entrada x Vendas - ADAPTATIVO
     * Detecta automaticamente a estrutura do banco e usa a query apropriada
     */
    public function getEntradaVendas($filtros) {
        // Detectar estrutura do banco
        $structure = $this->structureDetector->detectStructure();
        
        // Usar query conforme estrutura disponível
        if ($structure['version'] === 'NEW') {
            return $this->getEntradaVendasNew($filtros);
        } elseif ($structure['version'] === 'OLD') {
            return $this->getEntradaVendasOld($filtros);
        } else {
            throw new Exception('Estrutura de banco de dados não reconhecida');
        }
    }
```

### PASSO 3: Adicionar métodos privados

**ADICIONE** estes três métodos APÓS o `getEntradaVendas()`:

Veja o arquivo completo em: `models/Relatorio_getEntradaVendas_NOVO.php`

Os métodos são:
1. `private function getEntradaVendasNew($filtros)` - Query para estrutura nova
2. `private function getEntradaVendasOld($filtros)` - Query para estrutura antiga  
3. `private function executeEntradaVendas($sql, $params)` - Executa e formata resultados

## 📝 CÓDIGO COMPLETO DOS MÉTODOS

Copie do arquivo `models/Relatorio_getEntradaVendas_NOVO.php` (linhas 19 até o final).

## ✅ RESULTADO ESPERADO

Após implementar, o sistema irá:

1. **Detectar automaticamente** a estrutura do banco ao conectar
2. **Usar query correta:**
   - Bancos com `dm_orcamento_vendas_consolidadas` → Usa query nova
   - Bancos com `ped_vd` → Usa query antiga
3. **Funcionar em TODOS os bancos:** propasso, diaazze, agape, drill e futuros

## 🧪 TESTE

Após implementar:

1. Acesse o relatório "Entrada X Vendas"
2. Selecione período: 01/10/2025 a 07/10/2025
3. Clique em "Visualizar"
4. Deve mostrar vendas por marca e filial

## 📚 ARQUIVOS DE REFERÊNCIA

- ✅ `helpers/DatabaseStructureDetector.php` - Detector (pronto)
- 📄 `models/Relatorio_getEntradaVendas_NOVO.php` - Código novo (copiar daqui)
- 🔄 `models/Relatorio.php` - Arquivo para editar
- 💾 `models/Relatorio_BACKUP_antes_adaptativo.php` - Backup do original

## 🎯 PRÓXIMOS PASSOS

Depois de funcionar o "Entrada X Vendas", aplicar o mesmo padrão para:
- `getEstatisticas()`
- `getAtendimentosPorPeriodo()`
- `getTopClientes()`
- Todos os outros métodos do relatório

## ❓ DÚVIDAS?

O padrão é sempre:
1. Método público chama `$this->structureDetector->detectStructure()`
2. Verifica `$structure['version']`
3. Chama método privado específico (`...New()` ou `...Old()`)
4. Métodos privados têm queries diferentes conforme estrutura

---

**Data:** 06/01/2026
**Autor:** GitHub Copilot  
**Versão:** 1.0
