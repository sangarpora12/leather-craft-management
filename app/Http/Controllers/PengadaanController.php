<?php

namespace App\Http\Controllers;

use App\Models\Pengadaan;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Models\User; // Untuk operator
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BahanMasukNotification;
use App\Notifications\PengadaanBahanNotification;
use App\Notifications\PengadaanMaterialNotification;
use Illuminate\Support\Facades\Notification;

class PengadaanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Pengadaan dengan filter
        $query = Pengadaan::with('bahan')->orderBy('tanggal_dibutuhkan', 'asc');

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('bahan', function($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                  ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Filter Kategori (dinonaktifkan sementara sampai kolom tersedia)
        // if ($request->filled('kategori_id') && $request->kategori_id != 'semua') {
        //     $kategoriId = $request->kategori_id;
        //     $query->whereHas('bahan', function($q) use ($kategoriId) {
        //         $q->where('kategori_id', $kategoriId);
        //     });
        // }

        $pengadaans = $query->paginate(15)->withQueryString();

        // 2. Ambil semua Kategori untuk filter
        $kategories = \App\Models\Kategori::all();

        // 3. Ambil semua bahan baku untuk Katalog
        $bahanBakus = BahanBaku::all();
    
        // 4. Ambil bahan baku kritis (Stok <= Stok Minimum ATAU Stok <= 5)
        // Ini menjamin "Plat Besi" stok 0 kamu pasti muncul di atas
        $lowStockMaterials = BahanBaku::where(function($query) {
            $query->whereRaw('stok <= stok_minimum')
                  ->orWhere('stok', '<=', 5);
        })->get();
    
        // 5. Ambil daftar operator untuk dropdown
        $operators = User::where('role', 'operator')->get();
    
        return view('admin.pengadaan.index', compact('pengadaans', 'kategories', 'bahanBakus', 'lowStockMaterials', 'operators'));
    }
    public function indexOperator(Request $request)
    {
        $user = Auth::user();

        // ✅ GROUPING BAHAN YANG SAMA (FITUR BARU)
        // Gabungkan semua order PENDING berdasarkan bahan_baku_id
        $groupedPengadaan = Pengadaan::where('status', 'PENDING')
            ->with('bahan')
            ->select('id', 'bahan_baku_id', 'jumlah', 'received', 'prioritas', 'tanggal_dibutuhkan', 'catatan')
            ->orderBy('tanggal_dibutuhkan', 'asc')
            ->get()
            ->groupBy('bahan_baku_id')
            ->map(function($items, $bahanId) {
                $firstItem = $items->first();
                $urgentItems = $items->where('prioritas', 'URGENT');
                $biasaItems = $items->where('prioritas', 'BIASA');
                
                return (object) [
                    'id' => $firstItem->id,
                    'bahan_baku_id' => $bahanId,
                    'bahan' => $firstItem->bahan,
                    'total_jumlah' => intval($items->sum('jumlah')),
                    'total_received' => intval($items->sum('received')),
                    'jumlah_order' => $items->count(),
                    'prioritas' => $items->contains(fn($i) => $i->prioritas == 'URGENT') ? 'URGENT' : 'BIASA',
                    'deadline_terdekat' => $items->min('tanggal_dibutuhkan'),
                    'semua_pengadaan_id' => $items->pluck('id'),
                    'catatan_admin' => $items->pluck('catatan')->filter()->unique()->values(),
                    'total_urgent' => intval($urgentItems->sum('jumlah')),
                    'total_biasa' => intval($biasaItems->sum('jumlah')),
                    'count_urgent' => $urgentItems->count(),
                    'count_biasa' => $biasaItems->count()
                ];
            })
            ->values();

        // Filter Deadline
        if ($request->filled('deadline_filter')) {
            switch ($request->deadline_filter) {
                case 'hari_ini':
                    $groupedPengadaan = $groupedPengadaan->where('deadline_terdekat', today());
                    break;
                case 'minggu_ini':
                    $groupedPengadaan = $groupedPengadaan->whereBetween('deadline_terdekat', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'terlewat':
                    $groupedPengadaan = $groupedPengadaan->where('deadline_terdekat', '<', today());
                    break;
            }
        }

        // Sort berdasarkan prioritas dan deadline
        $groupedPengadaan = $groupedPengadaan
            ->sortBy(fn($item) => $item->prioritas == 'URGENT' ? 0 : 1)
            ->sortBy('deadline_terdekat')
            ->values();

        // Paginasi manual
        $perPage = 10;
        $currentPage = request()->query('page', 1);
        $total = $groupedPengadaan->count();
        $pendingPengadaan = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedPengadaan->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Notifikasi untuk operator (gunakan NotificationService jika ada, fallback ke method lama)
        try {
            $unreadCount = \App\Services\NotificationService::getUnreadCountByRole('operator');
        } catch (\Exception $e) {
            $unreadCount = $user->notifications()->whereNull('read_at')->count();
        }

        return view('operator.belanja.index', compact('pendingPengadaan', 'unreadCount'));
    }

    /**
     * Tampilkan form penerimaan bahan baku (modal)
     */
    public function showReceiveForm($id)
    {
        $pengadaan = Pengadaan::with('bahan')->findOrFail($id);
        
        // Hitung sisa yang belum datang
        $sisa = $pengadaan->jumlah - ($pengadaan->received ?? 0);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengadaan->id,
                'no_referensi' => $pengadaan->no_referensi,
                'nama_bahan' => $pengadaan->bahan->nama_material ?? 'Bahan Tidak Ditemukan',
                'jumlah_diperintahkan' => $pengadaan->jumlah,
                'sudah_diterima' => $pengadaan->received ?? 0,
                'sisa_belum_datang' => $sisa,
                'satuan' => $pengadaan->bahan->satuan ?? 'pcs',
            ]
        ]);
    }

    /**
     * Proses penerimaan bahan baku (dengan database transaction)
     * Termasuk membuat record di bahan_stok_layers
     */
    public function receiveMaterial(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'jumlah_diterima' => 'required|numeric|min:1',
            'harga_satuan' => 'nullable|numeric|min:0',
        ]);

        $jumlahDiterima = (float) $request->jumlah_diterima;
        $jumlahAwal = $jumlahDiterima;
        $hargaManual = $request->filled('harga_satuan') ? (float) $request->harga_satuan : null;

        try {
            DB::transaction(function () use ($id, $jumlahDiterima, $jumlahAwal, $hargaManual, $request) {
                // 1. Ambil salah satu pengadaan sebagai referensi bahan
                $pengadaanSample = Pengadaan::findOrFail($id);
                $bahanId = $pengadaanSample->bahan_baku_id;

                // ✅ LOGIKA FIFO: KURANGI DARI PESANAN TERTUA DAHULU
                // Ambil SEMUA pengadaan PENDING untuk bahan ini, urut dari paling tua
                $antrianPengadaan = Pengadaan::where('bahan_baku_id', $bahanId)
                    ->where('status', 'PENDING')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $sisaUntukDistribusi = $jumlahDiterima;
                
                foreach($antrianPengadaan as $pengadaan) {
                    if ($sisaUntukDistribusi <= 0) break;
                    
                    $sisaPesanan = $pengadaan->jumlah - ($pengadaan->received ?? 0);
                    $yangDiambil = min($sisaUntukDistribusi, $sisaPesanan);
                    
                    // Kurangi antrian ini
                    $pengadaan->received = ($pengadaan->received ?? 0) + $yangDiambil;
                    $pengadaan->save();
                    
                    // Cek jika pesanan ini sudah selesai
                    if ($pengadaan->received >= $pengadaan->jumlah) {
                        $pengadaan->status = 'SELESAI';
                        $pengadaan->save();
                    }
                    
                    $sisaUntukDistribusi -= $yangDiambil;
                }

                // 3. UPDATE STOK GUDANG: Tambahkan ke stok bahan baku
                $bahan = BahanBaku::findOrFail($bahanId);
                $bahan->stok = ($bahan->stok ?? 0) + $jumlahAwal;
                $bahan->save();

                // 4. TENTUKAN HARGA DENGAN PRIORITAS:
                // Priority 1: OCR Input (dari session)
                // Priority 2: Supplier Price (dari tabel suppliers)
                // Priority 3: Manual Input (dari request)
                
                $hargaBeli = null;
                $sumberHarga = null;

                // Cek harga dari session OCR (jika ada)
                $hargaOcr = $request->session()->get('harga_ocr_' . $pengadaan->bahan_baku_id);
                
                // Ambil harga supplier dari tabel suppliers
                $hargaSupplier = null;
                if ($bahan->supplier) {
                    $supplierData = Supplier::where('nama_toko', $bahan->supplier)
                        ->where('nama_bahan', 'like', '%' . $bahan->nama_material . '%')
                        ->orderBy('harga', 'asc')
                        ->first();
                    if ($supplierData) {
                        $hargaSupplier = (float) $supplierData->harga;
                    }
                }

                // Terapkan logika prioritas
                if ($hargaOcr && $hargaOcr > 0) {
                    // Priority 1: OCR
                    $hargaBeli = (float) $hargaOcr;
                    $sumberHarga = 'OCR';
                } elseif ($hargaSupplier && $hargaSupplier > 0) {
                    // Priority 2: Supplier
                    $hargaBeli = $hargaSupplier;
                    $sumberHarga = 'Supplier: ' . $bahan->supplier;
                } elseif ($hargaManual && $hargaManual > 0) {
                    // Priority 3: Manual
                    $hargaBeli = $hargaManual;
                    $sumberHarga = 'Manual';
                } else {
                    // Jika tidak ada harga, gunakan harga_satuan dari bahan_baku
                    $hargaBeli = (float) $bahan->harga_satuan;
                    $sumberHarga = 'Bahan Baku';
                }

                // 5. CREATE STOK LAYER: Simpan ke tabel bahan_stok_layers
                BahanStokLayer::create([
                    'bahan_baku_id' => $pengadaan->bahan_baku_id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk' => $jumlahDiterima,
                    'qty_sisa' => $jumlahDiterima,
                    'harga_beli' => $hargaBeli,
                    'sumber_harga' => $sumberHarga,
                ]);

                // Hapus session OCR setelah digunakan
                $request->session()->forget('harga_ocr_' . $pengadaan->bahan_baku_id);

                // 6. CEK STATUS: Jika sudah semua diterima, ubah status menjadi SELESAI
                if ($pengadaan->received >= $pengadaan->jumlah) {
                    $pengadaan->status = 'SELESAI';
                    $pengadaan->save();
                }

                // === PENGIRIMAN NOTIFIKASI BAHAN MASUK (Setelah Stok diperbarui) ===
                $notifStokAktif = Pengaturan::where('nama_pengaturan', 'notif_stok_menipis')->value('nilai') ?? '1';
                if ($notifStokAktif == '1') {
                    Notification::send(User::where('role', 'admin')->get(), new BahanMasukNotification($bahan));
                }
            });
            return redirect()->back()->with('success', 'Penerimaan bahan berhasil! Stok dan histori harga telah diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menerima bahan: ' . $e->getMessage());
        }
    }

    public function requestMaterial(Request $request, $materialId)
    {
        $user = Auth::user();
        $material = BahanBaku::findOrFail($materialId);

        // Hitung jumlah yang dibutuhkan (misal minimum 10 dikurangi stok saat ini)
        $needed = max(10 - $material->stok, 1);

        // Buat pengadaan request
        $pengadaan = Pengadaan::create([
            'no_referensi' => 'REQ-' . now()->format('YmdHis') . '-' . $materialId,
            'bahan_baku_id' => $materialId,
            'jumlah' => $needed,
            'prioritas' => 'BIASA',
            'tanggal_dibutuhkan' => now()->addDays(7)->toDateString(),
            'operator_id' => $user->id,
            'catatan' => 'Request otomatis dari operator untuk stok kritis',
            'status' => 'PENDING',
            'admin_id' => null, // Belum ada admin yang menangani
        ]);

        // Notify admin about approval request
        NotificationService::approvalRequest($pengadaan);

        return back()->with('success', 'Pengadaan bahan berhasil diajukan ke admin.');
    }

    public function store(Request $request)
    {
        // Validasi input array
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'items.*.jumlah' => 'required|numeric|min:1',
            'prioritas' => 'required|in:BIASA,URGENT',
            'tanggal_dibutuhkan' => 'required|date',
            'operator_id' => 'nullable|exists:users,id', // Pastikan operator_id ada di tabel users
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Generate nomor referensi unik untuk grup pengadaan ini
                $refGroup = 'REQ-' . date('YmdHis');
                $admin = Auth::user(); // Ambil objek admin yang sedang login

                foreach ($request->items as $item) {
                    // 1. Simpan ke tabel pengadaan
                    Pengadaan::create([
                        'no_referensi'      => $refGroup,
                        'bahan_baku_id'     => $item['bahan_baku_id'],
                        'jumlah'            => $item['jumlah'],
                        'prioritas'         => $request->prioritas,
                        'tanggal_dibutuhkan'=> $request->tanggal_dibutuhkan,
                        'operator_id'       => $request->operator_id, // Bisa null jika tidak dipilih
                        'catatan'           => $item['catatan'] ?? null,
                        'status'            => 'PENDING',
                        'admin_id'          => $admin->id,
                    ]);
                }

                // 2. KIRIM NOTIFIKASI (DENGAN PENANGANAN ERROR YANG AMAN)
                try {
                    $isPengadaanNotifAktif = Pengaturan::where('nama_pengaturan', 'notif_pengadaan_baru')->value('nilai') ?? '1';

                    // Hanya kirim jika notif aktif dan operator dipilih
                    if ($isPengadaanNotifAktif == '1' && $request->filled('operator_id')) {
                        // Ambil objek User operator dari ID yang diberikan
                        $operator = User::find($request->operator_id);

                        // Ini adalah pemeriksaan paling penting: pastikan $operator adalah objek User
                        if ($operator instanceof User) {
                            // KIRIM SATU EMAIL PER NOMOR TRANSAKSI
                            $groupedPengadaan = Pengadaan::where('no_referensi', $refGroup)->with('bahan')->get();
                            
                            if ($groupedPengadaan->isNotEmpty()) {
                                // Kirim SATU email saja yang berisi SEMUA item dalam transaksi ini
                                Notification::send($operator, new PengadaanMaterialNotification($groupedPengadaan, $admin));
                            }
                        } else {
                            // Jika $operator bukan objek User, catat sebagai peringatan
                            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi: operator_id tidak valid.', [
                                'operator_id' => $request->operator_id
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Jika ada error saat kirim notifikasi, catat di log tapi JANGAN hentikan aplikasi
                    \Illuminate\Support\Facades\Log::error('EXCEPTION SAAT KIRIM NOTIFIKASI PENGADAAN: ' . $e->getMessage());
                }
            });

            return redirect()->route('admin.pengadaan.index')->with('success', 'Berhasil menerbitkan Order Pengadaan.');

        } catch (\Exception $e) {
            // Tangkap error jika transaksi database gagal
            return redirect()->back()->with('error', 'Gagal memproses pengadaan: ' . $e->getMessage());
        }
    }
    public function markAsDone($id)
    {
        $pengadaan = Pengadaan::findOrFail($id);

        // Pastikan operator yang login adalah yang bertugas
        if ($pengadaan->operator_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk tugas ini.');
        }

        // Update status menjadi DONE
        $pengadaan->update(['status' => 'DONE']);

        return redirect()->back()->with('success', 'Tugas pengadaan berhasil ditandai selesai.');
    }
}