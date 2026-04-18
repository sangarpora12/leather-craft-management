@echo off
echo ============================================
echo   BERLIANO LEATHER - Server Starter
echo ============================================
echo.
echo Menjalankan Laravel Server...
echo URL: http://192.168.136.156:8000
echo.
echo Tekan Ctrl+C untuk menghentikan server
echo.

REM Clear cache terlebih dahulu
php artisan config:clear
php artisan cache:clear
php artisan route:clear

REM Jalankan server dengan host 0.0.0.0 agar bisa diakses dari jaringan lokal
php artisan serve --host=0.0.0.0 --port=8000

pause