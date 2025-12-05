# Start PHP built-in server with router.php
# Usage: Right-click -> Run with PowerShell OR Open PowerShell in project folder and run: .\start_php_server.ps1

# Use PHP from PATH (installed via winget)
$phpExe = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $phpExe) {
    Write-Host "PHP not found in PATH" -ForegroundColor Red
    Write-Host "Please install PHP or add it to PATH" -ForegroundColor Yellow
    exit 1
}

# Stop any existing php process (silently)
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
Start-Sleep -Milliseconds 300

# Start the server in a new window
Start-Process -FilePath $phpExe -ArgumentList '-S localhost:8000 router.php' -WorkingDirectory $PSScriptRoot -WindowStyle Normal
Write-Host "PHP server started: http://localhost:8000" -ForegroundColor Green
