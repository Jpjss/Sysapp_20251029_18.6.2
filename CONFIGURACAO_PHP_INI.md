# CONFIGURAÇÃO OBRIGATÓRIA DO PHP.INI

Para processar 3000 XMLs, você DEVE editar o php.ini do seu sistema.

## Localização do php.ini

Execute este comando para encontrar:
```powershell
php --ini
```

Ou verifique em:
- Windows: `C:\php\php.ini` ou `C:\Program Files\PHP\php.ini`
- Linux: `/etc/php/8.x/cli/php.ini`

## Configurações Obrigatórias

Edite o arquivo `php.ini` e adicione/modifique estas linhas:

```ini
; UPLOAD E POST
upload_max_filesize = 500M
post_max_size = 600M
max_file_uploads = 5000

; MEMÓRIA E TEMPO
memory_limit = 512M
max_execution_time = 600
max_input_time = 600

; GARBAGE COLLECTOR
zend.enable_gc = On
```

## Aplicar Configurações

### Windows
1. Edite `C:\php\php.ini`
2. Salve o arquivo
3. Reinicie o servidor PHP:
```powershell
Get-Process php | Stop-Process -Force
.\start_php_server.ps1
```

### Linux com Apache
```bash
sudo nano /etc/php/8.x/apache2/php.ini
# Edite as configurações
sudo systemctl restart apache2
```

### Linux com PHP-FPM
```bash
sudo nano /etc/php/8.x/fpm/php.ini
# Edite as configurações
sudo systemctl restart php8.x-fpm
```

## Verificar Configurações

Execute:
```powershell
php -i | Select-String "upload_max_filesize|post_max_size|max_file_uploads|memory_limit"
```

Ou acesse via web:
```php
<?php phpinfo(); ?>
```

## ⚠️ IMPORTANTE

**SEM ESTAS CONFIGURAÇÕES, o sistema só conseguirá processar até 20 arquivos por vez!**

O valor de `max_file_uploads` no php.ini é o único que não pode ser alterado via `ini_set()`.

---

Após aplicar as configurações, execute novamente o teste:
```powershell
php test_xml_capacity.php
```

E verifique que `Max File Uploads` mostra **5000** (não 20).
