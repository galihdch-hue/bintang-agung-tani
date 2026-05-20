@echo off
echo ========================================
echo   Bintang Agung Tani - Mobile Dev Mode
echo ========================================
echo.

REM Get local IP
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /r "IPv4.*192\."') do set LOCAL_IP=%%a
set LOCAL_IP=%LOCAL_IP: =%

if "%LOCAL_IP%"=="" (
    echo [ERROR] Gagal mendeteksi IP lokal. Pastikan terhubung ke WiFi.
    pause
    exit /b 1
)

echo [1/4] IP Lokal: %LOCAL_IP%
echo.

REM Update Flutter API URL
echo [2/4] Update API URL ke http://%LOCAL_IP%:8000/api...
powershell -Command "(Get-Content 'mobile_app\lib\services\api_service.dart') -replace 'http://localhost:8000/api', 'http://%LOCAL_IP%:8000/api' | Set-Content 'mobile_app\lib\services\api_service.dart'"
echo.

REM Update Flutter theme asset URL
echo [3/4] Update asset URL helper...
powershell -Command "(Get-Content 'mobile_app\lib\theme.dart') -replace 'http://localhost:8000', 'http://%LOCAL_IP%:8000' | Set-Content 'mobile_app\lib\theme.dart'"
echo.

REM Start Laravel
echo [4/4] Starting Laravel server on 0.0.0.0:8000...
echo.
echo ========================================
echo   Laravel: http://%LOCAL_IP%:8000
echo   Flutter: Buka mobile_app lalu jalankan:
echo            cd mobile_app ^&^& flutter run
echo ========================================
echo.
echo Tekan Ctrl+C untuk stop Laravel server.
echo.

cd /d "%~dp0"
php artisan serve --host=0.0.0.0 --port=8000
