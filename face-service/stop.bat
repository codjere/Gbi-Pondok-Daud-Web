@echo off
echo ========================================
echo   Stop GBI Pondok Daud Face Recognition Service
echo ========================================

set FOUND=0
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :5001 ^| findstr LISTENING') do (
    echo [INFO] Menghentikan proses PID %%a di port 5001...
    taskkill /PID %%a /F >nul 2>&1
    set FOUND=1
)

if "%FOUND%"=="0" (
    echo [INFO] Tidak ada service yang berjalan di port 5001.
) else (
    echo [OK] Service dihentikan.
)

pause
