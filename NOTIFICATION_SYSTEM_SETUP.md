# 📧 Panduan Lengkap: Notifikasi Otomatis Pengadaan & Produksi

Sistem notifikasi otomatis sudah diimplementasikan untuk mengirim notifikasi ke Operator melalui **Database** dan **Email (Gmail)** setiap kali Admin membuat data Pengadaan Bahan atau Perintah Produksi.

## ✅ Yang Sudah Diimplementasikan

### **1. Notification Classes**
- ✅ `PengadaanBahanNotification` - Notifikasi untuk pengadaan bahan baru
- ✅ `ProduksiBaruNotification` - Notifikasi untuk perintah produksi baru (sudah ada)
- ✅ Kedua notification menggunakan channel: **database** + **mail**

### **2. Controller Integration**
- ✅ `PengadaanController::store()` - Kirim notifikasi setelah pengadaan dibuat
- ✅ `ProduksiController::store()` - Kirim notifikasi setelah SPK dibuat
- ✅ Menggunakan saklar dari tabel `pengaturan` untuk enable/disable notifikasi

### **3. Notification Content**

#### **Email Format:**
```
Subject: 📦 [PENGADAAN BARU] Kulit Sapi A - REQ-20260409013000

Halo, Tim Operator!

Ada pengadaan bahan baru yang perlu Anda terima:

**Detail Pengadaan:**
- **Nama Bahan:** Kulit Sapi A
- **Jumlah:** 50 Lembar
- **No. Referensi:** REQ-20260409013000
- **Tanggal Dibutuhkan:** 16 Apr 2026

Silakan cek daftar pengadaan dan lakukan penerimaan material sesuai jumlah yang dipesan.

[📦 LIHAT DAFTAR PENGADAAN] (link ke halaman operator)
```

#### **Database Format:**
```json
{
  "judul": "📦 [PENGADAAN BARU] Kulit Sapi A",
  "pesan": "Ada pengadaan bahan baru: Kulit Sapi A (REQ-20260409013000) - 50 Lembar. Silakan cek dan terima material.",
  "url": "http://192.168.136.156:8000/operator/pengadaan-list",
  "pengadaan_id": 123,
  "bahan_id": 456
}
```

## 📋 Konfigurasi Gmail SMTP

### **Langkah 1: Setup App Password di Gmail**
1. Login ke Gmail Anda
2. Buka **Google Account Settings** → **Security**
3. Aktifkan **2-Step Verification** (jika belum)
4. Cari **App Passwords** di bagian Security
5. Buat app password baru dengan nama "Berliano Leather"
6. Salin password yang dihasilkan (16 karakter)

### **Langkah 2: Update .env**
```env
# Mail Configuration - Gmail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@berliano-leather.com
MAIL_FROM_NAME="Berliano Leather"
```

**Contoh:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=berliano.leather@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@berliano-leather.com
MAIL_FROM_NAME="Berliano Leather"
```

### **Langkah 3: Clear Cache**
```bash
php artisan config:clear
php artisan cache:clear
```

## 🚀 Setup Queue Configuration

Untuk mengirim email tanpa membuat aplikasi lambat, gunakan queue:

### **Langkah 1: Update .env**
```env
QUEUE_CONNECTION=database
```

### **Langkah 2: Buat tabel jobs (jika belum ada)**
```bash
php artisan queue:table
php artisan migrate
```

### **Langkah 3: Jalankan Queue Worker**
```bash
php artisan queue:work --tries=3
```

Atau jalankan sebagai background process:
```bash
php artisan queue:work --tries=3 &
```

### **Langkah 4: Update Notification Class (Optional)**
Jika ingin menggunakan queue, tambahkan trait `ShouldQueue`:

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class PengadaanBahanNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    // ...
}
```

## 🔧 Saklar Notifikasi (Notification Switches)

Sistem menggunakan tabel `pengaturan` untuk mengontrol notifikasi:

### **Saklar yang Tersedia:**
1. `notif_pengadaan_baru` - Enable/disable notifikasi pengadaan (default: '1')
2. `notif_spk_baru` - Enable/disable notifikasi SPK baru ke operator (default: '1')
3. `notif_stok_menipis` - Enable/disable notifikasi stok ke admin (default: '1')
4. `notif_spk_baru_admin` - Enable/disable notifikasi SPK baru ke admin (default: '1')
5. `notif_produksi_selesai` - Enable/disable notifikasi produksi selesai (default: '1')

### **Cara Mengatur Saklar:**
```sql
-- Enable notifikasi pengadaan
INSERT INTO pengaturan (nama_pengaturan, nilai) 
VALUES ('notif_pengadaan_baru', '1')
ON DUPLICATE KEY UPDATE nilai = '1';

-- Disable notifikasi pengadaan
UPDATE pengaturan SET nilai = '0' WHERE nama_pengaturan = 'notif_pengadaan_baru';
```

## 📱 Cara Menguji Notifikasi

### **Test 1: Kirim Notifikasi Pengadaan**
1. Login sebagai **Admin**
2. Buka `/admin/pengadaan`
3. Buat pengadaan bahan baru
4. Logout dan login sebagai **Operator**
5. Cek notifikasi di navbar (icon bell)
6. Cek email operator

### **Test 2: Kirim Notifikasi Produksi**
1. Login sebagai **Admin**
2. Buka `/admin/produksi/create`
3. Buat SPK baru
4. Logout dan login sebagai **Operator**
5. Cek notifikasi di navbar
6. Cek email operator

## 🔍 Troubleshooting

### **Email Tidak Terkirim**
1. Pastikan App Password Gmail sudah benar (bukan password biasa)
2. Cek `.env` - pastikan `MAIL_USERNAME` dan `MAIL_PASSWORD` benar
3. Clear cache: `php artisan config:clear && php artisan cache:clear`
4. Cek log: `storage/logs/laravel.log`

### **Notifikasi Database Tidak Muncul**
1. Pastikan tabel `notifications` sudah ada
2. Cek relasi `notifications` di model `User`
3. Pastikan operator_id di pengadaan/produksi benar

### **Aplikasi Lambat Saat Kirim Email**
1. Setup queue: `QUEUE_CONNECTION=database`
2. Jalankan queue worker: `php artisan queue:work`
3. Atau gunakan `MAIL_MAILER=log` untuk development

## 🎯 Manfaat untuk Demo Skripsi

- ✅ **Notifikasi Real-time** - Operator langsung tahu ada tugas baru
- ✅ **Dual Channel** - Database + Email, tidak ada yang terlewat
- ✅ **Professional** - Email template rapi dengan link langsung ke tugas
- ✅ **Scalable** - Menggunakan queue untuk performa optimal
- ✅ **Flexible** - Saklar enable/disable untuk setiap jenis notifikasi

## 📞 Kontak Support

Jika ada masalah, cek dokumentasi Laravel:
- Notifications: https://laravel.com/docs/11.x/notifications
- Mail: https://laravel.com/docs/11.x/mail
- Queues: https://laravel.com/docs/11.x/queues

---

**Sistem notifikasi sudah siap digunakan!** 🎉