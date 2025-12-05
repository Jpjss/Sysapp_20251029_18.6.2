$ErrorActionPreference = "Stop"

Write-Host "=== CONFIGURACAO DO BANCO LOCAL SYSAPP ===" -ForegroundColor Cyan
Write-Host ""

# Solicita credenciais do PostgreSQL local
Write-Host "Digite as credenciais do PostgreSQL LOCAL:" -ForegroundColor Yellow
$pgHost = Read-Host "Host (pressione Enter para 'localhost')"
if ([string]::IsNullOrWhiteSpace($pgHost)) { $pgHost = "localhost" }

$pgPort = Read-Host "Porta (pressione Enter para '5432')"
if ([string]::IsNullOrWhiteSpace($pgPort)) { $pgPort = "5432" }

$pgUser = Read-Host "Usuario (pressione Enter para 'postgres')"
if ([string]::IsNullOrWhiteSpace($pgUser)) { $pgUser = "postgres" }

$pgPass = Read-Host "Senha do usuario $pgUser" -AsSecureString
$pgPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($pgPass))

Write-Host ""
Write-Host "Testando conexao com PostgreSQL..." -ForegroundColor Yellow

# Testa conexão
$env:PGPASSWORD = $pgPassPlain
$testConn = & psql -h $pgHost -p $pgPort -U $pgUser -d postgres -c "SELECT version();" 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host "Conexao bem-sucedida!" -ForegroundColor Green
    Write-Host ""
    
    # Verifica se banco sysapp existe
    Write-Host "Verificando se banco 'sysapp' existe..." -ForegroundColor Yellow
    $checkDb = & psql -h $pgHost -p $pgPort -U $pgUser -d postgres -t -c "SELECT 1 FROM pg_database WHERE datname='sysapp';" 2>&1
    
    if ($checkDb -match "1") {
        Write-Host "Banco 'sysapp' ja existe!" -ForegroundColor Green
        $createDb = $false
    } else {
        Write-Host "Banco 'sysapp' NAO existe" -ForegroundColor Yellow
        $resposta = Read-Host "Deseja criar o banco 'sysapp'? (S/N)"
        if ($resposta -eq "S" -or $resposta -eq "s") {
            Write-Host "Criando banco 'sysapp'..." -ForegroundColor Yellow
            & psql -h $pgHost -p $pgPort -U $pgUser -d postgres -c "CREATE DATABASE sysapp;" 2>&1
            if ($LASTEXITCODE -eq 0) {
                Write-Host "Banco criado com sucesso!" -ForegroundColor Green
                $createDb = $true
            } else {
                Write-Host "Erro ao criar banco!" -ForegroundColor Red
                exit 1
            }
        } else {
            Write-Host "Operacao cancelada" -ForegroundColor Red
            exit 1
        }
    }
    
    # Executa schema
    Write-Host ""
    Write-Host "Executando schema do banco..." -ForegroundColor Yellow
    & psql -h $pgHost -p $pgPort -U $pgUser -d sysapp -f "database_schema.sql" 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Schema executado com sucesso!" -ForegroundColor Green
        
        # Atualiza config/database.php
        Write-Host ""
        Write-Host "Atualizando config/database.php..." -ForegroundColor Yellow
        
        $configPath = "config\database.php"
        $config = Get-Content $configPath -Raw
        
        $config = $config -replace "private \`$host = '[^']*';", "private `$host = '$pgHost';"
        $config = $config -replace "private \`$port = '[^']*';", "private `$port = '$pgPort';"
        $config = $config -replace "private \`$username = '[^']*';", "private `$username = '$pgUser';"
        $config = $config -replace "private \`$password = '[^']*';", "private `$password = '$pgPassPlain';"
        
        $config | Set-Content $configPath -NoNewline
        
        Write-Host "Configuracao atualizada!" -ForegroundColor Green
        Write-Host ""
        Write-Host "=== CONFIGURACAO CONCLUIDA COM SUCESSO ===" -ForegroundColor Green
        Write-Host ""
        Write-Host "Agora voce pode cadastrar bancos de empresas no sistema!" -ForegroundColor Cyan
    } else {
        Write-Host "Erro ao executar schema!" -ForegroundColor Red
        exit 1
    }
    
} else {
    Write-Host "Erro ao conectar!" -ForegroundColor Red
    Write-Host "Verifique as credenciais e tente novamente" -ForegroundColor Yellow
    exit 1
}

Remove-Item Env:\PGPASSWORD
