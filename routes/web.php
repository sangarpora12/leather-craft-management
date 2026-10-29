<?php

use App\Http\Controllers\Consumer\AuthController as ConsumerAuthController;
use App\Http\Controllers\Consumer\CartController;
use App\Http\Controllers\Consumer\CatalogController;
use App\Http\Controllers\Consumer\CheckoutController;
use App\Http\Controllers\Consumer\ProfileController;
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
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DangerZoneController;
use App\Http\Controllers\Admin\BahanBakuController as AdminBahanBakuController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\SatuanController as AdminSatuanController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. AREA PUBLIC (BELUM LOGIN)
Route::get('/', function () {
    return redirect('/login');
});

// Login & Forgot Password
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// // CONSUMER ROUTES (E-commerce for customers)
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

    // Cart & Checkout (supports guest checkout - no auth required)
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

    // Profile & Order History (requires auth)
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/profile/order/{orderId}', [ProfileController::class, 'orderDetail'])->name('profile.order.detail');
        Route::delete('/profile/delete', function () {
            $user = auth()->user();
            
            // Hapus semua data terkait user jika diperlukan
            // $user->orders()->delete(); // Jika ingin hapus semua pesanan
            
            // Hapus user dari database
            $user->delete();
            
            // Logout dan redirect ke login
            auth()->logout();
            return redirect()->route('login')->with('success', 'Akun Anda telah dihapus permanen.');
        })->name('profile.deleteAccount');
    });

    // Informational Pages (public)
    Route::get('/hubungi-kami', function () {
        return view('consumer.information.hubungi-kami');
    })->name('hubungi-kami');
    
    Route::get('/pengembalian-barang', function () {
        return view('consumer.information.pengembalian-barang');
    })->name('pengembalian-barang');
    
    Route::get('/garansi-produk', function () {
        return view('consumer.information.garansi-produk');
    })->name('garansi-produk');
    
    Route::get('/bantuan', function () {
        return view('consumer.information.bantuan');
    })->name('bantuan');
});

// Alias route untuk /katalog (lebih mudah diingat)
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog');
Route::get('/katalog/{id}', [CatalogController::class, 'show'])->name('katalog.show');

// // 2. AREA MEMBER (SUDAH LOGIN)
Route::middleware(['auth'])->group(function () {

    
    // --- REDIRECT DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Alias untuk kompatibilitas dengan referensi route('admin.dashboard') di seluruh codebase
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('admin.dashboard.chart-data');
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        return redirect()->route('login');
    })->name('logout');

    //     // 3. ADMIN & OPERATOR
    Route::middleware(['role:admin,operator'])->group(function () {

        // --- BAHAN BAKU ---
        Route::get('/bahan-baku/data', [BahanBakuController::class, 'index'])->name('bahan-baku.data');
        Route::get('/bahan-baku', [BahanBakuController::class, 'index'])->name('bahan-baku.index');
        Route::get('/bahan-baku/{bahanBaku}/edit', [BahanBakuController::class, 'edit'])->name('bahan-baku.edit');
        Route::put('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'update'])->name('bahan-baku.update');
        Route::delete('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('bahan-baku.destroy');
        Route::get('/bahan-baku/{bahanBaku}/varian/create', [BahanBakuController::class, 'createVarian'])->name('bahan-baku.varian.create');
        Route::post('/bahan-baku/{bahanBaku}/varian', [BahanBakuController::class, 'storeVarian'])->name('bahan-baku.varian.store');
        Route::get('/bahan-baku/{bahanBaku}/varian/{varian}/edit', [BahanBakuController::class, 'editVarian'])->name('bahan-baku.varian.edit');
        Route::put('/bahan-baku/{bahanBaku}/varian/{varian}', [BahanBakuController::class, 'updateVarian'])->name('bahan-baku.varian.update');
        Route::delete('/bahan-baku/{varian}/varian/delete', [BahanBakuController::class, 'destroyVarian'])->name('bahan-baku.varian.destroy');
        Route::get('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'show'])->name('bahan-baku.show');

        // --- PRODUK JADI ---
        Route::get('/produk-jadi/data', [ProdukJadiController::class, 'index'])->name('produk-jadi.data');
        Route::post('/produk-jadi/{produkJadi}/adjust-stock', [ProdukJadiController::class, 'adjustStock'])->name('produk-jadi.adjust-stock');
        Route::resource('produk-jadi', ProdukJadiController::class)->parameters([
            'produk-jadi' => 'produkJadi'
        ]);

        // --- PRODUKSI ---
        Route::get('/produksi/data', [ProduksiController::class, 'index'])->name('produksi.data');
        Route::get('/produksi', [ProduksiController::class, 'index'])->name('produksi.index');
        Route::get('/produksi/create', [ProduksiController::class, 'perintahProduksi'])->name('produksi.create');
        Route::post('/produksi', [ProduksiController::class, 'store'])->name('produksi.store');
        Route::get('/produksi/{produksi}', [ProduksiController::class, 'show'])->name('produksi.show');
        Route::delete('/produksi/{produksi}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
        Route::post('/produksi/{produksi}/selesai', function ($produksi) {
            $data = \App\Models\Produksi::findOrFail($produksi);
            $data->status = 'SELESAI';
            $data->tanggal_selesai = now();
            $data->save();
            return back()->with('success', 'Produksi berhasil ditandai selesai.');
        })->name('produksi.selesai');
        Route::get('/produksi/{produksi}/review', [ProduksiController::class, 'review'])->name('produksi.review');
        Route::post('/produksi/{produksi}/review', [ProduksiController::class, 'review'])->name('produksi.review.post');
        Route::get('/produksi/{id}/detail-operator', [ProduksiController::class, 'operatorDetail'])->name('produksi.operator.detail');
        Route::post('/produksi/{id}/set-operator', function ($id) {
            $operatorId = request()->input('operator_id');
            \App\Models\Produksi::where('id', $id)->update(['operator_id' => $operatorId]);
            return back()->with('success', 'Operator berhasil ditugaskan.');
        })->name('produksi.setOperator');
        Route::post('/produksi/{id}/set-pekerja', function ($id) {
            $pekerjaId = request()->input('pekerja_id');
            \App\Models\Produksi::where('id', $id)->update(['operator_id' => $pekerjaId]);
            return back()->with('success', 'Pekerja berhasil ditugaskan.');
        })->name('produksi.setPekerja');
        Route::post('/produksi/{produksi}/cancel', [ProduksiController::class, 'cancelProduksi'])->name('produksi.cancel');

        // --- BOM (Bill of Materials) ---
        Route::prefix('bom')->name('bom.')->group(function () {
            Route::get('/', [BomController::class, 'index'])->name('index');
            Route::get('/data', [BomController::class, 'index'])->name('data');
            Route::get('/resep/{produkJadi}', [BomController::class, 'edit'])->name('resep');
            Route::get('/create/{produkJadi}', [BomController::class, 'create'])->name('create');
            Route::post('/store/{produkJadi}', [BomController::class, 'store'])->name('store');
            Route::get('/{bom}/edit', [BomController::class, 'edit'])->name('edit');
            Route::put('/{bom}', [BomController::class, 'update'])->name('update');
            Route::delete('/{bom}', function ($bom) {
                \App\Models\Bom::findOrFail($bom)->delete();
                return back()->with('success', 'BOM berhasil dihapus.');
            })->name('destroy');
        });

        // --- PENGADAAN ---
        Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
            Route::get('/', [PengadaanController::class, 'index'])->name('index');
            Route::get('/data', [PengadaanController::class, 'index'])->name('data');
            Route::get('/create', [PengadaanController::class, 'create'])->name('create');
            Route::post('/store', [PengadaanController::class, 'store'])->name('store');
            Route::get('/{pengadaan}', [PengadaanController::class, 'index'])->name('show');
            Route::get('/{pengadaan}/edit', [PengadaanController::class, 'index'])->name('edit');
            Route::put('/{pengadaan}', function () {
                return back()->with('success', 'Data pengadaan berhasil diperbarui.');
            })->name('update');
            Route::delete('/{pengadaan}', function ($pengadaan) {
                \App\Models\Pengadaan::findOrFail($pengadaan)->delete();
                return back()->with('success', 'Pengadaan berhasil dihapus.');
            })->name('destroy');
            Route::post('/{pengadaan}/selesai', [PengadaanController::class, 'markAsDone'])->name('selesai');
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
        Route::post('/pengaturan', [PengaturanController::class, 'store'])->name('pengaturan.update');

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
        Route::post('/operator/{user}/send-verification', [AdminOperatorController::class, 'resendVerification'])->name('operator.send-verification');
        Route::post('/operator/{user}/toggle-active', [AdminOperatorController::class, 'toggleStatus'])->name('operator.toggle-active');
        Route::post('/operator/{user}/resend-verification', [AdminOperatorController::class, 'resendVerification'])->name('operator.resend-verification');
        Route::post('/operator/{user}/reset-password', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            $user->password = \Illuminate\Support\Facades\Hash::make('password123');
            $user->save();
            return back()->with('success', 'Password operator berhasil direset ke: password123');
        })->name('operator.reset-password');

        // --- NOTIFIKASI ---
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::get('/data', [NotifikasiController::class, 'index'])->name('data');
            Route::get('/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('read');
            Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read.post');
            Route::post('/mark-all-read', function () {
                return back()->with('success', 'Semua notifikasi ditandai dibaca.');
            })->name('markAllRead');
            Route::get('/unread-count', function () {
                return response()->json(['count' => 0]);
            })->name('unreadCount');
        });

        // --- ADMIN NOTIFICATIONS ALIAS (admin.notifications.*) ---
        // Route ini mengikuti routes/notifications.php yang TIDAK di-load
        // oleh bootstrap/app.php, jadi kita daftarkan ulang di sini.
        Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('api');
            Route::post('/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
            Route::get('/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('read');
            Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
            Route::post('/mark-all', [NotificationController::class, 'markAllRead'])->name('mark-all');
            Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        // --- WORKER MASTER (PEKERJA) ---
        Route::get('/pekerja-master', [WorkerMasterController::class, 'index'])->name('pekerja-master.index');
        Route::post('/pekerja-master', [WorkerMasterController::class, 'store'])->name('pekerja-master.store');
        Route::get('/pekerja-master/{pekerja}/edit', [WorkerMasterController::class, 'edit'])->name('pekerja-master.edit');
        Route::put('/pekerja-master/{pekerja}', [WorkerMasterController::class, 'update'])->name('pekerja-master.update');
        Route::delete('/pekerja-master/{pekerja}', [WorkerMasterController::class, 'destroy'])->name('pekerja-master.destroy');
        Route::get('/pekerja-master/{pekerja}/kinerja', [WorkerMasterController::class, 'kinerja'])->name('pekerja-master.kinerja');

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
        Route::post('/danger-zone/truncate', [DangerZoneController::class, 'resetData'])->name('danger-zone.truncate');
        Route::post('/danger-zone/delete-foto', [DangerZoneController::class, 'resetData'])->name('danger-zone.delete-foto');

        // --- ADMIN BAHAN BAKU ---
        Route::get('/admin/bahan-baku/data', [BahanBakuController::class, 'index'])->name('admin.bahan-baku.data');
        Route::get('/admin/bahan-baku', [BahanBakuController::class, 'index'])->name('admin.bahan-baku.index');
        Route::get('/admin/bahan-baku/{bahanBaku}/edit', [BahanBakuController::class, 'edit'])->name('admin.bahan-baku.edit');
        Route::put('/admin/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'update'])->name('admin.bahan-baku.update');
        Route::delete('/admin/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('admin.bahan-baku.destroy');
        Route::get('/admin/bahan-baku/{bahanBaku}/varian/create', [BahanBakuController::class, 'createVarian'])->name('admin.bahan-baku.varian.create');
        Route::post('/admin/bahan-baku/{bahanBaku}/varian', [BahanBakuController::class, 'storeVarian'])->name('admin.bahan-baku.varian.store');
        Route::get('/admin/bahan-baku/{bahanBaku}/varian/{varian}/edit', [BahanBakuController::class, 'editVarian'])->name('admin.bahan-baku.varian.edit');
        Route::put('/admin/bahan-baku/{bahanBaku}/varian/{varian}', [BahanBakuController::class, 'updateVarian'])->name('admin.bahan-baku.varian.update');
        Route::delete('/admin/bahan-baku/{varian}/varian/delete', [BahanBakuController::class, 'destroyVarian'])->name('admin.bahan-baku.varian.destroy');
        Route::get('/admin/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'show'])->name('admin.bahan-baku.show');

        // --- ADMIN PRODUK JADI ---
        Route::resource('/admin/produk-jadi', AdminProdukController::class)->parameters([
            'produk-jadi' => 'produkJadi'
        ])->names('admin.produk-jadi');
        Route::get('/admin/get-hpp-produk/{produkJadi}', [AdminProdukController::class, 'getHpp'])->name('admin.produk-jadi.get-hpp');

        // --- SISTEM ---
        Route::get('/sistem', function () {
            return view('admin.sistem.index');
        })->name('sistem');
        Route::post('/sistem/backup', [SistemController::class, 'resetData'])->name('sistem.backup');
        Route::post('/sistem/optimize', [SistemController::class, 'clearCache'])->name('sistem.optimize');
        Route::post('/sistem/clear-cache', [SistemController::class, 'clearCache'])->name('sistem.clear-cache');
        Route::post('/sistem/clear-log', [SistemController::class, 'clearCache'])->name('sistem.clear-log');

        // --- VERIFIKASI AKTIVASI OPERATOR VIA ADMIN ---
        Route::post('/operator/{user}/force-verify', function ($userId) {
            $user = User::findOrFail($userId);
            $user->email_verified_at = now();
            $user->is_active = true;
            $user->save();
            return back()->with('success', 'Akun operator berhasil diverifikasi dan diaktifkan.');
        })->name('operator.force-verify');

        // ====        // ALIAS ROUTES UNTUK KOMPATIBILITAS VIEW BLADE (PREFIX admin.*)
        // ====        // Banyak view admin memanggil route('admin.xxx') sementara route
        // aslinya terdaftar tanpa prefix 'admin.'. Blok ini menyediakan
        // alias agar semua pemanggilan route di view tidak error.

        // --- BOM ALIAS (admin.bom.*) ---
        Route::get('/admin/bom', [BomController::class, 'index'])->name('admin.bom.index');
        Route::get('/admin/bom/data', [BomController::class, 'index'])->name('admin.bom.data');
        Route::get('/admin/bom/resep/{produkJadi}', [BomController::class, 'edit'])->name('admin.bom.resep');
        Route::get('/admin/bom/create/{produkJadi}', [BomController::class, 'create'])->name('admin.bom.create');
        Route::post('/admin/bom/store/{produkJadi}', [BomController::class, 'store'])->name('admin.bom.store');
        Route::get('/admin/bom/{bom}/edit', [BomController::class, 'edit'])->name('admin.bom.edit');
        Route::put('/admin/bom/{bom}', [BomController::class, 'update'])->name('admin.bom.update');
        Route::delete('/admin/bom/{bom}', function ($bom) {
            \App\Models\Bom::findOrFail($bom)->delete();
            return redirect()->route('admin.bom.index')->with('success', 'BOM berhasil dihapus.');
        })->name('admin.bom.destroy');
        Route::post('/admin/bom/{produkJadi}/update-resep', [BomController::class, 'update'])->name('admin.bom.update-resep');
        Route::post('/admin/bom/{produkJadi}/update-spesifikasi', [BomController::class, 'update'])->name('admin.bom.update-spesifikasi');

        // --- BAHAN ALIAS (admin.bahan.*) ---
        Route::get('/admin/bahan', [BahanBakuController::class, 'index'])->name('admin.bahan.index');
        Route::post('/admin/bahan', [BahanBakuController::class, 'store'])->name('admin.bahan.store');
        Route::get('/admin/bahan/create', function () {
            $satuans = \App\Models\Satuan::all();
            $kategories = \App\Models\Kategori::where('tipe', 'bahan')->get();
            $warnas = \App\Models\Warna::all();
            return view('admin.bahan.create', compact('satuans', 'kategories', 'warnas'));
        })->name('admin.bahan.create');
        Route::get('/admin/bahan/{bahanBaku}/edit', [BahanBakuController::class, 'edit'])->name('admin.bahan.edit');
        Route::put('/admin/bahan/{bahanBaku}', [BahanBakuController::class, 'update'])->name('admin.bahan.update');
        Route::delete('/admin/bahan/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('admin.bahan.destroy');

        // --- BAHAN VARIAN ALIAS (admin.bahan-baku.update-varian) ---
        Route::put('/admin/bahan-baku/{bahanBaku}/varian/{varian}/update-varian', [BahanBakuController::class, 'updateVarian'])->name('admin.bahan-baku.update-varian');

        // --- PRODUK JADI COPY ---
        Route::post('/admin/produk-jadi/{produkJadi}/copy', function ($produkJadi) {
            $produk = \App\Models\ProdukJadi::findOrFail($produkJadi);
            $newProduk = $produk->replicate();
            $newProduk->nama_produk = $produk->nama_produk . ' (Copy)';
            $newProduk->save();
            foreach ($produk->boms as $bom) {
                $newBom = $bom->replicate();
                $newBom->id_produk = $newProduk->id;
                $newBom->save();
            }
            return redirect()->route('admin.bom.edit', $newProduk->id)->with('success', 'Produk berhasil disalin beserta resepnya.');
        })->name('admin.produk-jadi.copy');

        // --- PENGATURAN ALIAS (admin.pengaturan.*) ---
        Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan.index');
        Route::post('/admin/pengaturan', [PengaturanController::class, 'store'])->name('admin.pengaturan.store');
        Route::post('/admin/pengaturan/notifikasi', [NotifikasiController::class, 'store'])->name('admin.pengaturan.notifikasi.store');
        Route::post('/admin/pengaturan/danger-zone/reset', [DangerZoneController::class, 'resetData'])->name('admin.pengaturan.danger-zone.reset');
        Route::post('/admin/pengaturan/danger-zone/super-reset', [DangerZoneController::class, 'superReset'])->name('admin.pengaturan.danger-zone.super-reset');
        Route::post('/admin/pengaturan/reset-data', [DangerZoneController::class, 'resetData'])->name('admin.pengaturan.resetData');

        // --- PEKERJA MASTER ALIAS (admin.pekerja-master.*) ---
        Route::get('/admin/pekerja-master', [WorkerMasterController::class, 'index'])->name('admin.pekerja-master.index');
        Route::post('/admin/pekerja-master', [WorkerMasterController::class, 'store'])->name('admin.pekerja-master.store');
        Route::get('/admin/pekerja-master/create', [WorkerMasterController::class, 'index'])->name('admin.pekerja-master.create');
        Route::get('/admin/pekerja-master/{pekerja}/edit', [WorkerMasterController::class, 'edit'])->name('admin.pekerja-master.edit');
        Route::put('/admin/pekerja-master/{pekerja}', [WorkerMasterController::class, 'update'])->name('admin.pekerja-master.update');
        Route::delete('/admin/pekerja-master/{pekerja}', [WorkerMasterController::class, 'destroy'])->name('admin.pekerja-master.destroy');
        Route::get('/admin/pekerja-master/{pekerja}/kinerja', [WorkerMasterController::class, 'kinerja'])->name('admin.pekerja-master.kinerja');
        Route::get('/admin/pekerja-master/{pekerja}', function ($pekerja) {
            return redirect()->route('admin.pekerja-master.index');
        })->name('admin.pekerja-master.show');

        // --- OPERATOR ALIAS (admin.operator.*) ---
        Route::get('/admin/operator', [AdminOperatorController::class, 'index'])->name('admin.operator.index');
        Route::get('/admin/operator/create', [AdminOperatorController::class, 'create'])->name('admin.operator.create');
        Route::post('/admin/operator', [AdminOperatorController::class, 'store'])->name('admin.operator.store');
        Route::get('/admin/operator/{user}/edit', [AdminOperatorController::class, 'edit'])->name('admin.operator.edit');
        Route::put('/admin/operator/{user}', [AdminOperatorController::class, 'update'])->name('admin.operator.update');
        Route::delete('/admin/operator/{user}', [AdminOperatorController::class, 'destroy'])->name('admin.operator.destroy');

        // --- LAPORAN ALIAS (admin.laporan.*) ---
        Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
        Route::get('/admin/laporan/lengkap', [LaporanController::class, 'lengkap'])->name('admin.laporan.lengkap');
        Route::get('/admin/laporan/cetak-lengkap', [LaporanController::class, 'cetakLengkap'])->name('admin.laporan.cetak-lengkap');
        Route::get('/admin/laporan/operasional', [LaporanController::class, 'operasional'])->name('admin.laporan.operasional');
        Route::get('/admin/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.exportPdf');
        Route::get('/admin/laporan/cetak', [LaporanController::class, 'cetak'])->name('admin.laporan.cetak');
        Route::get('/admin/laporan/analisis', [LaporanController::class, 'index'])->name('admin.laporan.analisis');
        Route::get('/admin/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('admin.laporan.export-excel');

        // --- PRODUKSI ALIAS (admin.produksi.*) ---
        Route::get('/admin/produksi', [ProduksiController::class, 'index'])->name('admin.produksi.index');
        Route::get('/admin/produksi/data', [ProduksiController::class, 'index'])->name('admin.produksi.data');
        Route::get('/admin/produksi/create', [ProduksiController::class, 'perintahProduksi'])->name('admin.produksi.create');
        Route::post('/admin/produksi', [ProduksiController::class, 'store'])->name('admin.produksi.store');
        Route::get('/admin/produksi/{produksi}/edit', [ProduksiController::class, 'index'])->name('admin.produksi.edit');
        Route::get('/admin/produksi/{produksi}', [ProduksiController::class, 'show'])->name('admin.produksi.show');
        Route::put('/admin/produksi/{produksi}', function ($produksi) {
            return back()->with('success', 'Data produksi berhasil diperbarui.');
        })->name('admin.produksi.update');
        Route::delete('/admin/produksi/{produksi}', [ProduksiController::class, 'destroy'])->name('admin.produksi.destroy');
        Route::post('/admin/produksi/{produksi}/selesai', function ($produksi) {
            $data = \App\Models\Produksi::findOrFail($produksi);
            $data->status = 'SELESAI';
            $data->tanggal_selesai = now();
            $data->save();
            return back()->with('success', 'Produksi berhasil ditandai selesai.');
        })->name('admin.produksi.selesai');
        Route::get('/admin/produksi/{produksi}/review', [ProduksiController::class, 'review'])->name('admin.produksi.review');
        Route::post('/admin/produksi/{produksi}/review', [ProduksiController::class, 'review'])->name('admin.produksi.review.post');
        Route::post('/admin/produksi/bulk-destroy', function () {
            $ids = request()->input('ids', []);
            if (is_array($ids) && count($ids) > 0) {
                \App\Models\Produksi::whereIn('id', $ids)->delete();
                return back()->with('success', count($ids) . ' data produksi berhasil dihapus.');
            }
            return back()->with('error', 'Tidak ada data yang dipilih.');
        })->name('admin.produksi.bulk_destroy');
        Route::post('/admin/produksi/{produksi}/cancel', function ($produksi) {
            $data = \App\Models\Produksi::findOrFail($produksi);
            $data->status = 'DIBATALKAN';
            $data->save();
            return back()->with('success', 'SPK berhasil dibatalkan.');
        })->name('admin.produksi.cancel');
        Route::post('/admin/produksi/{produksi}/update-status', function ($produksi) {
            $data = \App\Models\Produksi::findOrFail($produksi);
            $status = request()->input('status', 'SELESAI');
            $data->status = $status;
            if ($status === 'SELESAI') {
                $data->tanggal_selesai = now();
            }
            $data->save();
            return back()->with('success', 'Status produksi berhasil diperbarui.');
        })->name('admin.produksi.update_status');

        // --- PENGADAAN ALIAS (admin.pengadaan.*) ---
        Route::get('/admin/pengadaan', [PengadaanController::class, 'index'])->name('admin.pengadaan.index');
        Route::get('/admin/pengadaan/data', [PengadaanController::class, 'index'])->name('admin.pengadaan.data');
        Route::get('/admin/pengadaan/create', [PengadaanController::class, 'create'])->name('admin.pengadaan.create');
        Route::post('/admin/pengadaan/store', [PengadaanController::class, 'store'])->name('admin.pengadaan.store');
        Route::get('/admin/pengadaan/{pengadaan}', [PengadaanController::class, 'index'])->name('admin.pengadaan.show');
        Route::get('/admin/pengadaan/{pengadaan}/edit', [PengadaanController::class, 'index'])->name('admin.pengadaan.edit');
        Route::put('/admin/pengadaan/{pengadaan}', [PengadaanController::class, 'index'])->name('admin.pengadaan.update');
        Route::delete('/admin/pengadaan/{pengadaan}', function ($pengadaan) {
            \App\Models\Pengadaan::findOrFail($pengadaan)->delete();
            return back()->with('success', 'Pengadaan berhasil dihapus.');
        })->name('admin.pengadaan.destroy');
        Route::post('/admin/pengadaan/{pengadaan}/selesai', [PengadaanController::class, 'markAsDone'])->name('admin.pengadaan.selesai');

        // --- PENJUALAN E-COMMERCE (DETAIL PENJUALAN) ---
        Route::get('/admin/penjualan', [PenjualanController::class, 'index'])->name('admin.penjualan.index');
        Route::get('/admin/penjualan/{id}', [PenjualanController::class, 'show'])->name('admin.penjualan.show');
        Route::put('/admin/penjualan/{id}/status', [PenjualanController::class, 'updateStatus'])->name('admin.penjualan.updateStatus');

        // --- PAKAI PRODUK / PRODUK KELUAR ALIAS ---
        Route::get('/admin/pakai-produk', function () {
            $transaksis = \App\Models\ProdukKeluar::latest()->paginate(15);
            return view('admin.pakai-produk.index', compact('transaksis'));
        })->name('admin.pakai-produk.index');
        Route::post('/admin/pakai-produk', function () {
            return back()->with('success', 'Data berhasil disimpan.');
        })->name('admin.pakai-produk.store');
        Route::delete('/admin/pakai-produk/{transaksi}', function ($transaksi) {
            \App\Models\ProdukKeluar::findOrFail($transaksi)->delete();
            return back()->with('success', 'Riwayat berhasil dihapus. Stok produk dikembalikan.');
        })->name('admin.pakai-produk.destroy');
        Route::get('/admin/produk-keluar', function () {
            $transaksis = \App\Models\ProdukKeluar::latest()->paginate(15);
            return view('admin.produk_keluar.index', compact('transaksis'));
        })->name('admin.produk-keluar.index');

        // --- SATUAN / KATEGORI / WARNA / MOTIF ALIAS ---
        Route::post('/admin/satuan', [AdminSatuanController::class, 'store'])->name('admin.satuan.store');
        Route::delete('/admin/satuan/{satuan}', [AdminSatuanController::class, 'destroy'])->name('admin.satuan.destroy');
        Route::put('/admin/satuan/{satuan}', [AdminSatuanController::class, 'update'])->name('admin.satuan.update');
        Route::post('/admin/kategori', function () {
            \App\Models\Kategori::create(['nama_kategori' => request()->input('nama_kategori'), 'tipe' => request()->input('tipe', 'bahan')]);
            return back()->with('success', 'Kategori berhasil ditambahkan.');
        })->name('admin.kategori.store');
        Route::delete('/admin/kategori/{kategori}', function ($kategori) {
            \App\Models\Kategori::findOrFail($kategori)->delete();
            return back()->with('success', 'Kategori berhasil dihapus.');
        })->name('admin.kategori.destroy');
        Route::post('/admin/warna', [WarnaController::class, 'store'])->name('admin.warna.store');
        Route::delete('/admin/warna/{warna}', [WarnaController::class, 'destroy'])->name('admin.warna.destroy');
        Route::put('/admin/warna/{warna}', [WarnaController::class, 'update'])->name('admin.warna.update');
        Route::post('/admin/motif', [MotifController::class, 'store'])->name('admin.motif.store');
        Route::delete('/admin/motif/{motif}', [MotifController::class, 'destroy'])->name('admin.motif.destroy');
        Route::put('/admin/motif/{motif}', [MotifController::class, 'update'])->name('admin.motif.update');

        // --- HAK AKSES ALIAS ---
        Route::get('/admin/hak-akses', function () {
            $users = \App\Models\User::whereIn('role', ['admin', 'operator'])->get();
            return view('admin.hak-akses.index', compact('users'));
        })->name('admin.hak-akses.index');
        Route::post('/admin/hak-akses/update-role/{user}', function ($userId) {
            $user = \App\Models\User::findOrFail($userId);
            $user->role = request()->input('role', $user->role);
            $user->save();
            return back()->with('success', 'Role user berhasil diperbarui.');
        })->name('admin.hak-akses.update-role');

        // --- LOG AKTIVITAS ALIAS ---
        Route::get('/admin/log-aktivitas', function () {
            $logs = \App\Models\LogStok::latest()->paginate(30);
            return view('admin.laporan.log-aktivitas.index', compact('logs'));
        })->name('admin.log-aktivitas.index');
        Route::post('/admin/log-aktivitas/cleanup', function () {
            \App\Models\LogStok::where('created_at', '<', now()->subDays(30))->delete();
            return back()->with('success', 'Log lebih dari 30 hari berhasil dihapus.');
        })->name('admin.log-aktivitas.cleanup');
        Route::post('/admin/log-aktivitas/destroy-all', function () {
            \App\Models\LogStok::query()->delete();
            return back()->with('success', 'Semua log berhasil dihapus.');
        })->name('admin.log-aktivitas.destroy-all');

        // --- PROFILE ALIAS ---
        Route::get('/admin/profile', function () {
            return view('admin.profile.index');
        })->name('admin.profile.index');
        Route::post('/admin/profile/update', function () {
            $user = auth()->user();
            $user->name = request()->input('name', $user->name);
            $user->email = request()->input('email', $user->email);
            if (request()->hasFile('foto_profil')) {
                $user->foto_profil = request()->file('foto_profil')->store('users', 'public');
            }
            $user->save();
            return back()->with('success', 'Profil berhasil diperbarui.');
        })->name('admin.profile.update');
        Route::post('/admin/profile/company', function () {
            return back()->with('success', 'Pengaturan perusahaan berhasil disimpan.');
        })->name('admin.profile.company');
        Route::post('/admin/profile/password', function () {
            $user = auth()->user();
            if (!\Illuminate\Support\Facades\Hash::check(request()->input('current_password'), $user->password)) {
                return back()->with('error', 'Password lama salah.');
            }
            $user->password = \Illuminate\Support\Facades\Hash::make(request()->input('password'));
            $user->save();
            return back()->with('success', 'Password berhasil diubah.');
        })->name('admin.profile.password');

        // --- SCAN ADMIN ALIAS ---
        Route::get('/admin/scan/pending', function () {
            return redirect()->route('scanner');
        })->name('admin.scan.pending');
        Route::post('/admin/scan/verify/{bahanMasuk}', function () {
            return back()->with('success', 'Bahan masuk berhasil diverifikasi.');
        })->name('admin.scan.verify');
        Route::post('/admin/scan/reject/{bahanMasuk}', function () {
            return back()->with('success', 'Bahan masuk berhasil ditolak.');
        })->name('admin.scan.reject');

        // --- MONITORING PRODUKSI ALIAS ---
        Route::get('/admin/monitoring-produksi', [ProduksiController::class, 'index'])->name('admin.monitoring.index');

        // --- OPERATOR DASHBOARD & BAHAN (utk view lama) ---
        Route::get('/operator/dashboard', [DashboardController::class, 'index'])->name('operator.dashboard');
        Route::get('/operator/notifications', [DashboardController::class, 'operatorNotifications'])->name('operator.notifications');
        Route::post('/operator/notifications/mark-all-read', [DashboardController::class, 'markAllOperatorNotificationsRead'])->name('operator.notifications.mark-all-read');
        Route::get('/operator/notifications/{id}/read', [DashboardController::class, 'markOperatorNotificationReadAndRedirect'])->name('operator.notifications.read');
        Route::post('/operator/bahan/store', [BahanBakuController::class, 'store'])->name('operator.bahan.store');
        Route::get('/operator/bahan/tambah', [BahanBakuController::class, 'index'])->name('operator.bahan.tambah');
        Route::get('/operator/bahan/create', [BahanBakuController::class, 'index'])->name('operator.bahan.create');
        Route::get('/operator/bahan', [BahanBakuController::class, 'index'])->name('operator.bahan.index');
        Route::get('/operator/bahan/{bahan}', [BahanBakuController::class, 'show'])->name('operator.bahan.show');
        Route::get('/operator/bahan/{bahan}/edit', [BahanBakuController::class, 'edit'])->name('operator.bahan.edit');
        Route::put('/operator/bahan/{bahan}', [BahanBakuController::class, 'update'])->name('operator.bahan.update');
        Route::delete('/operator/bahan/{bahan}', [BahanBakuController::class, 'destroy'])->name('operator.bahan.destroy');
        Route::get('/operator/bahan/{bahan}/edit-varian', [BahanBakuController::class, 'editVarian'])->name('operator.bahan.edit-varian');
        Route::put('/operator/bahan/{bahan}/varian/{varian}/update-varian', [BahanBakuController::class, 'updateVarian'])->name('operator.bahan.update-varian');
        Route::get('/operator/search', [DashboardController::class, 'operatorSearch'])->name('operator.search');
        Route::get('/operator/scan', [ScanController::class, 'index'])->name('operator.scan.index');
        Route::post('/operator/scan/process', [ScanController::class, 'processText'])->name('operator.scan.process');
        Route::post('/operator/scan/save', [ScanController::class, 'simpan'])->name('operator.scan.save');
        Route::get('/operator/tugas', [ProduksiController::class, 'index'])->name('operator.tugas.index');
        Route::get('/operator/tugas/{id}', [ProduksiController::class, 'show'])->name('operator.tugas.detail');
        Route::post('/operator/tugas/{id}/ambil', [ProduksiController::class, 'ambilTugas'])->name('operator.tugas.ambil');
        Route::get('/operator/tugas/{id}/lanjut', [ProduksiController::class, 'review'])->name('operator.tugas.lanjut');
        Route::post('/operator/produksi/{id}/selesai', [ProduksiController::class, 'review'])->name('operator.produksi.selesai');
        Route::post('/operator/produksi/{id}/proses', [ProduksiController::class, 'store'])->name('operator.produksi.proses');
        Route::post('/operator/produksi/{id}/partial', [ProduksiController::class, 'store'])->name('operator.produksi.partial');
        Route::post('/operator/produksi/{id}/request_bahan', [ProduksiController::class, 'store'])->name('operator.produksi.request_bahan');
        Route::delete('/operator/produksi/{id}', [ProduksiController::class, 'destroy'])->name('operator.produksi.destroy');
        Route::get('/operator/tugas/pengadaan', [PengadaanController::class, 'index'])->name('operator.tugas.pengadaan');
        Route::post('/operator/tugas/pengadaan/process', [PengadaanController::class, 'store'])->name('operator.tugas.pengadaan.process');
        Route::get('/operator/pengadaan', [PengadaanController::class, 'index'])->name('operator.pengadaan.index');
        Route::get('/operator/belanja', [PengadaanController::class, 'index'])->name('operator.belanja.index');
        Route::get('/operator/belanja/{namaMaterial}', [PengadaanController::class, 'index'])->name('operator.belanja.show');
        Route::post('/operator/belanja/{id}/selesai', [PengadaanController::class, 'markAsDone'])->name('operator.belanja.selesai');
        Route::get('/operator/resep-produk', [BomController::class, 'index'])->name('operator.resep-produk.index');
        Route::get('/operator/laporan', [DashboardController::class, 'operatorLaporan'])->name('operator.laporan.index');
        Route::get('/operator/laporan/cetak', [DashboardController::class, 'operatorLaporanCetak'])->name('operator.laporan.cetak');
        Route::get('/operator/laporan/pdf', [DashboardController::class, 'exportPdf'])->name('operator.laporan.pdf');
        Route::get('/operator/profile', function () {
            return view('operator.profile');
        })->name('operator.profile');
        Route::post('/operator/updateFoto', function () {
            $user = auth()->user();
            if (request()->hasFile('foto_profil')) {
                $user->foto_profil = request()->file('foto_profil')->store('users', 'public');
                $user->save();
            }
            return back()->with('success', 'Foto profil berhasil diperbarui.');
        })->name('operator.updateFoto');
        
        Route::delete('/operator/delete-account', function () {
            $user = auth()->user();
            
            // Hapus foto profil jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            // Hapus user dari database
            $user->delete();
            
            // Logout dan redirect ke login
            auth()->logout();
            return redirect()->route('login')->with('success', 'Akun kamu telah dihapus permanen.');
        })->name('operator.deleteAccount');
    });
});