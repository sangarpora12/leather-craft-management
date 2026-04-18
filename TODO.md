## ⚠️ REMINDER PENTING — CLOUDFLARE TUNNEL & APP_URL
> Setiap kali link Cloudflare Tunnel berubah (misalnya restart tunnel),
> **WAJIB** update `APP_URL` di file `.env` agar cocok dengan URL tunnel terbaru.
>
> Contoh:
> ```
> APP_URL=https://xxxx-yyyy-zzzz.trycloudflare.com
> ```
>
> **Kenapa penting?**
> - Gambar produk di email Gmail diambil berdasarkan `APP_URL` di `.env`
> - Server Google tidak bisa akses `localhost`, harus pakai URL publik (Cloudflare)
> - Jika `APP_URL` tidak cocok → gambar di email akan rusak / tidak muncul
>
> **Langkah setelah ganti URL Cloudflare:**
> 1. Edit `.env` → ubah `APP_URL=https://url-cloudflare-baru.trycloudflare.com`
> 2. Jalankan: `php artisan config:clear && php artisan cache:clear`
> 3. Test kirim notif produksi → cek gambar di Gmail

---

# ✅ TODO: Update Notifikasi Produksi & Pengadaan (Spesifik Operator)

## Status: [IN PROGRESS]

### 1. [ ] app/Notifications/PerintahProduksiNotification.php
   - Tambah parameter constructor `bool $isAssigned = false`
   - Simpan properti `$isAssigned`
   - `toMail()`: jika assigned tampilkan tombol **Login & Kerjakan**
   - `toMail()`: jika tidak assigned, sembunyikan tombol login dan hanya tampilkan detail instruksi

### 2. [ ] app/Http/Controllers/ProduksiController.php
   - Update method `store` untuk kirim notif ke semua operator via loop
   - Operator terpilih (`operator_id` dari form) kirim notif dengan `$isAssigned = true`
   - Operator lain kirim notif dengan `$isAssigned = false`
   - Pertahankan logging agar debug tetap mudah

### 3. [ ] app/Http/Controllers/PengadaanController.php
   - Pastikan notif pengadaan hanya ke satu operator yang dipilih di form
   - Hapus fallback kirim ke semua operator

### 4. [ ] Testing (Critical + Thorough)
   - Test kirim SPK baru
   - Verifikasi email operator assigned berisi tombol **Login & Kerjakan**
   - Verifikasi email operator non-assigned tanpa tombol login
   - Verifikasi notifikasi database tetap masuk
   - Test pengadaan: hanya operator terpilih yang menerima notifikasi

### 5. [ ] Gmail SMTP Verification
   - Pastikan `.env` memakai App Password Gmail asli (bukan placeholder)
   - Pastikan `MAIL_FROM_ADDRESS` terisi valid
   - Jalankan `php artisan config:clear` + `php artisan cache:clear`
   - Cek `storage/logs/laravel.log` untuk hasil kirim email

---

**Catatan:** Tidak ada perubahan migration/database schema, aman untuk data existing.
