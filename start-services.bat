@echo off
echo ================================================
echo     HL7 SmartWard Integration Service Starter
echo ================================================
echo.
echo Starting all required services...
echo.

REM Check if we're in the correct directory
if not exist "artisan" (
    echo ERROR: This script must be run from the Laravel project root directory
    echo Current directory: %CD%
    echo Please navigate to the smartward3.4lte directory and run this script again
    pause
    exit /b 1
)

echo [1/3] Starting Laravel Backend (Port 8000)...
start "Laravel Backend" cmd /k "echo Laravel Backend Starting... && php artisan serve --host=localhost --port=8000"

echo [2/3] Starting Vue.js Frontend (Port 3000)...
start "Vue.js Frontend" cmd /k "echo Vue.js Frontend Starting... && cd adt\frontend && npm run dev"

echo [3/3] Starting ADT Integrator Backend (Port 9000)...
start "ADT Integrator" cmd /k "echo ADT Integrator Starting... && cd adt && python -m uvicorn app.main:app --host localhost --port 9000"

echo.
echo ================================================
echo All services are starting up...
echo.
echo Services will be available at:
echo   - Laravel Backend:     http://localhost:8000
echo   - Vue.js Frontend:     http://localhost:3000  
echo   - ADT Integrator:      http://localhost:9000
echo.
echo Integration Flow:
echo   Frontend (3000) -^> ADT Integrator (9000) -^> Laravel (8000)
echo.
echo Press any key to open the frontend in your browser...
pause

REM Open the frontend in default browser
start http://localhost:3000

echo.
echo ================================================
echo All services started successfully!
echo.
echo To stop all services:
echo   - Close the individual command windows
echo   - Or run: stop-services.bat
echo ================================================
pause 