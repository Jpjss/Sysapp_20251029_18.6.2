# Guia de Teste - Correção de XMLs NFe

## 📋 Arquivos de Teste Criados

### 1. `nfe_teste_1.xml` - Com divergência SEM desconto
- **vNF Total**: R$ 150,50
- **Soma dos produtos**: R$ 150,00
- **Divergência**: R$ 0,50
- **Esperado**: Ajuste no último item (Produto 2)

### 2. `nfe_teste_2_desconto.xml` - Com divergência COM desconto
- **vNF Total**: R$ 195,75
- **Soma líquida**: R$ 195,00 (210 - 15 de desconto)
- **Divergência**: R$ 0,75
- **Esperado**: Ajuste no desconto do último item

### 3. `nfe_teste_3_correto.xml` - SEM divergência
- **vNF Total**: R$ 100,00
- **Soma dos produtos**: R$ 100,00
- **Divergência**: R$ 0,00
- **Esperado**: Nenhuma correção necessária

## 🧪 Como Testar

### Passo 1: Acesse a página
```
http://localhost:8000/xml/index
```

### Passo 2: Selecione os arquivos
1. Clique em "Selecionar arquivos XML"
2. Navegue até: `C:\Users\Lenovo01\OneDrive\Área de Trabalho\Projeto\Sysapp 2025\Sysapp_20251029_18.6.2\public\test_xmls`
3. Selecione todos os 3 arquivos XML

### Passo 3: Processe
1. Clique em "Iniciar Correção"
2. Acompanhe o progresso
3. Verifique os logs

### Passo 4: Valide os Resultados Esperados

#### 📊 Estatísticas Esperadas:
- **Total Processados**: 3
- **Corrigidos**: 2 (teste_1 e teste_2_desconto)
- **Sem Divergência**: 1 (teste_3_correto)
- **Erros**: 0

#### 📝 Logs Esperados:
1. ✅ **nfe_teste_1.xml**: Corrigido: diferença ajustada R$ 0.50
2. ✅ **nfe_teste_2_desconto.xml**: Corrigido: diferença ajustada R$ 0.75
3. ℹ️ **nfe_teste_3_correto.xml**: Sem divergência

### Passo 5: Download e Verificação
1. Clique em "Baixar XMLs Corrigidos"
2. Extraia o ZIP
3. Abra os XMLs corrigidos em um editor XML
4. Verifique se os valores foram ajustados corretamente

## ✅ Checklist de Validação

- [ ] Página carrega sem erros
- [ ] Upload de múltiplos arquivos funciona
- [ ] Barra de progresso aparece
- [ ] Logs são exibidos corretamente
- [ ] Estatísticas batem com o esperado
- [ ] Botão de download aparece
- [ ] Download do ZIP funciona
- [ ] XMLs corrigidos estão no ZIP
- [ ] Valores foram ajustados corretamente

## 🔍 Verificação Manual dos Valores

### nfe_teste_1.xml (ANTES)
```xml
<vProd>50.00</vProd>  <!-- Último item -->
<vUnCom>50.00</vUnCom>
<vUnTrib>50.00</vUnTrib>
```

### nfe_teste_1.xml (DEPOIS - esperado)
```xml
<vProd>50.50</vProd>  <!-- Ajustado +0.50 -->
<vUnCom>50.50</vUnCom>
<vUnTrib>50.50</vUnTrib>
```

### nfe_teste_2_desconto.xml (ANTES)
```xml
<vDesc>5.00</vDesc>  <!-- Último item -->
```

### nfe_teste_2_desconto.xml (DEPOIS - esperado)
```xml
<vDesc>4.25</vDesc>  <!-- Ajustado -0.75 -->
```

## 🐛 Possíveis Problemas

### Se não funcionar:
1. Verifique se o servidor PHP está rodando
2. Abra o Console do navegador (F12) e veja se há erros JavaScript
3. Verifique os logs do servidor PHP no terminal
4. Confirme que a pasta `public/uploads/xml_temp/` tem permissões de escrita

## 💡 Teste Adicional

Para testar com seus próprios XMLs reais:
1. Faça backup dos XMLs originais
2. Faça upload deles no sistema
3. Compare os valores antes e depois
4. Valide em um sistema de NFe se necessário
