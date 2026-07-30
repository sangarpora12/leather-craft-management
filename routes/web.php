<?php

use App\Http\Controllers\Consumer\AuthController as ConsumerAuthController;
use App\Http\Controllers\Consumer\CartController;
use App\Http\Controllers\Consumer\CatalogController;
use App\Http\Controllers\Consumer\CheckoutController;
// use App\Http\Controllers\Admin\ProfileController;
// use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ProdukJadiController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\Admin\WarnaController;
use App\Http\Controllers\Admin\MotifController;
use App\Http\Controllers\Admin\SistemController;
use App\Http\Controllers\Admin\WorkerMasterController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\DangerZoneController;
use App\Http\Controllers\Admin\BahanBakuController as AdminBahanBakuController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\SatuanController as AdminSatuanController;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. AREA PUBLIC (BELUM LOGIN)
// ==========================================
Route::get('/', function () {
    return redirect('/login');
});

// Login & Forgot Password
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

// ==========================================
// CONSUMER ROUTES (E-commerce for customers)
// ==========================================
Route::prefix('consumer')->name('consumer.')->group(function () {
    // Auth
    Route::get('/register', [ConsumerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ConsumerAuthController::class, 'register'])->name('register.process');
    Route::get('/login', [ConsumerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ConsumerAuthController::class, 'login'])->name('login.process');
    Route::post('/logout', [ConsumerAuthController::class, 'logout'])->name('logout');

    // Catalog - /consumer langsung tampil semua produk jadi (publik, tanpa wajib login)
    Route::get('/', [CatalogController::class, 'index'])->name('catalog');
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.alt');
    Route::get('/catalog/{id}', [CatalogController::class, 'show'])->name('catalog.show');

    // Cart & Checkout (requires auth)
    Route::middleware('auth')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add/{produkJadiId}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/buy-now/{produkJadiId}', [CartController::class, 'buyNow'])->name('cart.buynow');
        Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/order/success/{orderId}', [CheckoutController::class, 'success'])->name('order.success');
    });
});

// ==========================================
// 2. AREA MEMBER (SUDAH LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // --- REDIRECT DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        return redirect()->route('login');
    })->name('logout');

    // ==========================================
    // 3. ADMIN & OPERATOR
    // ==========================================
    Route::middleware(['role:admin,operator'])->group(function () {

        // --- BAHAN BAKU ---
        Route::get('/bahan-baku/data', [BahanBakuController::class, 'getData'])->name('bahan-baku.data');
        Route::resource('bahan-baku', BahanBakuController::class)->parameters([
            'bahan-baku' => 'bahanBaku'
        ]);
        Route::get('/bahan-baku/{bahanBaku}/varian/create', [BahanBakuController::class, 'createVarian'])->name('bahan-baku.varian.create');
        Route::post('/bahan-baku/{bahanBaku}/varian', [BahanBakuController::class, 'storeVarian'])->name('bahan-baku.varian.store');
        Route::get('/bahan-baku/{bahanBaku}/varian/{varian}/edit', [BahanBakuController::class, 'editVarian'])->name('bahan-baku.varian.edit');
        Route::put('/bahan-baku/{bahanBaku}/varian/{varian}', [BahanBakuController::class, 'updateVarian'])->name('bahan-baku.varian.update');
        Route::delete('/bahan-baku/{varian}/varian/delete', [BahanBakuController::class, 'destroyVarian'])->name('bahan-baku.varian.destroy');
        Route::get('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'show'])->name('bahan-baku.show');

        // --- PRODUK JADI ---
        Route::get('/produk-jadi/data', [ProdukJadiController::class, 'getData'])->name('produk-jadi.data');
        Route::resource('produk-jadi', ProdukJadiController::class)->parameters([
            'produk-jadi' => 'produkJadi'
        ]);

        // --- PRODUKSI ---
        Route::get('/produksi/data', [ProduksiController::class, 'getData'])->name('produksi.data');
        Route::resource('produksi', ProduksiController::class);
        Route::post('/produksi/{produksi}/selesai', [ProduksiController::class, 'selesai'])->name('produksi.selesai');
        Route::get('/produksi/{produksi}/review', [ProduksiController::class, 'reviewSelesai'])->name('produksi.review');
        Route::post('/produksi/{produksi}/review', [ProduksiController::class, 'reviewSelesaiPost'])->name('produksi.review.post');
        Route::get('/produksi/{id}/detail-operator', [ProduksiController::class, 'detailOperator'])->name('produksi.operator.detail');
        Route::post('/produksi/{id}/set-operator', [ProduksiController::class, 'setOperator'])->name('produksi.setOperator');
        Route::post('/produksi/{id}/set-pekerja', [ProduksiController::class, 'setPekerja'])->name('produksi.setPekerja');

        // --- BOM (Bill of Materials) ---
        Route::prefix('bom')->name('bom.')->group(function () {
            Route::get('/', [BomController::class, 'index'])->name('index');
            Route::get('/data', [BomController::class, 'getData'])->name('data');
            Route::get('/resep/{produkJadi}', [BomController::class, 'resep'])->name('resep');
            Route::get('/create/{produkJadi}', [BomController::class, 'create'])->name('create');
            Route::post('/store/{produkJadi}', [BomController::class, 'store'])->name('store');
            Route::get('/{bom}/edit', [BomController::class, 'edit'])->name('edit');
            Route::put('/{bom}', [BomController::class, 'update'])->name('update');
            Route::delete('/{bom}', [BomController::class, 'destroy'])->name('destroy');
        });

        // --- PENGADAAN ---
        Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
            Route::get('/', [PengadaanController::class, 'index'])->name('index');
            Route::get('/data', [PengadaanController::class, 'getData'])->name('data');
            Route::get('/create', [PengadaanController::class, 'create'])->name('create');
            Route::post('/store', [PengadaanController::class, 'store'])->name('store');
            Route::get('/{pengadaan}', [PengadaanController::class, 'show'])->name('show');
            Route::get('/{pengadaan}/edit', [PengadaanController::class, 'edit'])->name('edit');
            Route::put('/{pengadaan}', [PengadaanController::class, 'update'])->name('update');
            Route::delete('/{pengadaan}', [PengadaanController::class, 'destroy'])->name('destroy');
            Route::post('/{pengadaan}/selesai', [PengadaanController::class, 'selesai'])->name('selesai');
        });

        // --- LAPORAN ---
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/lengkap', [LaporanController::class, 'lengkap'])->name('lengkap');
            Route::get('/cetak-lengkap', [LaporanController::class, 'cetakLengkap'])->name('cetak-lengkap');
            Route::get('/operasional', [LaporanController::class, 'operasional'])->name('operasional');
            Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
            Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
            Route::post('/cetak', [LaporanController::class, 'cetak'])->name('cetak.post');
            Route::get('/operasional/cetak', [LaporanController::class, 'cetakOperasional'])->name('cetak-operasional');
        });

        // --- PENGATURAN ---
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        // --- SCANNER (OCR) ---
        Route::get('/scanner', [ScanController::class, 'scanner'])->name('scanner');
        Route::post('/scanner/scan', [ScanController::class, 'scan'])->name('scanner.scan');
        Route::get('/scanner/hasil/{noTransaksi}', [ScanController::class, 'hasil'])->name('scanner.hasil');
        Route::post('/scanner/simpan', [ScanController::class, 'simpan'])->name('scanner.simpan');
        Route::get('/scanner/nota/{nota}', [ScanController::class, 'showNota'])->name('scanner.nota.show');
        Route::post('/scanner/process-text', [ScanController::class, 'processText'])->name('scanner.process-text');
        Route::post('/scanner/process-ocr', [ScanController::class, 'processOcr'])->name('scanner.process-ocr');
        Route::get('/scanner/json', [ScanController::class, 'jsonNota'])->name('scanner.json');
        Route::post('/scanner/update-lokasi-bahan', [ScanController::class, 'updateLokasiBahan'])->name('scanner.update-lokasi-bahan');
        Route::post('/scanner/simpan-manual', [ScanController::class, 'simpanManual'])->name('scanner.simpan-manual');

        // --- OCR (UPLOAD GAMBAR) ---
        Route::get('/ocr', [OcrController::class, 'index'])->name('ocr');
        Route::post('/ocr/upload', [OcrController::class, 'upload'])->name('ocr.upload');
        Route::post('/ocr/proses', [OcrController::class, 'prosesOcr'])->name('ocr.proses');
        Route::get('/ocr/hasil/{noTransaksi}', [OcrController::class, 'hasil'])->name('ocr.hasil');
        Route::post('/ocr/simpan', [OcrController::class, 'simpan'])->name('ocr.simpan');

        // --- OPERATOR ---
        Route::get('/operator', [AdminOperatorController::class, 'index'])->name('operator.index');
        Route::get('/operator/create', [AdminOperatorController::class, 'create'])->name('operator.create');
        Route::post('/operator', [AdminOperatorController::class, 'store'])->name('operator.store');
        Route::get('/operator/{user}/edit', [AdminOperatorController::class, 'edit'])->name('operator.edit');
        Route::put('/operator/{user}', [AdminOperatorController::class, 'update'])->name('operator.update');
        Route::delete('/operator/{user}', [AdminOperatorController::class, 'destroy'])->name('operator.destroy');
        Route::post('/operator/{user}/send-verification', [AdminOperatorController::class, 'sendVerification'])->name('operator.send-verification');
        Route::post('/operator/{user}/toggle-active', [AdminOperatorController::class, 'toggleActive'])->name('operator.toggle-active');
        Route::post('/operator/{user}/resend-verification', [AdminOperatorController::class, 'resendVerification'])->name('operator.resend-verification');
        Route::post('/operator/{user}/reset-password', [AdminOperatorController::class, 'resetPasswordAdmin'])->name('operator.reset-password');

        // --- NOTIFIKASI ---
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::get('/data', [NotifikasiController::class, 'getData'])->name('data');
            Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('markAllRead');
            Route::get('/unread-count', [NotifikasiController::class, 'unreadCount'])->name('unreadCount');
        });

        // --- WORKER MASTER (PEKERJA) ---
        Route::get('/pekerja-master', [WorkerMasterController::class, 'index'])->name('pekerja-master.index');
        Route::post('/pekerja-master', [WorkerMasterController::class, 'store'])->name('pekerja-master.store');
        Route::get('/pekerja-master/{pekerja}/edit', [WorkerMasterController::class, 'edit'])->name('pekerja-master.edit');
        Route::put('/pekerja-master/{pekerja}', [WorkerMasterController::class, 'update'])->name('pekerja-master.update');
        Route::delete('/pekerja-master/{pekerja}', [WorkerMasterController::class, 'destroy'])->name('pekerja-master.destroy');
        Route::get('/pekerja-master/kinerja', [WorkerMasterController::class, 'kinerja'])->name('pekerja-master.kinerja');

        // --- WARNA ---
        Route::get('/warna', [WarnaController::class, 'index'])->name('warna.index');
        Route::post('/warna', [WarnaController::class, 'store'])->name('warna.store');
        Route::put('/warna/{warna}', [WarnaController::class, 'update'])->name('warna.update');
        Route::delete('/warna/{warna}', [WarnaController::class, 'destroy'])->name('warna.destroy');

        // --- MOTIF ---
        Route::get('/motif', [MotifController::class, 'index'])->name('motif.index');
        Route::post('/motif', [MotifController::class, 'store'])->name('motif.store');
        Route::put('/motif/{motif}', [MotifController::class, 'update'])->name('motif.update');
        Route::delete('/motif/{motif}', [MotifController::class, 'destroy'])->name('motif.destroy');

        // --- SATUAN ---
        Route::get('/satuan', [AdminSatuanController::class, 'index'])->name('satuan.index');
        Route::post('/satuan', [AdminSatuanController::class, 'store'])->name('satuan.store');
        Route::put('/satuan/{satuan}', [AdminSatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [AdminSatuanController::class, 'destroy'])->name('satuan.destroy');

        // --- VERIFIKASI AKTIVASI AKUN OPERATOR ---
        Route::get('/verify/{token}', function ($token) {
            // Default redirect
            $user = User::where('email_verified_at', null)->where('remember_token', $token)->first();
            if ($user) {
                if ($user->role !== 'operator') {
                    $user->role = 'operator';
                }
                $user->is_active = true;
                $user->email_verified_at = now();
                $user->save();
                return "<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'><title>Berhasil</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#0B0E14;display:flex;justify-content:center;align-items:center;min-height:100vh;color:#fff}.card{background:#1C222D;border:1px solid rgba(255,255,255,0.05);border-radius:20px;padding:50px 40px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.5)}.icon{width:80px;height:80px;background:rgba(16,185,129,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:50px;color:#10b981}h1{font-size:22px;font-weight:800;margin-bottom:8px;color:#10b981}p{font-size:13px;color:#94a3b8;line-height:1.6}.footer{margin-top:30px;font-size:11px;color:#4a5568}</style></head><body><div class='card'><div class='icon'>&#10003;</div><h1>Akun Anda Aktif!</h1><p>Selamat! Akun operator Anda telah berhasil diverifikasi dan aktif.<br>Sekarang Anda bisa menerima instruksi produksi.</p><div class='footer'>Berliano Leather &bull; Sistem Informasi Produksi</div></div></body></html>";
            }
            return abort(404, 'Token tidak valid.');
        })->name('operator.verify2');

        // --- DANGER ZONE ---
        Route::post('/danger-zone/truncate', [DangerZoneController::class, 'truncate'])->name('danger-zone.truncate');
        Route::post('/danger-zone/delete-foto', [DangerZoneController::class, 'deleteAllFoto'])->name('danger-zone.delete-foto');

        // --- ADMIN BAHAN BAKU ---
        Route::get('/admin/bahan-baku/data', [AdminBahanBakuController::class, 'getData'])->name('admin.bahan-baku.data');
        Route::resource('/admin/bahan-baku', AdminBahanBakuController::class)->parameters([
            'bahan-baku' => 'bahanBaku'
        ]);
        Route::get('/admin/bahan-baku/{bahanBaku}/varian/create', [AdminBahanBakuController::class, 'createVarian'])->name('admin.bahan-baku.varian.create');
        Route::post('/admin/bahan-baku/{bahanBaku}/varian', [AdminBahanBakuController::class, 'storeVarian'])->name('admin.bahan-baku.varian.store');
        Route::get('/admin/bahan-baku/{bahanBaku}/varian/{varian}/edit', [AdminBahanBakuController::class, 'editVarian'])->name('admin.bahan-baku.varian.edit');
        Route::put('/admin/bahan-baku/{bahanBaku}/varian/{varian}', [AdminBahanBakuController::class, 'updateVarian'])->name('admin.bahan-baku.varian.update');
        Route::delete('/admin/bahan-baku/{varian}/varian/delete', [AdminBahanBakuController::class, 'destroyVarian'])->name('admin.bahan-baku.varian.destroy');
        Route::get('/admin/bahan-baku/{bahanBaku}', [AdminBahanBakuController::class, 'show'])->name('admin.bahan-baku.show');

        // --- ADMIN PRODUK JADI ---
        Route::resource('/admin/produk-jadi', AdminProdukController::class)->parameters([
            'produk-jadi' => 'produkJadi'
        ]);

        // --- SISTEM ---
        Route::get('/sistem', [SistemController::class, 'index'])->name('sistem');
        Route::post('/sistem/backup', [SistemController::class, 'backup'])->name('sistem.backup');
        Route::post('/sistem/optimize', [SistemController::class, 'optimize'])->name('sistem.optimize');
        Route::post('/sistem/clear-cache', [SistemController::class, 'clearCache'])->name('sistem.clear-cache');
        Route::post('/sistem/clear-log', [SistemController::class, 'clearLog'])->name('sistem.clear-log');

        // --- VERIFIKASI AKTIVASI OPERATOR VIA ADMIN ---
        Route::post('/operator/{user}/force-verify', function ($userId) {
            $user = User::findOrFail($userId);
            $user->email_verified_at = now();
            $user->is_active = true;
            $user->save();
            return back()->with('success', 'Akun operator berhasil diverifikasi dan diaktifkan.');
        })->name('operator.force-verify');
    });
});