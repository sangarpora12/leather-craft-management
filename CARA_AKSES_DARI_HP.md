# Cara Akses Aplikasi dari HP via Hotspot Laptop

## Langkah 1: Cek IP Address Laptop

### Untuk Windows:
1. Buka Command Prompt (CMD)
2. Ketik: `ipconfig`
3. Cari **IPv4 Address** di bagian **Wireless LAN adapter** atau **Ethernet adapter**
4. Contoh IP: `192.168.137.1` (ini IP default saat pakai hotspot Windows)

## Langkah 2: Update File .env

Ganti `APP_URL` di file `.env` dengan IP yang ditemukan:

```env
APP_URL=http://192.168.137.1:8000
```

## Langkah 3: Jalankan Server

Double-click file `start_server.bat` atau jalankan manual:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Langkah 4: Akses dari HP

1. Pastikan HP terhubung ke hotspot laptop
2. Buka browser di HP
3. Ketik alamat: `http://192.168.137.1:8000` (ganti dengan IP laptop Anda)
4. Login seperti biasa

## Langkah 5: Izinkan Firewall (jika perlu)

Jika masih tidak bisa akses:

1. Buka **Windows Defender Firewall**
2. Klik **Advanced settings**
3. Klik **Inbound Rules** → **New Rule**
4. Pilih **Port** → **TCP** → Masukkan port **8000**
5. Pilih **Allow the connection**
6. Beri nama: "Laravel Port 8000"

## Troubleshooting

### Masalah: "Connection refused" atau "Cannot connect"
- Pastikan server Laravel sedang berjalan
- Pastikan HP dan laptop terhubung ke jaringan/hotspot yang sama
- Coba matikan sementara antivirus/firewall

### Masalah: "CSRF token mismatch" saat login
- Clear cache: `php artisan config:clear && php artisan cache:clear`
- Pastikan `SESSION_DOMAIN=null` di .env
- Pastikan `SESSION_SECURE_COOKIE=false` di .env

### Masalah: Email notifikasi tidak terkirim
- Pastikan koneksi internet aktif
- Cek konfigurasi MAIL di .env
- Password Gmail yang digunakan adalah **App Password**, bukan password biasa

## Catatan Penting

- IP `192.168.137.1` adalah IP default hotspot Windows
- Jika menggunakan router WiFi, IP biasanya `192.168.1.x` atau `192.168.0.x`
- Setiap kali restart hotspot, IP bisa berubah - cek ulang dengan `ipconfig`