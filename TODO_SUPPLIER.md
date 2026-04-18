# TODO: Implementasi Fitur Katalog Harga Supplier

## Progres

-   [ ] 1. Update Supplier Model - Tambah method untuk dapat data toko dan material
-   [ ] 2. Update SupplierController - Modifikasi store() untuk auto-update, tambah method grouping
-   [ ] 3. Update View - Ubah layout tabel ke kartu (4 per baris), klik tampil material
-   [ ] 4. Test dan Verifikasi

## Detail Tasks:

### 1. Supplier Model

-   [ ] Tambah method getAllStores() - dapat semua toko unik
-   [ ] Tambah method getMaterialsForStore($namaToko) - dapat material toko tertentu
-   [ ] Tambah method getPriceComparison() - hitung selisih harga termurah

### 2. Supplier Controller

-   [ ] Modifikasi store() - cek material sudah ada → UPDATE bukan CREATE
-   [ ] Tambah method getStoresWithMaterials() - grouping data per toko
-   [ ] Update index() - siapin data untuk view baru

### 3. View (index.blade.php)

-   [ ] Ubah dari table ke card layout (grid-cols-4)
-   [ ] Tambah expand/collapse untuk tampilkan material
-   [ ] Tampilkan perbandingan harga ("lebih murah X ribu")
-   [ ] Hapus edit button untuk info toko
-   [ ] Update modal untuk tambah material

### 4. Operator Route (Jika diperlukan)

-   [ ] Tambah route untuk operator input harga via scan/manual
