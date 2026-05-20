@echo off
echo ========================================
echo   Restore ke Localhost (Development)
echo ========================================
echo.

echo [1/2] Restore API URL ke localhost...
powershell -Command "(Get-Content 'mobile_app\lib\services\api_service.dart') -replace 'http://[0-9.]+:8000/api', 'http://localhost:8000/api' | Set-Content 'mobile_app\lib\services\api_service.dart'"

echo [2/2] Restore asset URL helper...
powershell -Command "(Get-Content 'mobile_app\lib\theme.dart') -replace 'http://[0-9.]+:8000', 'http://localhost:8000' | Set-Content 'mobile_app\lib\theme.dart'"

echo.
echo ✅ Selesai! Sudah kembali ke localhost.
echo    Jalankan: php artisan serve
echo    Lalu: cd mobile_app ^&^& flutter run
echo.
pause
