@echo off
REM Starts PHP built-in server (Windows)
REM Usage: Double-click or run in CMD from project root
SET PHP_EXE=C:\xampp\php\php.exe
IF NOT EXIST "%PHP_EXE%" (
  echo PHP not found at %PHP_EXE%
  exit /b 1
)

REM Stop existing PHP process (if any)
taskkill /F /IM php.exe >nul 2>&1

cd /d %~dp0
start "PHP Server" "%PHP_EXE%" -S localhost:8000 router.php
echo PHP server started: http://localhost:8000
