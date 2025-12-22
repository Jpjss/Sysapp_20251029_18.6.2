# Dashboard de Marcas Mais Vendidas - Tempo Real

Sistema de gráficos em tempo real para acompanhamento de marcas mais vendidas por cliente.

## ✨ Características

- ✅ **Atualização Automática**: Gráficos atualizam automaticamente sem recarregar a página
- ✅ **Multi-Cliente**: Cada cliente logado visualiza apenas os dados do seu banco
- ✅ **Múltiplos Períodos**: Filtro por 7, 15, 30, 60 ou 90 dias
- ✅ **Top Marcas**: Visualize Top 5, 10, 15 ou 20 marcas
- ✅ **Intervalo Configurável**: Atualizações de 10 segundos até 5 minutos
- ✅ **3 Gráficos**: Quantidade, Valor Total e Total de Vendas
- ✅ **Tabela Detalhada**: Visualização completa dos dados

## 📁 Arquivos Criados

### 1. API Endpoint
**Arquivo**: `/api/marcas_vendas.php`

Retorna dados em JSON das marcas mais vendidas:
```json
{
  "success": true,
  "periodo": 30,
  "timestamp": "2025-12-21 23:10:00",
  "data": {
    "labels": ["Marca A", "Marca B", ...],
    "datasets": [...]
  },
  "marcas_detalhadas": [...]
}
```

**Parâmetros**:
- `periodo`: Número de dias (padrão: 30)
- `limite`: Top N marcas (padrão: 10)
- `cd_filial`: Código da filial (opcional)

### 2. Controller
**Arquivo**: `/controllers/MarcasVendasController.php`

Gerencia acesso ao dashboard com verificação de autenticação.

### 3. View - Dashboard
**Arquivo**: `/views/marcas_vendas/dashboard.php`

Interface completa com:
- 3 gráficos interativos (Chart.js 4.4.0)
- Controles de filtro
- Tabela detalhada
- Status de atualização em tempo real
- Design responsivo (Bootstrap 5.3)

## 🚀 Como Usar

### 1. Acesso pela URL
```
http://localhost:8000/marcasvendas/dashboard
```

### 2. Requisitos
- Usuário deve estar logado
- Sessão deve conter `client_db_config` (configuração do banco do cliente)

### 3. Fluxo de Autenticação
```
Login → Cliente Selecionado → Dashboard de Marcas
```

## 🔒 Segurança

### Isolamento por Cliente
- ✅ Cada cliente acessa apenas seu banco de dados
- ✅ Configuração em `$_SESSION['client_db_config']`
- ✅ Autenticação verificada em cada requisição

### Validações
- Usuário autenticado
- Conexão com banco do cliente válida
- Sanitização de parâmetros SQL

## 📊 Estrutura SQL

O sistema utiliza as seguintes tabelas:
- `dm_produto` - Produtos e marcas
- `dm_orcamento_vendas_consolidadas` - Vendas consolidadas
- Campos principais: `cd_marca`, `ds_marca`, `cd_cpl_tamanho`

### Query Base
```sql
SELECT 
    dm_produto.cd_marca,
    dm_produto.ds_marca,
    COUNT(DISTINCT dm_venda.cd_lanc_cpl) as total_vendas,
    SUM(dm_venda.qtde_produto) as quantidade_vendida,
    SUM(dm_venda.vl_tot_it - dm_venda.vl_devol_proporcional) as valor_total
FROM dm_produto
INNER JOIN dm_orcamento_vendas_consolidadas dm_venda
    ON dm_venda.cd_cpl_tamanho = dm_produto.cd_cpl_tamanho
WHERE dm_venda.dt_vd >= CURRENT_DATE - INTERVAL 'N days'
GROUP BY dm_produto.cd_marca, dm_produto.ds_marca
ORDER BY quantidade_vendida DESC
LIMIT N
```

## 🎨 Interface

### Controles Disponíveis
1. **Período**: Selecione o intervalo de dias para análise
2. **Top Marcas**: Quantidade de marcas exibidas
3. **Intervalo de Atualização**: Frequência de refresh dos dados
4. **Botão Atualizar**: Atualização manual imediata

### Gráficos
1. **Quantidade Vendida** (Barras azuis)
2. **Valor Total em R$** (Barras verdes)
3. **Total de Vendas** (Linha laranja)

### Indicadores
- 🟢 Pulse verde: Atualização automática ativa
- 🕐 Timestamp: Última atualização dos dados

## 🔧 Customizações Possíveis

### Adicionar Filtro por Filial
No frontend (dashboard.php):
```javascript
const cdFilial = document.getElementById('filialSelect').value;
const response = await fetch(`/api/marcas_vendas.php?periodo=${periodo}&limite=${limite}&cd_filial=${cdFilial}`);
```

### Alterar Cores dos Gráficos
Edite as constantes no JavaScript:
```javascript
backgroundColor: 'rgba(54, 162, 235, 0.6)',
borderColor: 'rgba(54, 162, 235, 1)',
```

### Adicionar Mais Métricas
No endpoint API, adicione novos campos na query e retorne no JSON.

## 🐛 Troubleshooting

### Erro: "Usuário não autenticado"
- Verifique se o login foi realizado
- Confirme se `$_SESSION['usuario_logado']` é `true`

### Erro: "Configuração de banco de dados não encontrada"
- Certifique-se de que `$_SESSION['client_db_config']` está definido
- Verifique o processo de seleção de cliente após login

### Gráficos não atualizam
- Abra o Console do navegador (F12)
- Verifique erros de JavaScript
- Confirme que a API retorna status 200

### CDN bloqueado (Edge)
Se o Chart.js não carregar:
1. Desabilite Tracking Prevention
2. Ou baixe Chart.js localmente

## 📈 Melhorias Futuras

- [ ] Exportar dados para Excel/PDF
- [ ] Comparação entre períodos
- [ ] Alertas de queda/alta de vendas
- [ ] Filtro por categoria de produto
- [ ] Dashboard mobile otimizado
- [ ] Notificações push
- [ ] Gráficos de pizza/donut
- [ ] Análise de tendências

## 🎯 Diferencial do Sistema

Diferente do projeto sys-grafico original:
- ✅ **Atualização Parcial**: Só os gráficos são atualizados, não a página inteira
- ✅ **Multi-Tenant**: Isolamento por cliente/banco
- ✅ **Foco em Marcas**: Análise específica de marcas em vez de produtos
- ✅ **Tempo Real**: Intervalo configurável de 10s a 5min
- ✅ **API RESTful**: Arquitetura moderna e escalável

## 📞 Suporte

Para questões ou melhorias, consulte:
- Documentação do Chart.js: https://www.chartjs.org/
- Bootstrap 5: https://getbootstrap.com/
- API REST: `/api/marcas_vendas.php`
