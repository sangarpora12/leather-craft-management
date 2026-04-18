# Panduan Lengkap: Sistem Notifikasi Advanced Berliano Leather

## 📋 Ringkasan Sistem

Sistem notifikasi advanced telah diimplementasikan dengan **3 skenario utama**:

1. **Scan Nota (Operator → Admin)** - Notifikasi setelah operator scan nota
2. **Pengadaan Material (Admin → Operator)** - Notifikasi perintah pengadaan
3. **Perintah Produksi (Admin → Operator)** - Notifikasi perintah produksi

Setiap notifikasi menggunakan **dual channel**: Database (untuk dashboard) + Gmail (untuk email detail).

---

## 🏗️ Arsitektur Sistem

### 1. Notification Classes

#### `ScanNotaNotification`
- **Lokasi**: `app/Notifications/ScanNotaNotification.php`
- **Trigger**: Setelah operator sukses scan nota (OCR/Manual)
- **Penerima**: Semua Admin
- **Channel**: Database + Mail
- **Data**: ID Bahan, Nama Bahan, Daftar Items, Total Harga, Foto Nota

#### `PengadaanMaterialNotification`
- **Lokasi**: `app/Notifications/PengadaanMaterialNotification.php`
- **Trigger**: Admin membuat pengadaan baru
- **Penerima**: Operator yang ditugaskan
- **Channel**: Database + Mail
- **Data**: Tabel pengadaan (Nama Bahan, Jumlah, Target Tanggal)

#### `PerintahProduksiNotification`
- **Lokasi**: `app/Notifications/PerintahProduksiNotification.php`
- **Trigger**: Admin membuat SPK baru
- **Penerima**: Operator yang ditugaskan
- **Channel**: Database + Mail
- **Data**: Gambar Produk, Daftar Material (BOM), Catatan Admin, Status

---

## 📧 Skenario 1: Scan Nota (Operator → Admin)

### Trigger Point
File: `app/Http/Controllers/ScanController.php`
Method: `processManual()` (Baris ~108-130)

### Alur:
1. Operator upload foto nota / input manual
2. Sistem simpan data ke `bahan_masuk`
3. Notifikasi dikirim ke semua admin

### Data yang Dikirim:
```php
$scanData = [
    'id' => $bahanMasuk->id,
    'bahan_id' => $bahanMasuk->id,
    'nama_bahan' => $bahanMasuk->nama_material,
    'items' => [
        [
            'nama' => $bahanMasuk->nama_material,
            'jumlah' => $bahanMasuk->jumlah,
            'satuan' => $bahanMasuk->satuan,
            'harga' => $bahanMasuk->harga_satuan ?? 0,
        ]
    ],
    'total_harga' => ($bahanMasuk->jumlah * ($bahanMasuk->harga_satuan ?? 0)),
];
```

### Email yang Dikirim:
```
Subject: 📸 [SCAN NOTA BARU] - Kulit Sapi A

Halo, Admin!

Operator Budi telah berhasil melakukan scan nota untuk bahan:

Detail Scan:
- ID Bahan: 123
- Nama Bahan: Kulit Sapi A
- Tanggal Scan: 09 Apr 2026 14:30

Daftar Barang di Nota:
1. Kulit Sapi A - 50 Lembar (Rp 150,000)
2. Kulit Kambing - 30 Lembar (Rp 120,000)

Total Harga: Rp 7,500,000

[Foto Nota]

[📋 LIHAT DETAIL BAHAN] → http://192.168.137.1:8000/admin/bahan
```

### Database Notification:
```php
[
    'judul' => '📸 [SCAN NOTA BARU] Kulit Sapi A',
    'pesan' => 'Operator Budi telah scan nota untuk Kulit Sapi A.',
    'url' => route('admin.bahan.index'),
    'icon' => 'camera',
    'type' => 'scan_nota',
    'scan_data' => [...],
    'foto_nota_path' => 'notas/nota_20260409_143000.jpg',
    'foto_nota_url' => 'http://192.168.137.1:8000/storage/notas/nota_20260409_143000.jpg',
]
```

---

## 📦 Skenario 2: Pengadaan Material (Admin → Operator)

### Trigger Point
File: `app/Http/Controllers/PengadaanController.php`
Method: `store()` (Baris ~256-273)

### Alur:
1. Admin membuat pengadaan baru
2. Data disimpan ke `pengadaan`
3. Notifikasi dikirim ke operator

### Data yang Dikirim:
```php
$pengadaan = Pengadaan::create([...]);
$adminName = auth()->user()->name ?? 'Admin';

$operator->notify(new PengadaanMaterialNotification($pengadaan, $adminName));
```

### Email yang Dikirim:
```
Subject: 📦 [PERINTAH PENGADAAN] Kulit Sapi A - REQ-20260409123456

Halo, Tim Operator!

Admin Andi telah menerbitkan perintah pengadaan baru:

TABEL PENGADAAN MATERIAL:

| Detail | Informasi |
|--------|-----------|
| Nama Bahan | Kulit Sapi A |
| Jumlah/Kuantitas | 50 Lembar |
| No. Referensi | REQ-20260409123456 |
| Target Tanggal Pengadaan | 15 Apr 2026 |
| Prioritas | 🟢 BIASA |

📋 Catatan from Admin:
> Segera beli karena stok hampir habis.

Silakan segera lakukan pembelian material sesuai dengan spesifikasi dan jumlah yang tertera.

[📦 LIHAT DAFTAR PENGADAAN] → http://192.168.137.1:8000/operator/pengadaan/list
```

### Database Notification:
```php
[
    'judul' => '📦 [PERINTAH PENGADAAN] Kulit Sapi A',
    'pesan' => 'Admin Andi memerintahkan pengadaan: Kulit Sapi A (REQ-20260409123456) - 50 Lembar.',
    'url' => route('operator.pengadaan.list'),
    'icon' => 'shopping-cart',
    'type' => 'pengadaan_material',
    'pengadaan_data' => [
        'id' => $pengadaan->id,
        'no_referensi' => 'REQ-20260409123456',
        'nama_bahan' => 'Kulit Sapi A',
        'jumlah' => 50,
        'satuan' => 'Lembar',
        'tanggal_dibutuhkan' => '2026-04-15',
        'prioritas' => 'BIASA',
        'catatan' => 'Segera beli karena stok hampir habis.',
        'admin_name' => 'Andi',
    ],
]
```

---

## 🏭 Skenario 3: Perintah Produksi (Admin → Operator)

### Trigger Point
File: `app/Http/Controllers/ProduksiController.php`
Method: `store()` (Baris ~437-444)

### Alur:
1. Admin membuat SPK baru
2. Stok bahan baku dipotong
3. Notifikasi dikirim ke operator

### Data yang Dikirim:
```php
$produksiBaru = Produksi::create([...]);
$adminName = auth()->user()->name ?? 'Admin';

$operator->notify(new PerintahProduksiNotification($produksiBaru, $adminName));
```

### Email yang Dikirim:
```
Subject: 🚨 [PERINTAH PRODUKSI] Dompet Kulit - SPK-2604-1234

Halo, Tim Produksi!

Admin Andi telah menerbitkan perintah produksi baru:

🖼️ GAMBAR PRODUK:
[Image: Dompet Kulit]

📋 INFORMASI PERINTAH PRODUKSI:

| Detail | Informasi |
|--------|-----------|
| Nama Produk | Dompet Kulit |
| Jumlah Produksi | 100 Pcs |
| Kode SPK | SPK-2604-1234 |
| Target Selesai | 20 Apr 2026 |
| Status | 🟡 Menunggu Produksi |

📦 DAFTAR BAHAN/MATERIAL YANG DIBUTUHKAN:

| No | Material | Jumlah Kebutuhan | Satuan |
|----|----------|-----------------|--------|
| 1 | Kulit Sapi A | 50.00 | Lembar |
| 2 | Benang Nylon | 500.00 | Meter |
| 3 | Lem Kulit | 2.00 | Botol |

📝 CATATAN KHUSUS DARI ADMIN:

> Pastikan kualitas jahitan rapi. Gunakan benang warna coklat.

Silakan segera mulai produksi sesuai dengan spesifikasi dan jumlah yang tertera.
Pastikan semua material tersedia sebelum memulai pengerjaan.

[🛠️ LIHAT DETAIL TUGAS] → http://192.168.137.1:8000/operator/tugas/123
```

### Database Notification:
```php
[
    'judul' => '🚨 [PERINTAH PRODUKSI] Dompet Kulit',
    'pesan' => 'Admin Andi memerintahkan produksi: Dompet Kulit (SPK-2604-1234) - 100 Pcs. Status: Menunggu Produksi.',
    'url' => route('operator.tugas.detail', ['id' => $produksi->id]),
    'icon' => 'factory',
    'type' => 'perintah_produksi',
    'produksi_data' => [
        'id' => $produksi->id,
        'kode_spk' => 'SPK-2604-1234',
        'nama_produk' => 'Dompet Kulit',
        'jumlah_produksi' => 100,
        'target_date' => '2026-04-20',
        'status' => 'Menunggu Produksi',
        'catatan' => 'Pastikan kualitas jahitan rapi.',
        'admin_name' => 'Andi',
        'materials' => [
            ['nama_material' => 'Kulit Sapi A', 'jumlah_kebutuhan' => 50, 'satuan' => 'Lembar'],
            ['nama_material' => 'Benang Nylon', 'jumlah_kebutuhan' => 500, 'satuan' => 'Meter'],
            ['nama_material' => 'Lem Kulit', 'jumlah_kebutuhan' => 2, 'satuan' => 'Botol'],
        ],
        'gambar_produk' => 'produk/dompet-kulit.jpg',
    ],
]
```

---

## ⚡ Konfigurasi Mail (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # App Password, bukan password biasa
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Berliano Leather"
```

### Cara Membuat App Password Gmail:
1. Buka [Google Account](https://myaccount.google.com/)
2. Pilih **Security** → **2-Step Verification**
3. Scroll ke bawah → **App passwords**
4. Pilih app: **Mail**, device: **Other**
5. Copy password yang dihasilkan (16 karakter)

---

## 🔔 Saklar Notifikasi (Tabel Pengaturan)

Sistem menggunakan saklar di tabel `pengaturan` untuk mengaktifkan/menonaktifkan notifikasi:

| Nama Pengaturan | Deskripsi | Default |
|----------------|-----------|---------|
| `notif_scan_nota` | Notif saat operator scan nota | 1 (Aktif) |
| `notif_pengadaan_baru` | Notif saat pengadaan baru dibuat | 1 (Aktif) |
| `notif_spk_baru` | Notif saat SPK baru dibuat | 1 (Aktif) |

### Cara Cek/Mengubah Saklar:
```sql
-- Cek status saklar
SELECT * FROM pengaturan WHERE nama_pengaturan LIKE 'notif_%';

-- Aktifkan notifikasi
UPDATE pengaturan SET nilai = '1' WHERE nama_pengaturan = 'notif_scan_nota';

-- Matikan notifikasi
UPDATE pengaturan SET nilai = '0' WHERE nama_pengaturan = 'notif_scan_nota';
```

---

## 🧪 Cara Testing

### Test Skenario 1: Scan Nota
1. Login sebagai **Operator**
2. Buka halaman Scan Nota (`/operator/scan-nota`)
3. Upload foto nota atau input manual
4. Cek:
   - Email masuk ke admin
   - Notifikasi muncul di dashboard admin

### Test Skenario 2: Pengadaan Material
1. Login sebagai **Admin**
2. Buka halaman Pengadaan (`/admin/pengadaan`)
3. Buat pengadaan baru
4. Cek:
   - Email masuk ke operator
   - Notifikasi muncul di dashboard operator

### Test Skenario 3: Perintah Produksi
1. Login sebagai **Admin**
2. Buka halaman Produksi (`/admin/produksi`)
3. Buat SPK baru
4. Cek:
   - Email masuk ke operator
   - Notifikasi muncul di dashboard operator

### Manual Test via Tinker
```bash
php artisan tinker
```

```php
// Test Scan Nota Notification
$admin = App\Models\User::where('role', 'admin')->first();
$scanData = [
    'id' => 1,
    'bahan_id' => 1,
    'nama_bahan' => 'Kulit Sapi A',
    'items' => [['nama' => 'Kulit Sapi A', 'jumlah' => 50, 'satuan' => 'Lembar', 'harga' => 150000]],
    'total_harga' => 7500000,
];
$admin->notify(new App\Notifications\ScanNotaNotification($scanData, 'Budi', 'notas/test.jpg'));

// Test Pengadaan Material Notification
$operator = App\Models\User::where('role', 'operator')->first();
$pengadaan = App\Models\Pengadaan::first();
$operator->notify(new App\Notifications\PengadaanMaterialNotification($pengadaan, 'Andi'));

// Test Perintah Produksi Notification
$produksi = App\Models\Produksi::first();
$operator->notify(new App\Notifications\PerintahProduksiNotification($produksi, 'Andi'));
```

---

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
2. Pastikan user memiliki notifikasi yang belum dibaca
3. Cek JavaScript console untuk error

### Gambar tidak muncul di email
1. Pastikan `APP_URL` sudah benar di `.env`
2. Pastikan file gambar ada di `storage/app/public/`
3. Pastikan symbolic link sudah dibuat: `php artisan storage:link`

### Aplikasi lambat saat kirim email
1. Ubah `QUEUE_CONNECTION=database` di `.env`
2. Jalankan worker: `php artisan queue:work --tries=3`
3. Pastikan tabel `jobs` sudah dibuat:
```bash
php artisan queue:table
php artisan migrate
```

---

## 📁 File Terkait

```
app/
├── Notifications/
│   ├── ScanNotaNotification.php         # Notif scan nota
│   ├── PengadaanMaterialNotification.php # Notif pengadaan
│   ├── PerintahProduksiNotification.php  # Notif produksi
│   ├── PengadaanBahanNotification.php    # Notif pengadaan (old)
│   ├── ProduksiBaruNotification.php      # Notif produksi (old)
│   ├── ProduksiSelesaiNotification.php   # Notif selesai
│   ├── BahanMasukNotification.php        # Notif bahan masuk
│   └── StokGudangNotification.php        # Notif stok
├── Http/
│   └── Controllers/
│       ├── ScanController.php            # Trigger scan nota
│       ├── PengadaanController.php       # Trigger pengadaan
│       └── ProduksiController.php        # Trigger produksi
└── Models/
    ├── BahanMasuk.php
    ├── Pengadaan.php
    ├── Produksi.php
    └── User.php

config/
└── mail.php                              # Konfigurasi mail

.env                                      # Konfigurasi SMTP
```

---

## ✅ Checklist Implementasi

- [x] ScanNotaNotification - Dual channel (Database + Mail)
- [x] PengadaanMaterialNotification - Dual channel (Database + Mail)
- [x] PerintahProduksiNotification - Dual channel (Database + Mail)
- [x] Template email dengan tabel dan gambar
- [x] Trigger di ScanController::processManual()
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
4. Storage: pastikan `storage/app/public` accessible