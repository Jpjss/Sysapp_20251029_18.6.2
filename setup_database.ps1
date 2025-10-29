# Script de Configuração Automática do Banco de Dados SysApp
# Execute este script no PowerShell como Administrador

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Configuração do Banco de Dados SysApp" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Solicitar senha do PostgreSQL
$pgPassword = Read-Host "Digite a senha do usuário 'postgres' do PostgreSQL" -AsSecureString
$pgPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($pgPassword))

# Caminho do psql
$psqlPath = "C:\Program Files\PostgreSQL\17\bin\psql.exe"
if (-not (Test-Path $psqlPath)) {
    $psqlPath = "C:\Program Files\PostgreSQL\16\bin\psql.exe"
}
if (-not (Test-Path $psqlPath)) {
    $psqlPath = "C:\Program Files\PostgreSQL\15\bin\psql.exe"
}

if (-not (Test-Path $psqlPath)) {
    Write-Host "[ERRO] PostgreSQL nao encontrado!" -ForegroundColor Red
    Write-Host "Instale o PostgreSQL primeiro." -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "[OK] PostgreSQL encontrado: $psqlPath" -ForegroundColor Green

# Definir variável de ambiente com a senha
$env:PGPASSWORD = $pgPasswordPlain

# Testar conexão
Write-Host ""
Write-Host "Testando conexao com PostgreSQL..." -ForegroundColor Yellow
& $psqlPath -U postgres -h localhost -p 5432 -c "SELECT version();" | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Nao foi possivel conectar ao PostgreSQL!" -ForegroundColor Red
    Write-Host "Verifique se:" -ForegroundColor Yellow
    Write-Host "  1. O serviço PostgreSQL está rodando" -ForegroundColor Yellow
    Write-Host "  2. A senha está correta" -ForegroundColor Yellow
    Write-Host "  3. O usuário 'postgres' existe" -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "[OK] Conexao estabelecida!" -ForegroundColor Green

# Verificar se o banco sysapp ja existe
Write-Host ""
Write-Host "Verificando se o banco 'sysapp' ja existe..." -ForegroundColor Yellow
$dbExists = & $psqlPath -U postgres -h localhost -p 5432 -t -c "SELECT 1 FROM pg_database WHERE datname='sysapp';"

if ($dbExists -match "1") {
    Write-Host "[AVISO] Banco 'sysapp' ja existe!" -ForegroundColor Yellow
    $recreate = Read-Host "Deseja recriar o banco? (S/N) [Isso apagará todos os dados]"
    
    if ($recreate -eq "S" -or $recreate -eq "s") {
        Write-Host "Removendo banco existente..." -ForegroundColor Yellow
        & $psqlPath -U postgres -h localhost -p 5432 -c "DROP DATABASE sysapp;" | Out-Null
        Write-Host "[OK] Banco removido!" -ForegroundColor Green
    } else {
        Write-Host "Cancelado pelo usuário." -ForegroundColor Yellow
        Read-Host "Pressione Enter para sair"
        exit 0
    }
}

# Criar banco de dados
Write-Host ""
Write-Host "Criando banco de dados 'sysapp'..." -ForegroundColor Yellow
& $psqlPath -U postgres -h localhost -p 5432 -c "CREATE DATABASE sysapp WITH ENCODING 'UTF8';" | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERRO] Nao foi possivel criar banco de dados!" -ForegroundColor Red
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "[OK] Banco criado!" -ForegroundColor Green

# Executar script SQL
Write-Host ""
Write-Host "Executando script de criacao de tabelas..." -ForegroundColor Yellow
$scriptPath = Join-Path $PSScriptRoot "database_schema.sql"

if (-not (Test-Path $scriptPath)) {
    Write-Host "[ERRO] Arquivo database_schema.sql nao encontrado!" -ForegroundColor Red
    Read-Host "Pressione Enter para sair"
    exit 1
}

& $psqlPath -U postgres -h localhost -p 5432 -d sysapp -f $scriptPath | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "[AVISO] Houve avisos ao executar o script, mas as tabelas podem ter sido criadas." -ForegroundColor Yellow
} else {
    Write-Host "[OK] Tabelas criadas com sucesso!" -ForegroundColor Green
}

# Verificar tabelas criadas
Write-Host ""
Write-Host "Verificando tabelas criadas..." -ForegroundColor Yellow
$tables = & $psqlPath -U postgres -h localhost -p 5432 -d sysapp -t -c "SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name;"

if ($tables) {
    Write-Host "[OK] Tabelas encontradas:" -ForegroundColor Green
    $tables | ForEach-Object { Write-Host "  - $_" -ForegroundColor Cyan }
} else {
    Write-Host "[AVISO] Nenhuma tabela encontrada!" -ForegroundColor Yellow
}

# Atualizar config.php
Write-Host ""
Write-Host "Atualizando arquivo de configuracao..." -ForegroundColor Yellow
$configPath = Join-Path $PSScriptRoot "config\config.php"

if (Test-Path $configPath) {
    $configContent = Get-Content $configPath -Raw
    $configContent = $configContent -replace "define\('DB_PASS', '.*?'\);", "define('DB_PASS', '$pgPasswordPlain');"
    Set-Content -Path $configPath -Value $configContent -NoNewline
    Write-Host "[OK] Configuracao atualizada!" -ForegroundColor Green
} else {
    Write-Host "[AVISO] Arquivo config.php nao encontrado!" -ForegroundColor Yellow
}

# Limpar variável de senha
$env:PGPASSWORD = ""

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "[OK] Configuracao concluida com sucesso!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Credenciais padrao:" -ForegroundColor Cyan
Write-Host "  Usuário: admin" -ForegroundColor White
Write-Host "  Senha: admin" -ForegroundColor White
Write-Host ""
Write-Host "Proximos passos:" -ForegroundColor Yellow
Write-Host "  1. Inicie o Apache no XAMPP" -ForegroundColor White
Write-Host "  2. Acesse http://localhost/Sysapp_20251029_18.6.2" -ForegroundColor White
Write-Host "  3. Faça login com admin/admin" -ForegroundColor White
Write-Host ""
Read-Host "Pressione Enter para sair"
