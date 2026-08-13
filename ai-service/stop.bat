@echo off
echo Stop GBI Pondok Daud AI Service port 5002...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :5002 ^| findstr LISTENING') do (
    taskkill /PID %%a /F >nul 2>&1
    echo [OK] Proses %%a dihentikan.
)
pause
