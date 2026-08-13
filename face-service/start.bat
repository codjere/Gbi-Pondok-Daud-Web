@echo off
setlocal EnableExtensions

echo ========================================
echo   GBI Pondok Daud Face Recognition Service
echo   FaceNet - Port 5001
echo ========================================

cd /d "%~dp0"

curl.exe -s http://127.0.0.1:5001/health | findstr /C:"\"status\":\"ok\"" >nul 2>&1
if not errorlevel 1 (
    echo.
    echo [INFO] Service SUDAH BERJALAN di http://127.0.0.1:5001
    echo [INFO] Tidak perlu start ulang. Langsung refresh halaman registrasi wajah.
    echo.
    echo        Untuk restart, jalankan stop.bat lalu start.bat lagi.
    echo.
    pause
    exit /b 0
)

if not exist "venv\Scripts\python.exe" (
    echo [INFO] Membuat virtual environment...
    python -m venv venv
    if errorlevel 1 (
        echo [ERROR] Gagal membuat venv. Pastikan Python sudah terinstall.
        pause
        exit /b 1
    )
)

call "venv\Scripts\activate.bat"
if errorlevel 1 (
    echo [ERROR] Gagal mengaktifkan virtual environment.
    pause
    exit /b 1
)

if not exist "venv\Lib\site-packages\fastapi" (
    echo [INFO] Menginstall dependensi - pertama kali butuh waktu...
    python -m pip install --upgrade pip
    pip install fastapi "uvicorn[standard]" python-multipart numpy Pillow torch torchvision requests tqdm
    pip install facenet-pytorch --no-deps
    if errorlevel 1 (
        echo [ERROR] Gagal install dependensi.
        pause
        exit /b 1
    )
)

echo [INFO] Menjalankan service di http://127.0.0.1:5001
python app.py

if errorlevel 1 (
    echo.
    echo [ERROR] Service berhenti dengan error.
    echo [TIP] Port 5001 mungkin masih dipakai. Jalankan stop.bat lalu coba lagi.
    pause
)

endlocal
