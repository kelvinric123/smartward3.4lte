# HL7 SmartWard Integration Service Starter (PowerShell)
# Run this script from the Laravel project root directory

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "    HL7 SmartWard Integration Service Starter" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Function to check if a port is available
function Test-Port {
    param([int]$Port)
    $connection = Test-NetConnection -ComputerName localhost -Port $Port -WarningAction SilentlyContinue
    return $connection.TcpTestSucceeded
}

# Function to kill process on port
function Stop-ProcessOnPort {
    param([int]$Port)
    $processes = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess
    foreach ($process in $processes) {
        try {
            Stop-Process -Id $process -Force -ErrorAction SilentlyContinue
            Write-Host "Stopped process $process on port $Port" -ForegroundColor Yellow
        } catch {
            # Process might already be stopped
        }
    }
}

# Check if we're in the correct directory
if (-not (Test-Path "artisan")) {
    Write-Host "ERROR: This script must be run from the Laravel project root directory" -ForegroundColor Red
    Write-Host "Current directory: $(Get-Location)" -ForegroundColor Red
    Write-Host "Please navigate to the smartward3.4lte directory and run this script again" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "Starting all required services..." -ForegroundColor Green
Write-Host ""

# Check and stop existing processes on our ports
$ports = @(3000, 8000, 9000)
foreach ($port in $ports) {
    if (Test-Port $port) {
        Write-Host "Port $port is in use. Stopping existing process..." -ForegroundColor Yellow
        Stop-ProcessOnPort $port
        Start-Sleep -Seconds 2
    }
}

Write-Host "[1/3] Starting Laravel Backend (Port 8000)..." -ForegroundColor Blue
$laravelProcess = Start-Process powershell -ArgumentList "-NoExit", "-Command", "Write-Host 'Laravel Backend Starting...' -ForegroundColor Green; php artisan serve --host=localhost --port=8000" -WindowStyle Normal -PassThru

Write-Host "[2/3] Starting Vue.js Frontend (Port 3000)..." -ForegroundColor Blue
$frontendProcess = Start-Process powershell -ArgumentList "-NoExit", "-Command", "Write-Host 'Vue.js Frontend Starting...' -ForegroundColor Green; Set-Location 'adt\frontend'; npm run dev" -WindowStyle Normal -PassThru

Write-Host "[3/3] Starting ADT Integrator Backend (Port 9000)..." -ForegroundColor Blue
$adtProcess = Start-Process powershell -ArgumentList "-NoExit", "-Command", "Write-Host 'ADT Integrator Starting...' -ForegroundColor Green; Set-Location 'adt'; python -m uvicorn app.main:app --host localhost --port 9000" -WindowStyle Normal -PassThru

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "All services are starting up..." -ForegroundColor Green
Write-Host ""
Write-Host "Services will be available at:" -ForegroundColor White
Write-Host "  - Laravel Backend:     http://localhost:8000" -ForegroundColor Gray
Write-Host "  - Vue.js Frontend:     http://localhost:3000" -ForegroundColor Gray
Write-Host "  - ADT Integrator:      http://localhost:9000" -ForegroundColor Gray
Write-Host ""
Write-Host "Integration Flow:" -ForegroundColor White
Write-Host "  Frontend (3000) -> ADT Integrator (9000) -> Laravel (8000)" -ForegroundColor Gray
Write-Host ""

# Wait for services to start
Write-Host "Waiting for services to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Check if services are running
Write-Host "Checking service status..." -ForegroundColor Yellow
$servicesReady = $true

if (-not (Test-Port 8000)) {
    Write-Host "⚠️  Laravel Backend (port 8000) not responding" -ForegroundColor Red
    $servicesReady = $false
} else {
    Write-Host "✅ Laravel Backend (port 8000) is running" -ForegroundColor Green
}

if (-not (Test-Port 3000)) {
    Write-Host "⚠️  Vue.js Frontend (port 3000) not responding" -ForegroundColor Red
    $servicesReady = $false
} else {
    Write-Host "✅ Vue.js Frontend (port 3000) is running" -ForegroundColor Green
}

if (-not (Test-Port 9000)) {
    Write-Host "⚠️  ADT Integrator (port 9000) not responding" -ForegroundColor Red
    $servicesReady = $false
} else {
    Write-Host "✅ ADT Integrator (port 9000) is running" -ForegroundColor Green
}

Write-Host ""
if ($servicesReady) {
    Write-Host "🎉 All services started successfully!" -ForegroundColor Green
    Write-Host ""
    $openBrowser = Read-Host "Open frontend in browser? (y/n)"
    if ($openBrowser -eq "y" -or $openBrowser -eq "Y") {
        Start-Process "http://localhost:3000"
    }
} else {
    Write-Host "⚠️  Some services may not have started properly. Check the individual windows for errors." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "To stop all services:" -ForegroundColor White
Write-Host "  - Close the individual PowerShell windows" -ForegroundColor Gray
Write-Host "  - Or run: .\stop-services.ps1" -ForegroundColor Gray
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit this window (services will continue running)" 