# 🌐 Panduan Lengkap: Cloudflare Tunnel untuk Demo Skripsi

Dokumen ini menjelaskan cara mengonfigurasi project Laravel Berliano Leather agar bisa diakses dari HP melalui Cloudflare Tunnel dengan aman.

## ✅ Konfigurasi yang Sudah Dilakukan

### 1. Trust Proxies Middleware
File `app/Http/Middleware/TrustProxies.php` sudah dibuat dengan konfigurasi:
```php
protected $proxies = '*';
```
Ini membuat Laravel mempercayai semua header dari Cloudflare, sehingga HTTPS terbaca dengan benar dan login tidak error 419.

### 2. Force HTTPS Scheme
File `app/Providers/AppServiceProvider.php` sudah diupdate untuk:
- Mendeteksi domain `trycloudflare.com`
- Memaksa penggunaan HTTPS saat menggunakan tunnel
- Mendukung header `X-Forwarded-Proto`

## 📋 Langkah-Langkah Menjalankan

### Langkah 1: Jalankan Laravel Server

Buka **Terminal 1** dan jalankan:

```bash
cd c:\xampp\htdocs\berliano_leather\berliano-leather
php artisan serve --host=0.0.0.0 --port=8000
```

> **Catatan:** `--host=0.0.0.0` penting agar server bisa diakses dari Cloudflare Tunnel.

### Langkah 2: Jalankan Cloudflare Tunnel

Buka **Terminal 2** dan jalankan:

```bash
cloudflared tunnel --url http://localhost:8000
```

Atau jika menggunakan versi lama:

```bash
cloudflared.exe tunnel --url http://localhost:8000
```

### Langkah 3: Dapatkan URL Cloudflare

Setelah cloudflared berjalan, Anda akan melihat output seperti ini:

```
+-----------------------------------------------------------------------------+
|  Your quick Tunnel has been created! Visit it at (it may take some time):   |
+-----------------------------------------------------------------------------+
|                                                                             |
|  https://random-name-tries-cloudflare.com                                   |
|                                                                             |
+-----------------------------------------------------------------------------+
```

**Salin URL** yang muncul (contoh: `https://abc123-def456.trycloudflare.com`).

### Langkah 4: Update .env (Opsional)

Jika Anda ingin URL tetap, update file `.env`:

```env
APP_URL=https://abc123-def456.trycloudflare.com
```

> **Penting:** Ganti `abc123-def456.trycloudflare.com` dengan URL yang Anda dapatkan.

### Langkah 5: Akses dari HP

1. Pastikan HP terhubung ke **WiFi yang sama** dengan komputer (atau gunakan data seluler).
2. Buka browser di HP.
3. Ketik URL Cloudflare yang Anda dapatkan.
4. Login dengan kredensial admin/operator.

## 🔧 Troubleshooting

### Error 419 (CSRF Token Mismatch)
Jika muncul error 419 saat login:
1. Pastikan `TrustProxies.php` sudah dibuat.
2. Clear cache: `php artisan config:clear && php artisan cache:clear`
3. Pastikan URL di browser sama dengan `APP_URL` di `.env`.

### HTTPS Tidak Terbaca
Jika masih HTTP padahal pakai Cloudflare:
1. Cek `AppServiceProvider.php` - pastikan ada `str_contains(request()->getHost(), 'trycloudflare.com')`
2. Restart Laravel server.
3. Clear browser cache.

### Cloudflared Tidak Dikenal
Jika command `cloudflared` tidak ditemukan:
1. Download dari: https://github.com/cloudflare/cloudflared/releases/latest
2. Extract dan letakkan di folder yang accessible (misal: `C:\cloudflared.exe`)
3. Jalankan dengan path lengkap: `C:\cloudflared.exe tunnel --url http://localhost:8000`

## 🚀 Cara Cepat (Quick Start)

```bash
# Terminal 1 - Laravel Server
cd c:\xampp\htdocs\berliano_leather\berliano-leather
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2 - Cloudflare Tunnel
cloudflared tunnel --url http://localhost:8000

# Salin URL yang muncul, lalu buka di HP!
```

## 📱 URL Akses

- **Admin Dashboard:** `https://your-url.trycloudflare.com/admin/dashboard`
- **Operator Dashboard:** `https://your-url.trycloudflare.com/operator/dashboard`
- **Scan Nota:** `https://your-url.trycloudflare.com/operator/scan`

## ⚠️ Catatan Penting

1. **Cloudflare Tunnel hanya berjalan selama terminal tidak ditutup.**
2. **URL berubah setiap kali restart cloudflared** (kecuali pakai tunnel permanen).
3. **Pastikan komputer tidak sleep** saat demo.
4. **Untuk demo skripsi, ini solusi yang aman dan profesional.**

## 🎓 Manfaat untuk Demo Skripsi

- ✅ **Akses dari mana saja** - Tidak perlu satu jaringan WiFi
- ✅ **HTTPS otomatis** - Aman dan profesional
- ✅ **Tidak perlu setup DNS** - Langsung pakai
- ✅ **Gratis** - Tidak perlu beli domain/hosting
- ✅ **Mudah** - Cukup jalankan 2 command

## 📞 Kontak Support

Jika ada masalah, hubungi developer atau cek dokumentasi Laravel:
- Laravel Trust Proxies: https://laravel.com/docs/11.x/requests#trusting-all-proxies
- Cloudflare Tunnel: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/run-tunnel/

---

**Good luck dengan demo skripsinya! 🎉**