@echo off
echo Iniciando servidores do SysApp...
echo.
powershell.exe -ExecutionPolicy Bypass -File "%~dp0start_servers.ps1"
pause
