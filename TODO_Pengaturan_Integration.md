# TODO: Pengaturan Integration (Tahap 2)

## Status: IN PROGRESS

## Tasks:

### 1. Fix PengaturanController.php

-   [ ] Fix `index()` method - change pluck to correct order
-   [ ] Add `storeSatuan()` method
-   [ ] Add `destroySatuan()` method

### 2. Add Routes for Satuan

-   [ ] Add route for `admin.pengaturan.satuan.store`
-   [ ] Add route for `admin.pengaturan.satuan.destroy`

### 3. Fix View (index.blade.php)

-   [ ] Fix route in Tab 2 (Profil Perusahaan)
-   [ ] Fix route in Tab 4 (Konfigurasi Gudang)
-   [ ] Add @foreach loop for $satuans in Tab 3
-   [ ] Add working form for adding new Satuan in Tab 3
-   [ ] Add delete button for each row in Tab 3

## Completed:

-   [x] Analyze existing code
-   [x] Create plan
-   [x] Get user confirmation
