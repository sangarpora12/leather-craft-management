# TODO - Fix Issues

## Issue 1: Aset Tidak Berkurang (Assets not decreasing)

-   **Root Cause**: When materials come in via `storeOperator`, no `BahanStokLayer` entries are created
-   **Fix**: Modify `BahanBakuController::storeOperator` to create FIFO layers when materials are added
-   **File**: `app/Http/Controllers/BahanBakuController.php`

## Issue 2: Warna Batas Minimum Stok Tidak Berubah

-   **Root Cause**: Blade uses `$b->stok_minimum` instead of `$batasStok` from Pengaturan table
-   **Fix**: Update blade to use `$batasStok` variable that is passed from controller
-   **File**: `resources/views/admin/bahan/index.blade.php`

## Steps:

-   [x] 1. Fix BahanBakuController::storeOperator to create BahanStokLayer entries
-   [x] 2. Fix bahan/index.blade.php to use $batasStok for stock color logic
-   [x] 3. Run `php artisan view:clear` to clear cached views
