# Otimizações para Correção de XMLs em Massa

## 📊 Capacidade Aumentada: 3.000 XMLs

O sistema foi otimizado para processar até **3.000 arquivos XML de uma vez**.

## ⚙️ Otimizações Implementadas

### 1. **Limites de Memória e Tempo**
- Memory limit: **512MB** (antes: 128MB padrão)
- Execution time: **600 segundos** (10 minutos)
- Upload max filesize: **500MB**
- Post max size: **600MB**
- Max file uploads: **5000 arquivos**

### 2. **Processamento em Lotes**
- XMLs processados em lotes de **100 arquivos** por vez
- Garbage collector executado a cada **50 arquivos**
- Logs detalhados apenas para primeiros e últimos **50 arquivos** (economia de memória)

### 3. **Otimizações de XML**
- Uso de flags `LIBXML_COMPACT` e `LIBXML_NOBLANKS` para reduzir memória
- Desabilitada formatação de saída (`formatOutput = false`)
- Liberação explícita de memória após cada processamento

### 4. **Limpeza Automática**
- Arquivos temporários com mais de **1 hora** são removidos automaticamente
- Evita acúmulo de arquivos no servidor

## 🚀 Como Usar

### Upload de Múltiplos XMLs
1. Acesse a página de Correção de XMLs
2. Selecione até 3.000 arquivos XML
3. Clique em "Iniciar Correção"
4. Aguarde o processamento (pode levar até 10 minutos)
5. Baixe o ZIP com os arquivos corrigidos

### Monitoramento
Durante o processamento, você verá:
- Total de arquivos processados
- Quantidade de arquivos corrigidos
- Arquivos sem divergência
- Erros encontrados
- Logs dos primeiros e últimos 50 arquivos

## 📋 Requisitos do Servidor

Para garantir o funcionamento ideal:

```ini
; php.ini ou .user.ini
upload_max_filesize = 500M
post_max_size = 600M
memory_limit = 512M
max_execution_time = 600
max_input_time = 600
max_file_uploads = 5000
```

## 🔧 Configuração Manual (se necessário)

Se as configurações não forem aplicadas automaticamente, edite o arquivo `php.ini`:

### Windows
1. Localize: `C:\php\php.ini`
2. Edite as configurações acima
3. Reinicie o servidor PHP

### Linux
1. Localize: `/etc/php/8.x/apache2/php.ini` ou `/etc/php/8.x/fpm/php.ini`
2. Edite as configurações acima
3. Reinicie: `sudo systemctl restart apache2` ou `sudo systemctl restart php-fpm`

## ⚡ Performance Esperada

Com as otimizações:
- **100 XMLs**: ~30-60 segundos
- **500 XMLs**: ~2-4 minutos
- **1000 XMLs**: ~4-7 minutos
- **3000 XMLs**: ~8-10 minutos

*Tempo varia conforme tamanho dos XMLs e hardware do servidor*

## 🐛 Troubleshooting

### "Erro de timeout"
- Aumente `max_execution_time` no php.ini
- Processe em lotes menores (divida os 3000 em 2-3 uploads)

### "Erro de memória"
- Aumente `memory_limit` para 1024M no php.ini
- Verifique se há espaço em disco suficiente

### "Erro ao fazer upload"
- Verifique `upload_max_filesize` e `post_max_size`
- Confirme que `max_file_uploads` está configurado

## 📁 Estrutura de Arquivos

```
controllers/
  └── XmlController.php         # Controller otimizado
config/
  └── xml_config.php            # Configurações automáticas
public/uploads/
  └── xml_temp/                 # Arquivos temporários
php_xml_config.ini              # Referência de configurações
```

## ✅ Validação

Para testar com 3000 XMLs:
1. Prepare 3000 arquivos XML de NFe
2. Selecione todos no upload
3. Observe o processamento concluir sem erros
4. Baixe o ZIP resultante
5. Verifique os logs para confirmar sucesso

---

**Desenvolvido para SysApp v18.6.2**
*Última atualização: Dezembro 2025*
