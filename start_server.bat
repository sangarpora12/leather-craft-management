@echo off
title Berliano Leather - Server ^& Cloudflare
echo ============================================
echo   BERLIANO LEATHER - Server Starter
echo ============================================
echo.

echo [1/4] 🧹 Membersihkan cache Laravel...
call php artisan optimize:clear
echo.

echo [2/4] 🛑 Mematikan proses Cloudflare yang mungkin masih berjalan...
taskkill /f /im cloudflared.exe > nul 2>&1
echo.

echo [3/4] 🚀 Menjalankan Laravel Server...
REM Membuka terminal baru khusus untuk Laravel Server
start "Laravel Server" php artisan serve --host=0.0.0.0 --port=8000

REM Jeda 3 detik untuk memastikan server Laravel sudah berdiri sebelum Cloudflare jalan
timeout /t 3 /nobreak > nul
echo.

echo [4/4] 🌐 Memulai Cloudflare Tunnel ^& Auto-Update .env...
echo Script akan terus berjalan di jendela ini. Tekan Ctrl+C untuk berhenti.
echo.
REM Memanggil script Node.js yang sudah kita buat sebelumnya
node tunnel.js

pause