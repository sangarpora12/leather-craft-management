# Task: Rewrite ProduksiController.php and detail.blade.php

## Backend Changes (ProduksiController.php):
- [x] STORE function: Change from "reserve only" to "actually cut stock immediately"
  - Use DB::transaction()
  - Reduce stock AND reserved fields at the time SPK is saved
  - Validate BOM requirements before saving

## Frontend Changes (detail.blade.php):
- [x] Change product image path to use `{{ asset("storage/" . $produksi->produk_jadi->foto) }}`
- [x] Fix progress bar: if new (jumlah_selesai = 0), show 0%
- [x] Remove duplicate "Kebutuhan Material" section (keep only one)
- [x] Add data-attributes for live calculator: `data-kebutuhan="{{ $bom->jumlah_kebutuhan }}"`
- [x] Add modal padding fix: `padding-bottom: 120px !important`
- [x] Add delete button that only shows when status = 'SELESAI'
- [x] Fix progress bar formula: (jumlah_selesai / jumlah_produksi) * 100
- [x] Add gradient buttons (from-emerald-500 to-teal-600)

## Route Changes (web.php):
- [x] Add destroy route for operator: Route::delete('/produksi/{id}')

## Status:
- [x] COMPLETED
