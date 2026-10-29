# Panduan Lengkap: Sistem Notifikasi Otomatis Berliano Leather

## 📋 Ringkasan Sistem

Sistem notifikasi otomatis sudah **terimplementasi lengkap** di aplikasi Berliano Leather. Setiap kali Admin membuat:
1. **Pengadaan Bahan** → Notifikasi ke Operator
2. **Perintah Produksi (SPK)** → Notifikasi ke Operator

## 🏗️ Arsitektur Sistem

### 1. Notification Classes

#### `PengadaanBahanNotification`
- **Lokasi**: `app/Notifications/PengadaanBahanNotification.php`
- **Channel**: Database + Mail
- **Trigger**: Saat Admin membuat pengadaan bahan baru
- **Penerima**: Operator yang ditugaskan (atau semua operator jika tidak spesifik)

#### `ProduksiBaruNotification`
- **Lokasi**: `app/Notifications/ProduksiBaruNotification.php`
- **Channel**: Database + Mail
- **Trigger**: Saat Admin membuat SPK baru
- **Penerima**: Operator yang ditugaskan

### 2. Controller Triggers

#### PengadaanController::store()
```php
// Baris 255-273
$isPengadaanNotifAktif = Pengaturan::where('nama_pengaturan', 'notif_pengadaan_baru')->value('nilai') ?? '1';

if ($isPengadaanNotifAktif == '1') {
    $operator = User::find($request->operator_id);
    if ($operator) {
        $operator->notify(new PengadaanBahanNotification($pengadaan));
    } else {
        $operators = User::where('role', 'operator')->get();
        Notification::send($operators, new PengadaanBahanNotification($pengadaan));
    }
}
```

#### ProduksiController::store()
```php
// Baris 437-444
$isSpkNotifAktif = Pengaturan::where('nama_pengaturan', 'notif_spk_baru')->value('nilai') ?? '1';

if ($isSpkNotifAktif == '1') {
    $operator = User::find($request->operator_id);
    if ($operator) {
        Notification::send($operator, new ProduksiBaruNotification($produksiBaru));
    }
}
```

### 3. Konfigurasi Mail (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Berliano Leather"
```

## 🎯 Fitur Utama

### 1. Dual Channel (Database + Mail)
- **Database**: Notifikasi tersimpan di tabel `notifications` untuk ditampilkan di dashboard
- **Mail**: Email otomatis terkirim ke operator

### 2. Smart URL Generation
- URL otomatis menyesuaikan dengan IP lokal untuk akses dari HP
- Fallback ke IP hostname jika `APP_URL` adalah localhost

### 3. Saklar Notifikasi (Pengaturan)
Sistem menggunakan saklar di tabel `pengaturan` untuk mengaktifkan/menonaktifkan notifikasi:

| Nama Pengaturan | Deskripsi | Default |
|----------------|-----------|---------|
| `notif_pengadaan_baru` | Notif saat pengadaan baru dibuat | 1 (Aktif) |
| `notif_spk_baru` | Notif saat SPK baru dibuat | 1 (Aktif) |
| `notif_produksi_selesai` | Notif saat produksi selesai | 1 (Aktif) |
| `notif_stok_menipis` | Notif saat stok menipis | 1 (Aktif) |

### 4. Template Email yang Rapi
Email menggunakan format yang informatif dengan:
- Subject yang jelas dengan emoji
- Detail lengkap (nama bahan, jumlah, tanggal)
- Tombol aksi yang mengarah ke halaman terkait
- Petunjuk login jika belum login

## 📧 Contoh Email yang Dikirim

### Email Pengadaan Bahan
```
Subject: 📦 [PENGADAAN BARU] Kulit Sapi A - REQ-20260409123456

Halo, Tim Operator!

Ada pengadaan bahan baru yang perlu Anda terima:

Detail Pengadaan:
- Nama Bahan: Kulit Sapi A
- Jumlah: 50 Lembar
- No. Referensi: REQ-20260409123456
- Tanggal Dibutuhkan: 15 Apr 2026

Silakan cek daftar pengadaan dan lakukan penerimaan material sesuai jumlah yang dipesan.

[📦 LIHAT DAFTAR PENGADAAN] → http://192.168.137.1:8000/operator/pengadaan/list
```

### Email Produksi Baru (SPK)
```
Subject: 🚨 [TUGAS BARU] Perintah Produksi: Dompet Kulit - SPK-2604-1234

Halo, Tim Produksi!

Ada tugas baru untuk mengerjakan **Dompet Kulit** sebanyak **100 Pcs**.

📋 Catatan dari Admin:
> Segera selesaikan sebelum tanggal target.

Silakan cek detail bahan (BOM) dan kalkulasi modal (HPP) melalui tautan di bawah ini sebelum memulai pengerjaan.

[🛠️ LIHAT DETAIL TUGAS] → http://192.168.137.1:8000/operator/tugas/123
```

## ⚡ Queue Configuration

### Status Saat Ini
- **Config default**: `database`
- **ENV setting**: `sync`

### Rekomendasi
Untuk production, ubah `QUEUE_CONNECTION` di `.env` menjadi `database`:

```env
QUEUE_CONNECTION=database
```

Kemudian jalankan worker:
```bash
php artisan queue:work --tries=3
```

**Keuntungan Queue Database:**
- Email dikirim di background
- Aplikasi tidak lambat saat klik simpan
- Jika gagal, akan retry otomatis

## 🧪 Cara Testing

### 1. Test Notifikasi Pengadaan
1. Login sebagai Admin
2. Buka halaman Pengadaan (`/admin/pengadaan`)
3. Buat pengadaan bahan baru
4. Cek:
   - Email masuk ke operator
   - Notifikasi muncul di dashboard operator

### 2. Test Notifikasi Produksi
1. Login sebagai Admin
2. Buka halaman Produksi (`/admin/produksi`)
3. Buat SPK baru
4. Cek:
   - Email masuk ke operator
   - Notifikasi muncul di dashboard operator

### 3. Manual Test via Tinker
```bash
php artisan tinker
```

```php
// Test kirim notifikasi
$operator = App\Models\User::where('role', 'operator')->first();
$pengadaan = App\Models\Pengadaan::first();
$operator->notify(new App\Notifications\PengadaanBahanNotification($pengadaan));
```

## 🔧 Troubleshooting

### Email tidak terkirim
1. Cek koneksi internet
2. Pastikan `MAIL_PASSWORD` adalah **App Password** (bukan password biasa)
3. Cek log: `storage/logs/laravel.log`
4. Test koneksi SMTP:
```bash
php artisan tinker
Mail::raw('test', function($m) { $m->to('test@example.com')->subject('Test'); });
```

### Notifikasi tidak muncul di dashboard
1. Cek tabel `notifications` di database
2. Pastikan operator memiliki notifikasi yang belum dibaca
3. Cek JavaScript console untuk error

### Aplikasi lambat saat simpan
1. Ubah `QUEUE_CONNECTION=database` di `.env`
2. Jalankan worker: `php artisan queue:work`
3. Pastikan tabel `jobs` sudah dibuat:
```bash
php artisan queue:table
php artisan migrate
```

## 📁 File Terkait

```
app/
├── Notifications/
│   ├── PengadaanBahanNotification.php    # Notif pengadaan baru
│   ├── ProduksiBaruNotification.php      # Notif SPK baru
│   ├── ProduksiSelesaiNotification.php   # Notif produksi selesai
│   ├── BahanMasukNotification.php        # Notif bahan masuk
│   └── StokGudangNotification.php        # Notif stok
├── Http/
│   └── Controllers/
│       ├── PengadaanController.php       # Trigger notif pengadaan
│       └── ProduksiController.php        # Trigger notif produksi
└── Models/
    ├── Pengadaan.php
    ├── Produksi.php
    └── User.php

config/
└── mail.php                              # Konfigurasi mail

.env                                      # Konfigurasi SMTP
```

## ✅ Checklist Implementasi

- [x] Generate Notification Class (PengadaanBahanNotification)
- [x] Generate Notification Class (ProduksiBaruNotification)
- [x] Setup dual channel (database + mail)
- [x] Template email yang rapi
- [x] Trigger di PengadaanController::store()
- [x] Trigger di ProduksiController::store()
- [x] Konfigurasi SMTP Gmail
- [x] Smart URL generation untuk akses lokal
- [x] Saklar notifikasi di tabel pengaturan
- [x] Dokumentasi lengkap

## 📞 Support

Jika ada masalah, cek:
1. Log file: `storage/logs/laravel.log`
2. Database: tabel `notifications` dan `jobs`
3. Konfigurasi: `.env` dan `config/mail.php`