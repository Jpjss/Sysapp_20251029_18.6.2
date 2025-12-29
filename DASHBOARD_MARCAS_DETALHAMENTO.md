# Dashboard de Marcas - Funcionalidade de Detalhamento

## 📊 Visão Geral

Esta documentação descreve a implementação da funcionalidade de **seleção e detalhamento de marcas** no Dashboard de Vendas por Marca.

## 🎯 Funcionalidades Implementadas

### 1. **Box de Seleção de Marcas**
- **Localização**: Canto superior direito do gráfico "Total de Vendas por Marca"
- **Conteúdo**: Lista dinâmica com as Top 10 marcas (ou Top 5/15/20, conforme selecionado)
- **Integração visual**: Box com sombra, borda e estilo consistente com o design do dashboard

### 2. **Dois Modos de Visualização**

#### **Modo Overview (Padrão)**
- Exibe o comparativo das Top 10 marcas
- Eixo X: Nome das marcas
- Eixo Y: Quantidade de vendas
- Gráfico tipo: Linha com preenchimento (laranja)

#### **Modo Detalhado (Após selecionar uma marca)**
- Exibe o progresso de vendas da marca selecionada ao longo do tempo
- Eixo X: Datas (formato dia/mês)
- Eixo Y: Quantidade de vendas
- Gráfico tipo: Linha com preenchimento (roxo/azul)
- Título dinâmico: "Progresso de Vendas – [Nome da Marca]"
- Subtítulo: "Acompanhamento diário nos últimos X dias"

### 3. **Recursos Interativos**

#### **Dropdown de Seleção**
```html
<select id="brandSelect">
  <option value="">-- Selecione uma marca --</option>
  <option value="MARCA1">MARCA 1</option>
  <option value="MARCA2">MARCA 2</option>
  ...
</select>
```

#### **Botão "Voltar para visão geral"**
- Aparece automaticamente quando uma marca é selecionada
- Restaura o gráfico original com todas as marcas
- Reseta o dropdown para a opção padrão

#### **Painel de Estatísticas**
Quando uma marca é selecionada, exibe:
- **Total de Vendas**: Número de pedidos no período
- **Quantidade Total**: Total de unidades vendidas
- **Valor Total**: Soma em R$ das vendas

### 4. **Transição Suave**
- Animação de 750ms ao alternar entre os modos
- Easing: `easeInOutQuart` para movimento natural
- Atualização sem recarregar a página

## 📁 Arquivos Criados/Modificados

### **Novos Arquivos**

#### 1. `/api/marca_historico.php`
Endpoint REST para buscar dados históricos de uma marca específica.

**Parâmetros:**
- `cd_marca` (obrigatório): Código da marca
- `periodo` (opcional): Número de dias (padrão: 30)
- `agrupamento` (opcional): dia, semana ou mes (padrão: dia)

**Resposta JSON:**
```json
{
  "success": true,
  "cd_marca": "MARCA1",
  "ds_marca": "Marca 1",
  "periodo": 30,
  "agrupamento": "dia",
  "totais": {
    "vendas": 150,
    "quantidade": 500,
    "valor": 12500.50
  },
  "data": {
    "labels": ["01/12", "02/12", ...],
    "datasets": [
      {
        "label": "Quantidade Vendida",
        "data": [10, 15, 20, ...]
      },
      {
        "label": "Valor Total (R$)",
        "data": [250.00, 375.50, ...]
      },
      {
        "label": "Total de Vendas",
        "data": [5, 8, 10, ...]
      }
    ]
  }
}
```

### **Arquivos Modificados**

#### 1. `/views/marcasvendas/dashboard.php`

**Novos Estilos CSS:**
- `.chart-header`: Container flex para título e controles
- `.brand-selector-box`: Box estilizada para o dropdown
- `.btn-back-overview`: Botão de retorno com gradiente
- `.stats-summary`: Painel de estatísticas resumidas
- `.chart-subtitle`: Subtítulo informativo

**Novos Elementos HTML:**
- Dropdown de seleção de marcas
- Botão "Voltar para visão geral"
- Painel de estatísticas (oculto por padrão)
- Subtítulo dinâmico do gráfico

**Novas Funções JavaScript:**
- `atualizarDropdownMarcas()`: Popula dropdown com Top 10
- `carregarHistoricoMarca(cd_marca)`: Busca e exibe histórico
- `voltarVisaoGeral()`: Restaura modo overview
- `configurarIntervalo()`: Atualização automática inteligente

## 🔄 Fluxo de Funcionamento

### **Inicialização**
1. Dashboard carrega em modo "overview"
2. Busca Top 10 marcas via `/api/marcas_vendas.php`
3. Popula dropdown com as marcas retornadas
4. Exibe gráfico comparativo padrão

### **Seleção de Marca**
1. Usuário seleciona uma marca no dropdown
2. JavaScript detecta mudança via `addEventListener('change')`
3. Chama `carregarHistoricoMarca(cd_marca)`
4. Faz requisição para `/api/marca_historico.php`
5. Atualiza gráfico com dados históricos
6. Exibe botão "Voltar" e painel de estatísticas
7. Altera título e subtítulo dinamicamente

### **Retorno à Visão Geral**
1. Usuário clica no botão "Voltar" ou seleciona "-- Selecione --"
2. Chama `voltarVisaoGeral()`
3. Reseta dropdown
4. Esconde botão e estatísticas
5. Recarrega dados gerais via `atualizarDados()`

### **Atualização Automática**
- Respeita o modo atual (overview ou detalhado)
- Se no modo detalhado, atualiza apenas a marca selecionada
- Intervalo configurável (10s, 30s, 1min, 2min, 5min)

## 🎨 Design e UX

### **Cores**
- **Overview**: Laranja (#FF9F40) - ênfase no comparativo
- **Detalhado**: Roxo/Azul (#667eea) - destaque individual
- **Estatísticas**: Verde (#4CAF50) - informações positivas

### **Responsividade**
- Dropdown ajusta-se em telas menores
- Box de seleção empilha verticalmente em mobile
- Gráfico mantém proporções adequadas

### **Acessibilidade**
- Labels descritivos nos selects
- Ícones FontAwesome para contexto visual
- Feedback visual em hover/focus
- Transições suaves sem causar desorientação

## 🚀 Como Usar

### **Para o Usuário Final**

1. **Acessar o Dashboard**
   ```
   URL: /marcasvendas/dashboard
   ```

2. **Visualizar Comparativo Geral**
   - Por padrão, visualiza Top 10 marcas
   - Ajuste período e limite conforme necessário

3. **Detalhar uma Marca**
   - Clique no dropdown "Selecionar Marca"
   - Escolha a marca desejada
   - Veja o progresso ao longo do tempo

4. **Voltar ao Comparativo**
   - Clique em "Voltar para visão geral"
   - Ou selecione "-- Selecione uma marca --" no dropdown

### **Para Desenvolvedores**

#### **Adicionar Novos Agrupamentos**

No arquivo `/api/marca_historico.php`, adicione no switch:

```php
case 'ano':
    $dateFormat = 'YYYY';
    $intervalFormat = '1 year';
    break;
```

#### **Personalizar Cores do Gráfico**

Em `carregarHistoricoMarca()`:

```javascript
chartVendas.data.datasets[0].backgroundColor = 'rgba(SEU_RGB, 0.2)';
chartVendas.data.datasets[0].borderColor = 'rgba(SEU_RGB, 1)';
```

#### **Adicionar Mais Estatísticas**

No HTML, adicione na `.stats-summary-content`:

```html
<div class="stat-item">
    <span class="stat-label">Nova Métrica</span>
    <span class="stat-value" id="statNovaMetrica">-</span>
</div>
```

## 📊 Estrutura de Dados

### **Query Principal (marca_historico.php)**

```sql
WITH date_series AS (
    -- Gera série de datas
    SELECT generate_series(
        CURRENT_DATE - INTERVAL '30 days',
        CURRENT_DATE,
        INTERVAL '1 day'
    )::date AS data
),
vendas_marca AS (
    -- Agrupa vendas por período
    SELECT 
        TO_CHAR(dt_emi_pedido, 'YYYY-MM-DD') as periodo,
        COUNT(DISTINCT cd_pedido) as total_vendas,
        SUM(qtde_produto) as quantidade_vendida,
        SUM(vl_tot_it - vl_devol_proporcional) as valor_total
    FROM dm_produto
    INNER JOIN dm_orcamento_vendas_consolidadas
        ON ...
    WHERE cd_marca = :cd_marca
    GROUP BY periodo
)
-- Join com série de datas para preencher dias sem vendas
SELECT 
    ds.data,
    COALESCE(vm.total_vendas, 0) as total_vendas,
    COALESCE(vm.quantidade_vendida, 0) as quantidade_vendida,
    COALESCE(vm.valor_total, 0) as valor_total
FROM date_series ds
LEFT JOIN vendas_marca vm ON ...
ORDER BY ds.data ASC
```

**Benefícios:**
- Preenche automaticamente dias sem vendas com zero
- Permite visualização contínua no gráfico
- Suporta diferentes agrupamentos (dia, semana, mês)

## 🔒 Segurança

### **Validações Implementadas**

1. **Autenticação**
   ```php
   if (!Session::isValid()) {
       http_response_code(401);
       echo json_encode(['success' => false, 'error' => 'Não autenticado']);
       exit;
   }
   ```

2. **Empresa Selecionada**
   ```php
   if (!Session::check('Config.database')) {
       http_response_code(400);
       echo json_encode(['error' => 'Nenhuma empresa selecionada']);
       exit;
   }
   ```

3. **Prepared Statements**
   ```php
   $stmt->bindParam(':cd_marca', $cd_marca, PDO::PARAM_STR);
   ```

4. **Validação de Parâmetros**
   ```php
   if (!$cd_marca) {
       http_response_code(400);
       echo json_encode(['error' => 'Código da marca obrigatório']);
       exit;
   }
   ```

## 🐛 Troubleshooting

### **Dropdown não carrega marcas**
- Verificar se `/api/marcas_vendas.php` retorna `marcas_detalhadas`
- Console do navegador: procurar erros JavaScript
- Verificar autenticação e empresa selecionada

### **Gráfico não atualiza**
- Inspecionar resposta de `/api/marca_historico.php` no DevTools
- Confirmar que `cd_marca` é válido no banco
- Verificar console para erros de requisição

### **Estatísticas exibem "NaN"**
- Confirmar estrutura de resposta JSON
- Verificar se `result.totais` existe
- Validar conversão de strings para números

### **Animação travada**
- Desabilitar temporariamente: `chartVendas.update('none')`
- Verificar performance do navegador
- Reduzir quantidade de pontos no gráfico

## 📈 Melhorias Futuras

### **Curto Prazo**
- [ ] Exportar dados da marca selecionada (CSV/Excel)
- [ ] Comparar duas marcas lado a lado
- [ ] Adicionar filtro por categoria de produto

### **Médio Prazo**
- [ ] Previsão de vendas usando ML
- [ ] Alertas de queda/aumento significativo
- [ ] Drill-down por produto específico

### **Longo Prazo**
- [ ] Dashboard mobile nativo
- [ ] Relatórios automáticos por email
- [ ] Integração com Power BI/Tableau

## 📞 Suporte

Para dúvidas ou problemas:
1. Consulte logs do servidor PHP
2. Verifique console do navegador
3. Revise esta documentação
4. Entre em contato com a equipe de desenvolvimento

---

**Versão**: 1.0  
**Data**: 28/12/2025  
**Autor**: Sistema SysApp  
**Última Atualização**: 28/12/2025
