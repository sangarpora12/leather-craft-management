# TODO: Fix Sidebar (Berliano Leather Admin Panel)

## Status: [ ] Belum dimulai

### Step 1: [x] Update layouts/admin.blade.php
- [x] Fixed positioning + mobile responsive
- [x] ml-64 offset pada content

-   [ ] Buat sidebar fixed positioning (`fixed left-0 top-0 h-screen z-50 w-64`)
-   [ ] Tambah `ml-64` pada main content wrapper
-   [ ] Tambah mobile toggle button di header (hamburger icon dengan Alpine.js)
-   [ ] Tambah mobile overlay backdrop
-   [ ] Update z-index untuk header dan sidebar

### Step 2: [ ] Hapus/Rename components/sidebar.blade.php

-   [ ] Rename ke `components/sidebar-backup.blade.php` (tidak digunakan)

### Step 3: [ ] Test & Verify

-   [ ] Jalankan `php artisan serve`
-   [ ] Test desktop: no overlap, smooth hover
-   [ ] Test mobile: toggle works, overlay closes on click outside
-   [ ] Cek semua routes di sidebar ada di routes/web.php

### Step 4: [ ] Complete

-   [ ] Update checklist ini dengan [x]
-   [ ] Test print layout (sidebar hidden)

**Catatan:** User konfirmasi pakai sidebar dari layouts/admin.blade.php (inline dengan Font Awesome icons).
