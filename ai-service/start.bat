@echo off
setlocal EnableExtensions

echo ========================================
echo   GBI Pondok Daud AI Service
echo   Recommendation + RAG Chatbot - Port 5002
echo ========================================

cd /d "%~dp0"

curl.exe -s http://127.0.0.1:5002/health | findstr /C:"\"status\":\"ok\"" >nul 2>&1
if not errorlevel 1 (
    echo.
    echo [INFO] AI Service SUDAH BERJALAN di http://127.0.0.1:5002
    echo [INFO] Tidak perlu start ulang. Refresh halaman browser.
    echo.
    pause
    exit /b 0
)

if not exist "venv\Scripts\python.exe" (
    echo [INFO] Membuat virtual environment...
    python -m venv venv
)

call "venv\Scripts\activate.bat"

if not exist "venv\Lib\site-packages\fastapi" (
    echo [INFO] Menginstall dependensi AI...
    python -m pip install --upgrade pip
    pip install -r requirements.txt
)

echo [INFO] Menjalankan AI Service di http://127.0.0.1:5002
python app.py

if errorlevel 1 (
    echo [ERROR] Service berhenti. Jalankan stop.bat lalu coba lagi.
    pause
)

endlocal
