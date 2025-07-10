@echo off
REM PostGrid Quick Release Script
REM Usage: release.bat 0.1.2 "Bug fixes and improvements"

if "%~1"=="" goto usage
if "%~2"=="" goto usage

echo.
echo Releasing PostGrid v%1...
echo.

REM Run the PowerShell release script
powershell -ExecutionPolicy Bypass -File "%~dp0release.ps1" -Version %1 -Message %2
goto end

:usage
echo Usage: release.bat VERSION "MESSAGE"
echo Example: release.bat 0.1.2 "Bug fixes and improvements"

:end
