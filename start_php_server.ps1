# Start PHP built-in server with router.php
# Usage: Right-click -> Run with PowerShell OR Open PowerShell in project folder and run: .\start_php_server.ps1

$phpExe = 'C:\xampp\php\php.exe'
if (-not (Test-Path $phpExe)) {
    Write-Host "PHP not found at $phpExe" -ForegroundColor Red
    Write-Host "Update path in this script or install XAMPP" -ForegroundColor Yellow
    exit 1
}

# Stop any existing php process (silently)
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
Start-Sleep -Milliseconds 300

# Start the server in a new window
Start-Process -FilePath $phpExe -ArgumentList '-S localhost:8000 router.php' -WorkingDirectory $PSScriptRoot -WindowStyle Normal
Write-Host "PHP server started: http://localhost:8000" -ForegroundColor Green
