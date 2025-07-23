# HL7 SmartWard Integration Service Stopper (PowerShell)
# Stops all services running on ports 3000, 8000, and 9000

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "    HL7 SmartWard Integration Service Stopper" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Function to check if a port is in use
function Test-Port {
    param([int]$Port)
    try {
        $connection = Test-NetConnection -ComputerName localhost -Port $Port -WarningAction SilentlyContinue
        return $connection.TcpTestSucceeded
    } catch {
        return $false
    }
}

# Function to stop processes on a specific port
function Stop-ProcessOnPort {
    param([int]$Port, [string]$ServiceName)
    
    Write-Host "[$ServiceName] Checking port $Port..." -ForegroundColor Blue
    
    try {
        $connections = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
        if ($connections) {
            $processIds = $connections | Select-Object -ExpandProperty OwningProcess -Unique
            
            foreach ($processId in $processIds) {
                try {
                    $process = Get-Process -Id $processId -ErrorAction SilentlyContinue
                    if ($process) {
                        Write-Host "  Stopping process: $($process.ProcessName) (PID: $processId)" -ForegroundColor Yellow
                        Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
                        Start-Sleep -Milliseconds 500
                    }
                } catch {
                    # Process might already be stopped
                }
            }
            Write-Host "  ✅ $ServiceName stopped" -ForegroundColor Green
        } else {
            Write-Host "  ℹ️  $ServiceName was not running" -ForegroundColor Gray
        }
    } catch {
        Write-Host "  ⚠️  Could not check $ServiceName" -ForegroundColor Yellow
    }
}

Write-Host "Stopping all services..." -ForegroundColor Red
Write-Host ""

# Stop services
Stop-ProcessOnPort 8000 "Laravel Backend"
Stop-ProcessOnPort 3000 "Vue.js Frontend" 
Stop-ProcessOnPort 9000 "ADT Integrator"

# Additional cleanup - close PowerShell windows that might be running our services
Write-Host ""
Write-Host "Cleaning up service windows..." -ForegroundColor Blue

$serviceProcesses = @(
    "php",
    "node", 
    "python"
)

foreach ($processName in $serviceProcesses) {
    try {
        $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
        if ($processes) {
            foreach ($process in $processes) {
                # Check if it's one of our service processes by looking at command line
                $commandLine = (Get-CimInstance Win32_Process -Filter "ProcessId = $($process.Id)" -ErrorAction SilentlyContinue).CommandLine
                
                if ($commandLine -and (
                    $commandLine -like "*artisan serve*" -or 
                    $commandLine -like "*npm run dev*" -or 
                    $commandLine -like "*uvicorn*" -or
                    $commandLine -like "*vite*"
                )) {
                    Write-Host "  Stopping $processName process (PID: $($process.Id))" -ForegroundColor Yellow
                    Stop-Process -Id $process.Id -Force -ErrorAction SilentlyContinue
                }
            }
        }
    } catch {
        # Process might not exist or already stopped
    }
}

# Final verification
Write-Host ""
Write-Host "Verifying all services are stopped..." -ForegroundColor Blue

$allStopped = $true
$ports = @(
    @{Port=8000; Name="Laravel Backend"},
    @{Port=3000; Name="Vue.js Frontend"},
    @{Port=9000; Name="ADT Integrator"}
)

foreach ($service in $ports) {
    if (Test-Port $service.Port) {
        Write-Host "  ⚠️  $($service.Name) (port $($service.Port)) is still running" -ForegroundColor Red
        $allStopped = $false
    } else {
        Write-Host "  ✅ $($service.Name) (port $($service.Port)) is stopped" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan

if ($allStopped) {
    Write-Host "🎉 All services stopped successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Ports 3000, 8000, and 9000 are now free." -ForegroundColor Gray
} else {
    Write-Host "⚠️  Some services may still be running." -ForegroundColor Yellow
    Write-Host "You may need to manually close remaining windows or restart your computer." -ForegroundColor Gray
}

Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit" 