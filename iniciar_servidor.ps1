# Script para inicializar o servidor PHP com ambiente limpo

Write-Host "=== Iniciando Servidor SysApp ===" -ForegroundColor Cyan

# Para todos os processos PHP
Write-Host "Parando processos PHP existentes..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 2

# Navega para o diretório correto
$projectPath = "c:\Users\Lenovo01\OneDrive\Área de Trabalho\Projeto\Sysapp 2025\Sysapp_20251029_18.6.2"
Set-Location $projectPath
Write-Host "Diretório: $projectPath" -ForegroundColor Green

# Limpa o cache de arquivos
Write-Host "Limpando cache..." -ForegroundColor Yellow
php limpar_cache.php

# Inicia o servidor
Write-Host "`nIniciando servidor PHP em http://localhost:8000..." -ForegroundColor Green
Write-Host "Pressione Ctrl+C para parar o servidor" -ForegroundColor Yellow
Write-Host "=" * 50

php -S localhost:8000 router.php
