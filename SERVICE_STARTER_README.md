# HL7 SmartWard Integration Service Starter

This directory contains Windows scripts to easily start and stop all required services for the HL7 SmartWard Integration system.

## 🚀 Quick Start

### Option 1: Batch Scripts (Command Prompt)
```cmd
# Start all services
start-services.bat

# Stop all services  
stop-services.bat
```

### Option 2: PowerShell Scripts (Recommended)
```powershell
# Start all services
.\start-services.ps1

# Stop all services
.\stop-services.ps1
```

## 📋 What Gets Started

The scripts start these three services:

1. **Laravel Backend** (Port 8000)
   - Command: `php artisan serve --host=localhost --port=8000`
   - URL: http://localhost:8000

2. **Vue.js Frontend** (Port 3000)  
   - Command: `npm run dev` (in adt/frontend directory)
   - URL: http://localhost:3000

3. **ADT Integrator Backend** (Port 9000)
   - Command: `python -m uvicorn app.main:app --host localhost --port 9000` (in adt directory)
   - URL: http://localhost:9000

## 🔄 Integration Flow

```
Vue.js Frontend (3000) → ADT Integrator (9000) → Laravel Backend (8000)
```

## 📦 Prerequisites

Make sure you have installed:

- **PHP** (for Laravel)
- **Node.js & npm** (for Vue.js frontend)
- **Python** (for ADT integrator)
- **Composer** (Laravel dependencies)

## 🎯 Usage Instructions

### First Time Setup
1. Navigate to your project root directory (smartward3.4lte)
2. Ensure all dependencies are installed:
   ```cmd
   composer install
   cd adt/frontend && npm install
   cd ../.. && cd adt && pip install -r requirements.txt
   ```

### Starting Services
1. **Right-click** on `start-services.bat` or `start-services.ps1`
2. **Select "Run as Administrator"** (recommended for port management)
3. Wait for all services to start
4. The frontend will automatically open in your browser

### Stopping Services
1. **Double-click** `stop-services.bat` or `stop-services.ps1`
2. Or simply **close the individual service windows**

## 🖥️ Service Windows

Each service runs in its own command/PowerShell window:
- **Laravel Backend** - Shows Laravel server logs
- **Vue.js Frontend** - Shows Vite development server logs  
- **ADT Integrator** - Shows FastAPI/Uvicorn logs

## 🔧 Features

### PowerShell Scripts (Recommended)
- ✅ **Port conflict detection** - Automatically stops conflicting processes
- ✅ **Service health checks** - Verifies services are running
- ✅ **Colored output** - Easy to read status messages
- ✅ **Error handling** - Graceful failure management
- ✅ **Smart cleanup** - Properly stops related processes

### Batch Scripts
- ✅ **Simple execution** - Works on any Windows system
- ✅ **No permissions required** - Runs without admin rights
- ✅ **Broad compatibility** - Works on older Windows versions

## ❌ Troubleshooting

### Port Already in Use
If you get "port already in use" errors:
1. Run the **stop script** first
2. Or manually kill processes using Task Manager
3. Then run the **start script** again

### Permission Errors
- **Run as Administrator** for better port management
- Check Windows Firewall settings
- Ensure antivirus isn't blocking the processes

### Service Not Starting
1. Check if **PHP**, **Node.js**, and **Python** are in your PATH
2. Verify all dependencies are installed
3. Check the individual service windows for error messages

## 🎉 Success Indicators

When everything is working correctly:
- ✅ **Laravel Backend**: You can access http://localhost:8000
- ✅ **Vue.js Frontend**: You can access http://localhost:3000
- ✅ **ADT Integrator**: You can access http://localhost:9000
- ✅ **Integration**: Frontend shows "SUCCESS" status when sending HL7 messages

## 📝 Notes

- Services run in **development mode** with hot-reload enabled
- All logs are visible in their respective command windows  
- The frontend automatically forwards HL7 messages to Laravel via the ADT integrator
- CORS is pre-configured to allow communication between all services

## 🆘 Support

If you encounter issues:
1. Check the individual service windows for error messages
2. Verify all prerequisites are installed and in PATH
3. Try running services manually to isolate the problem
4. Check Windows Event Viewer for system-level errors 