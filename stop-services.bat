@echo off
echo ================================================
echo     HL7 SmartWard Integration Service Stopper
echo ================================================
echo.
echo Stopping all services...
echo.

echo [1/3] Stopping Laravel Backend (Port 8000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do (
    echo Killing process %%a on port 8000
    taskkill /f /pid %%a >nul 2>&1
)

echo [2/3] Stopping Vue.js Frontend (Port 3000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :3000') do (
    echo Killing process %%a on port 3000
    taskkill /f /pid %%a >nul 2>&1
)

echo [3/3] Stopping ADT Integrator (Port 9000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :9000') do (
    echo Killing process %%a on port 9000
    taskkill /f /pid %%a >nul 2>&1
)

REM Also close any command windows with our service titles
taskkill /fi "WINDOWTITLE eq Laravel Backend*" /f >nul 2>&1
taskkill /fi "WINDOWTITLE eq Vue.js Frontend*" /f >nul 2>&1
taskkill /fi "WINDOWTITLE eq ADT Integrator*" /f >nul 2>&1

echo.
echo ================================================
echo All services stopped successfully!
echo.
echo Ports 3000, 8000, and 9000 are now free.
echo ================================================
pause 