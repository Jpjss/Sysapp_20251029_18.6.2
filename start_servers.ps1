# Script para iniciar o servidor PHP e Next.js simultaneamente
Write-Host "🚀 Iniciando servidores do SysApp..." -ForegroundColor Cyan
Write-Host ""

# Para o servidor PHP se já estiver rodando
Write-Host "📌 Parando servidor PHP anterior..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Inicia servidor PHP em background
Write-Host "🔧 Iniciando servidor PHP na porta 8000..." -ForegroundColor Green
$phpJob = Start-Job -ScriptBlock {
    Set-Location "C:\Users\Lenovo01\OneDrive\Área de Trabalho\Projeto\Sysapp 2025\Sysapp_20251029_18.6.2"
    php -S localhost:8000 router.php
}

Start-Sleep -Seconds 2

# Verifica se PHP está rodando
$phpProcess = Get-Process php -ErrorAction SilentlyContinue
if ($phpProcess) {
    Write-Host "✅ Servidor PHP iniciado com sucesso!" -ForegroundColor Green
    Write-Host "   Backend API: http://localhost:8000/api" -ForegroundColor Cyan
} else {
    Write-Host "❌ Erro ao iniciar servidor PHP" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🔧 Iniciando servidor Next.js na porta 3000..." -ForegroundColor Green
Write-Host ""

# Inicia Next.js (isso ficará em foreground)
pnpm dev

# Quando Next.js for encerrado, para o PHP também
Write-Host ""
Write-Host "📌 Parando servidor PHP..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
Remove-Job -Job $phpJob -Force -ErrorAction SilentlyContinue

Write-Host "✅ Servidores encerrados" -ForegroundColor Green
