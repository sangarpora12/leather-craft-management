# TODO: Perbaikan Logika Pemotongan Stok Bahan Baku

## Task: Stok Terpotong Otomatis Saat SPK Dibuat

### Requirements:
1. **Store Method (ProduksiController.php)**:
   - [x] Gunakan DB::transaction untuk keamanan data
   - [x] Validasi stok bahan baku sebelum menyimpan SPK
   - [x] Potong stok (bukan reserve) langsung saat SPK dibuat
   - [x] Jika stok tidak cukup, tampilkan error: "Gagal membuat perintah! Stok [Nama Bahan] tidak mencukupi."

2. **Frontend Operator**:
   - [x] Tampilkan keterangan: "Material sudah dipotong/dialokasikan untuk tugas ini."
   - [x] Jika status SELESAI, munculkan tombol HAPUS di baris tugas

### Files to Edit:
1. `app/Http/Controllers/ProduksiController.php` - Method store()
2. `resources/views/operator/produksi/detail_fix.blade.php` - Tambah pesan material dialokasikan
3. `resources/views/operator/tugas/index.blade.php` - Tambah tombol HAPUS untuk tugas selesai
