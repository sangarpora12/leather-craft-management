<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
// --- 1. IMPORT CONTROLLER ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ProdukJadiController; 
use App\Http\Controllers\BomController; 
use App\Http\Controllers\ProduksiController; 
use App\Http\Controllers\ProdukKeluarController; 
use App\Http\Controllers\PengadaanController; 
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\WorkerMasterController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\ScanController;

// --- 2. IMPORT MIDDLEWARE ---
use App\Http\Middleware\CheckRole as RoleMiddleware;

// Load additional route files
// require __DIR__.'/auth.php';
require __DIR__ . '/notifications.php';

/*
|--------------------------------------------------------------------------
| Web Routes - BERLIANO LEATHER (SYSTEM V2)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. AREA TAMU (LOGIN)
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
   Route::get('/login', [AuthController::class, 'login'])->name('login'); 
   Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
});

// Route untuk verifikasi email operator
Route::get('/operator/verify/{id}', [WorkerMasterController::class, 'verifyOperator'])
    ->name('operator.verify');

// ==========================================
// PASSWORD RESET ROUTES (Laravel Built-in)
// ==========================================

// Tampilkan form "Lupa Password" - minta email
Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

// Kirim link reset password ke email
Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])
    ->name('password.email');

// Tampilkan form reset password (link dari email)
Route::get('/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])
    ->name('password.reset');

// Simpan password baru
Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword'])
    ->name('password.update');

// ==========================================
// 2. AREA MEMBER (SUDAH LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // --- REDIRECT DASHBOARD ---
    Route::get('/dashboard', function () {
        // Case-insensitive agar konsisten dengan middleware CheckRole
        // (menghindari masalah jika di DB tersimpan 'Operator'/'ADMIN', dll)
        $role = strtolower((string) Auth::user()->role);
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'operator') return redirect()->route('operator.dashboard');
        return abort(403, 'Akses ditolak.');
    })->name('dashboard');

    // --- LOGOUT ---
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
    
    // ------------------------------------------
    // GRUP A: ADMIN
    // ------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware(RoleMiddleware::class . ':admin')->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

        // --- DATA MASTER (BAHAN BAKU) ---
        Route::resource('bahan', BahanBakuController::class)->names('bahan');
        Route::get('/api/bahan/search', [BahanBakuController::class, 'searchApi'])->name('bahan.search.api');
        Route::get('/bahan/{id}/stock-layers', [BahanBakuController::class, 'getStockLayers'])->name('bahan.stock-layers');
        
        // Produk Jadi
        Route::resource('produk-jadi', ProdukJadiController::class)->names('produk-jadi');
        Route::post('/produk-jadi/{id}/copy', [ProdukJadiController::class, 'copy'])->name('produk-jadi.copy');
        Route::post('/produk-jadi/{id}/adjust', [ProdukJadiController::class, 'adjustStock'])->name('produk-jadi.adjust');

        // --- MANAJEMEN USER ---
        Route::resource('pekerja-master', WorkerMasterController::class)->names('pekerja-master');
        Route::resource('operator', OperatorController::class)->names('operator');
        Route::patch('/operator/{id}/toggle-status', [OperatorController::class, 'toggleStatus'])->name('operator.toggle-status');
        Route::post('/operator/{id}/resend-verification', [OperatorController::class, 'resendVerification'])->name('operator.resend-verification');

        // --- MASTER DATA (SATUAN & KATEGORI) ---
        Route::resource('satuan', \App\Http\Controllers\Admin\SatuanController::class)->except(['show', 'edit', 'update', 'create', 'index']);
        Route::resource('kategori', \App\Http\Controllers\Admin\KategoriController::class)->except(['show', 'edit', 'update', 'create', 'index']);
        Route::resource('warna', \App\Http\Controllers\Admin\WarnaController::class)->except(['show', 'edit', 'update', 'create', 'index']);
        Route::resource('motif', \App\Http\Controllers\Admin\MotifController::class)->except(['show', 'edit', 'update', 'create', 'index']);
        // --- PRODUKSI & LOGISTIK ---
        Route::get('/bom', [BomController::class, 'index'])->name('bom.index');
        Route::get('/bom/create/{produk_jadi_id}', [BomController::class, 'create'])->name('bom.create'); 
        Route::post('/bom/store/{produk_jadi_id}', [BomController::class, 'store'])->name('bom.store'); 
        Route::get('/bom/{id}/edit', [BomController::class, 'edit'])->name('bom.edit');
        Route::put('/bom/{id}', [BomController::class, 'update'])->name('bom.update');
        
         // SPK / Perintah Produksi
         Route::get('/produksi/perintah-produksi', [ProduksiController::class, 'perintahProduksi'])->name('produksi.perintah');
         // Redirect legacy route untuk kompatibilitas
         Route::get('/produksi/create', function () {
             return redirect()->route('admin.produksi.perintah', [], 301);
         })->name('produksi.create');
         
          Route::resource('produksi', ProduksiController::class)->names('produksi')->except(['create', 'show']);
          Route::post('/produksi/{id}/cancel', [ProduksiController::class, 'cancelProduksi'])->name('produksi.cancel');
          Route::get('/produksi/{id}', [ProduksiController::class, 'show'])->name('produksi.show');
         Route::delete('produksi', [ProduksiController::class, 'bulkDestroy'])->name('produksi.bulk_destroy');
         Route::put('/produksi/{id}/status', [ProduksiController::class, 'updateStatus'])->name('produksi.update_status'); 
         Route::post('/produksi/{id}/request-bahan', [ProduksiController::class, 'requestBahan'])->name('produksi.request_bahan');
         Route::get('/produksi/monitoring', [ProduksiController::class, 'monitoring'])->name('produksi.monitoring');
         Route::get('/produksi/{id}/review', [ProduksiController::class, 'review'])->name('produksi.review');
        // Produk Keluar
        Route::get('/produk-keluar/history', [ProdukKeluarController::class, 'history'])->name('produk-keluar.history');
        Route::get('/pakai-produk', [\App\Http\Controllers\Admin\PenguranganStokController::class, 'index'])->name('pakai-produk.index');
        Route::get('/pakai-produk/history', [\App\Http\Controllers\ProdukKeluarController::class, 'index'])->name('pakai-produk.history');
        Route::get('/pakai-produk/create', [\App\Http\Controllers\Admin\PenguranganStokController::class, 'create'])->name('pakai-produk.create');
        Route::post('/pakai-produk', [\App\Http\Controllers\Admin\PenguranganStokController::class, 'store'])->name('pakai-produk.store');
        
        
        Route::get('/pengadaan', [PengadaanController::class, 'index'])->name('pengadaan.index');
        Route::post('/pengadaan', [PengadaanController::class, 'store'])->name('pengadaan.store');
        Route::delete('/pengadaan/{id}', [PengadaanController::class, 'destroy'])->name('pengadaan.destroy');

        // --- MONITORING & LAPORAN ---
        Route::get('/ringkasan-operasional', [LaporanController::class, 'index'])->name('ringkasan-operasional');
        
        Route::prefix('laporan')->name('laporan.')->group(function () {
            // Single unified laporan page - uses mode parameter to filter
            // mode options: lengkap (all data), produk (production only), bahan (materials only)
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            
            // Menu Pengaturan (Settings)
            Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
            Route::post('/pengaturan', [PengaturanController::class, 'store'])->name('pengaturan.store');
            Route::post('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
            
            // Reset Data Transaksi
            Route::post('/pengaturan/reset-transaksi', [PengaturanController::class, 'resetDataTransaksi'])->name('pengaturan.reset-transaksi');
            
            // Reset Database Pilihan (Checkbox-based selective reset)
            Route::post('/pengaturan/reset-data', [PengaturanController::class, 'resetData'])->name('pengaturan.resetData');
            
            // Super Reset - Tombol Kiamat (Direct access via browser)
            Route::get('/super-reset', [PengaturanController::class, 'superReset'])->name('super-reset');
            
            Route::get('/analisis', [LaporanController::class, 'analisis'])->name('analisis');
            // Export PDF untuk halaman laporan produksi (admin.laporan.index)
            // Dipakai oleh tombol di resources/views/admin/laporan/index.blade.php
            Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])->name('exportPdf');
            Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
            Route::get('/hasil-produksi', [LaporanController::class, 'hasilProduksi'])->name('hasil-produksi');
            Route::get('/harian', [LaporanController::class, 'harian'])->name('harian');
            // Removed separate /lengkap route - now using unified index with mode parameter
            Route::get('/hasil-produksi/export/pdf', [LaporanController::class, 'exportHasilProduksiPdf'])->name('hasil-produksi.export.pdf');
            Route::get('/harian/export/pdf', [LaporanController::class, 'exportHarianPdf'])->name('harian.export.pdf');
            Route::post('/fast-complete', [LaporanController::class, 'fastComplete'])->name('fast-complete');
            
            // Menu Supplier / Katalog Harga
            Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
            Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
            Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
            Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
            
            // AJAX routes for dynamic content
            Route::get('/supplier/materials/{namaToko}', [SupplierController::class, 'getMaterials'])->name('supplier.materials');
            Route::get('/supplier/price-comparison', [SupplierController::class, 'getPriceComparison'])->name('supplier.price-comparison');
        });

        // --- HAK AKSES (Access Rights Management) ---
        Route::get('/hak-akses', [HakAksesController::class, 'index'])->name('hak-akses.index');
        Route::put('/hak-akses/{id}/role', [HakAksesController::class, 'updateRole'])->name('hak-akses.update-role');
        Route::patch('/hak-akses/{id}/toggle-status', [HakAksesController::class, 'toggleStatus'])->name('hak-akses.toggle-status');
        Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');

        // --- PROFILE (Admin Profile Page) ---
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/company', [ProfileController::class, 'updateCompany'])->name('profile.company');

        // --- LOG AKTIVITAS (Audit Trail) ---
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
        Route::post('/log-aktivitas/cleanup', [LogAktivitasController::class, 'cleanup'])->name('log-aktivitas.cleanup');
        Route::delete('/log-aktivitas', [LogAktivitasController::class, 'destroyAll'])->name('log-aktivitas.destroy-all');
        
        // --- SCAN NOTA ADMIN (Material Entry Verification) ---
        Route::get('/scan-pending', [ScanController::class, 'pendingList'])->name('scan.pending');
        Route::get('/scan-detail/{id}', [ScanController::class, 'show'])->name('scan.detail');
        Route::post('/scan-detail/{id}/verify', [ScanController::class, 'verify'])->name('scan.verify');
        Route::post('/scan-detail/{id}/reject', [ScanController::class, 'reject'])->name('scan.reject');
        
    });

    // ------------------------------------------
    // GRUP B: OPERATOR
    // ------------------------------------------
    Route::prefix('operator')->name('operator.')->middleware(RoleMiddleware::class . ':operator')->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'operatorDashboard'])->name('dashboard');
        // Operator notifications (mark read + redirect)
        Route::get('/notifications', [DashboardController::class, 'operatorNotifications'])->name('notifications');
        Route::post('/notifications/mark-read', [DashboardController::class, 'markOperatorNotificationRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [DashboardController::class, 'markAllOperatorNotificationsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{id}/read', [DashboardController::class, 'markOperatorNotificationReadAndRedirect'])->name('notifications.read');

        // Laporan operator (ringkasan + riwayat)
        Route::get('/laporan', [DashboardController::class, 'operatorLaporan'])->name('laporan.index');
        Route::get('/laporan/cetak', [DashboardController::class, 'operatorLaporanCetak'])->name('laporan.cetak');
        
        // Tugas Produksi
        Route::get('/tugas', [ProduksiController::class, 'operatorIndex'])->name('tugas.index');
        Route::post('/tugas/bulk-delete', [ProduksiController::class, 'operatorBulkDelete'])->name('tugas.bulk-delete');
        // Detail tugas operator (baru) - khusus operator, menampilkan BOM+HPP+input qty split
        Route::get('/tugas/{id}', [ProduksiController::class, 'operatorDetail'])->name('tugas.detail');
        // Halaman baru setelah klik "Lanjut" (ringkasan pekerjaan + tombol selesai)
        Route::get('/tugas/{id}/lanjut', [ProduksiController::class, 'operatorLanjut'])->name('tugas.lanjut');
        // Route::post('/tugas/{id}/selesai', [ProduksiController::class, 'operatorSelesai'])->name('tugas.selesai');
        Route::get('/tugas/{id}/detail-ajax', [ProduksiController::class, 'getDetailAjax'])->name('tugas.detail_ajax');
         Route::get('/produksi/{id}', [ProduksiController::class, 'show'])->name('produksi.show');
        Route::post('/produksi/{id}/ambil', [ProduksiController::class, 'ambilTugas'])->name('produksi.ambil');
        Route::put('/produksi/{id}/status', [ProduksiController::class, 'updateStatus'])->name('produksi.update_status');
        
        // AJAX: Mulai Tugas (tanpa reload halaman)
        Route::post('/produksi/{id}/start', [ProduksiController::class, 'startTaskAjax'])->name('produksi.start');
        Route::post('/tugas/{id}/start', [ProduksiController::class, 'startTaskAjax'])->name('tugas.start_ajax');
        Route::post('/produksi/{id}/request-bahan', [ProduksiController::class, 'requestBahan'])->name('produksi.request_bahan');
        Route::post('/produksi/{id}/partial', [ProduksiController::class, 'savePartialProduction'])->name('produksi.partial');
        Route::post('/produksi/{id}/selesai', [ProduksiController::class, 'operatorSelesai'])->name('produksi.selesai');
        Route::delete('/produksi/{id}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
        Route::post('/produksi/update-qty/{productId}', [ProduksiController::class, 'updateProductQty'])->name('produksi.update_qty');
        Route::post('/produksi/start-product/{productId}', [ProduksiController::class, 'startProductProduction'])->name('produksi.start_product');
        
        // Scan Nota (New System)
        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan/process', [ScanController::class, 'process'])->name('scan.process');
        Route::get('/scan/success/{id}', [ScanController::class, 'success'])->name('scan.success');
        
        // Scan Nota (Legacy - keep for backward compatibility)
        Route::get('/scan/ocr', function () { return view('operator.scan-ocr'); })->name('scan.ocr');
        Route::get('/scanner', function () { return view('operator.scanner'); })->name('scanner');
        Route::post('/scan/legacy', [OcrController::class, 'processScan'])->name('scan.process.legacy');
        Route::post('/scan-old', [OcrController::class, 'scan'])->name('scan.process.old');
        Route::get('/scan/result', function () { return view('operator.scan-result'); })->name('scan.result');
        Route::post('/stok/store', [OcrController::class, 'storeStok'])->name('stok.store');
        
        // OCR API endpoint for smart scanner
        Route::post('/ocr/process', [OcrController::class, 'processScan'])->name('ocr.process');
        
        // Rute Test OCR (untuk debugging)
        Route::get('/test-ocr', function() {
            // Test dengan gambar dummy
            $testImage = public_path('tes.jpeg');
            if (file_exists($testImage)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test image exists',
                    'path' => $testImage
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Test image not found'
                ]);
            }
        });
        
        // Bahan Baku
        Route::get('/bahan/tambah', function() { return view('operator.tambah-bahan'); })->name('bahan.tambah');
        Route::post('/bahan/simpan', [BahanBakuController::class, 'storeOperator'])->name('bahan.store');
        Route::get('/bahan', [BahanBakuController::class, 'indexOperator'])->name('bahan.index');
        Route::get('/bahan/{id}/edit', [BahanBakuController::class, 'editOperator'])->name('bahan.edit');
        Route::put('/bahan/{id}', [BahanBakuController::class, 'updateOperator'])->name('bahan.update');
        
        // Pengadaan / Penerimaan Bahan
        Route::get('/pengadaan-bahan', [PengadaanController::class, 'indexOperator'])->name('pengadaan');
        Route::get('/daftar-belanja', [PengadaanController::class, 'indexOperator'])->name('belanja.index');
        Route::get('/pengadaan/list', [PengadaanController::class, 'indexOperator'])->name('pengadaan.list');
        Route::post('/pengadaan/request/{materialId}', [PengadaanController::class, 'requestMaterial'])->name('pengadaan.request');
        Route::patch('/daftar-belanja/{id}/selesai', [PengadaanController::class, 'markAsDone'])->name('belanja.selesai');
        
        // Rute baru untuk penerimaan bahan baku
        Route::get('/pengadaan/{id}/receive-form', [PengadaanController::class, 'showReceiveForm'])->name('pengadaan.receive_form');
        Route::post('/pengadaan/{id}/receive', [PengadaanController::class, 'receiveMaterial'])->name('pengadaan.receive');
        
        // Profile
        Route::get('/profile', function () { return view('operator.profile', ['user' => Auth::user()]); })->name('profile');
        Route::post('/profile/update-foto', function (Request $request) {
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            $user = Auth::user();
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::exists('public/' . $user->foto)) {
                    Storage::delete('public/' . $user->foto);
                }
                $path = $request->file('foto')->store('profile_photos', 'public');
                $user->foto = $path;
                $user->save();
            }
            return back()->with('success', 'Foto profil berhasil diupdate');
        })->name('updateFoto');
    });
});

// Auto login for operators via email
Route::get('/operator/auto-login', [ProduksiController::class, 'autoLogin'])->name('operator.auto_login')->middleware('signed');

// API for bahan search
Route::get('/api/bahan/search', [BahanBakuController::class, 'searchApi'])->name('api.bahan.search')->middleware('auth');
Route::get('/api/bahan/suggestions', [BahanBakuController::class, 'searchSuggestions'])->name('api.bahan.suggestions')->middleware('auth');
Route::get('/api/produk-jadi/suggestions', [ProdukJadiController::class, 'searchSuggestions'])->name('api.produk-jadi.suggestions')->middleware('auth');

// --- ROUTE DARURAT ---
Route::get('/fix-notifications', function() {
    try {
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        return "<h1 style='color:green; text-align:center;'>Tabel notifikasi berhasil diperbaiki.</h1>";
    } catch (\Exception $e) { 
        return "<h1 style='color:red'>Gagal memperbaiki tabel notifikasi: " . e($e->getMessage()) . "</h1>";
    }
});

Route::get('/test-bahan', function() {
    return \App\Models\BahanBaku::all();
})->middleware('auth');

// Route untuk seed default Satuan (satuan ukuran)
Route::get('/seed-satuan', function() {
    $satuans = [
        // Satuan Bahan
        ['nama_satuan' => 'Meter', 'tipe' => 'bahan'],
        ['nama_satuan' => 'Kg', 'tipe' => 'bahan'],
        ['nama_satuan' => 'Liter', 'tipe' => 'bahan'],
        ['nama_satuan' => 'Roll', 'tipe' => 'bahan'],
        ['nama_satuan' => 'Yard', 'tipe' => 'bahan'],
        ['nama_satuan' => 'Square Feet', 'tipe' => 'bahan'],

        // Satuan Produk
        ['nama_satuan' => 'Pcs', 'tipe' => 'produk'],
        ['nama_satuan' => 'Set', 'tipe' => 'produk'],
        ['nama_satuan' => 'Lusin', 'tipe' => 'produk'],
    ];
    
    foreach ($satuans as $satuan) {
        \App\Models\Satuan::updateOrCreate(
            ['nama_satuan' => $satuan['nama_satuan']],
            $satuan
        );
    }
    
    return redirect('/admin/laporan/pengaturan')->with('success', 'Default Satuan berhasil ditambahkan!');
})->middleware('auth');

// Route untuk seed default Kategori
Route::get('/seed-kategori', function() {
    $kategoris = [
        ['nama_kategori' => 'Bahan Utama'],
        ['nama_kategori' => 'Aksesoris'],
        ['nama_kategori' => 'Kimia'],
        ['nama_kategori' => 'Packaging'],
    ];
    
    foreach ($kategoris as $kategori) {
        \App\Models\Kategori::updateOrCreate(
            ['nama_kategori' => $kategori['nama_kategori']],
            $kategori
        );
    }
    
    return redirect('/admin/laporan/pengaturan')->with('success', 'Default Kategori berhasil ditambahkan!');
})->middleware('auth');

Route::get('/reset-bahan', function () {
    try {
        // Hapus semua data di tabel bahan_bakus dan bahan_stok_layers
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bahan_stok_layers')->truncate();
        DB::table('bahan_bakus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return "<h1 style='color:green; text-align:center;'>Data bahan baku berhasil direset.</h1>";
    } catch (\Exception $e) {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return "<h1 style='color:red; text-align:center;'>Gagal mereset data bahan baku: " . e($e->getMessage()) . "</h1>";
    }
})->middleware('auth');

// Route untuk form input manual bahan
Route::get('/operator/input-manual-bahan', function () {
    return view('operator.input-manual-bahan');
})->name('operator.input-manual-bahan');
Route::get('/admin/warna', [\App\Http\Controllers\Admin\WarnaController::class, 'index'])->name('admin.warna.index');