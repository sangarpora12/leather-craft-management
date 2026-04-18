@echo off
echo ============================================
echo   FIX FIREWALL - Laravel Port 8000
echo ============================================
echo.
echo Menambahkan rule Firewall untuk port 8000...
echo.

REM Tambah rule firewall untuk port 8000
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000

echo.
echo ============================================
echo   Firewall rule berhasil ditambahkan!
echo ============================================
echo.
echo Sekarang coba akses dari HP dengan alamat:
echo http://192.168.137.1:8000
echo.
echo Jika masih tidak bisa, cek IP laptop dengan: ipconfig
echo.
pause