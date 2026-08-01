<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Notifications\StokKritisNotification;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use App\Models\Bom;
use App\Models\ProdukBom;
use App\Notifications\PerintahProduksiNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ProduksiController extends Controller
{
    /**
     * Menampilkan halaman monitoring produksi untuk Admin.
     */
    public function index(Request $request)
    {
        $query = Produksi::with(['produk', 'operator', 'produk_jadi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_spk', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($subq) use ($search) {
                      $subq->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $query->when($request->filled('operator_id') && $request->operator_id !== 'semua', function($q) use ($request) {
            return $q->where('operator_id', $request->operator_id);
        });

        $sortBy = $request->get('sort_by', 'terbaru');
        switch ($sortBy) {
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            case 'target_terdekat':
                $query->orderBy('tanggal_target', 'asc');
                break;
            case 'target_terjauh':
                $query->orderBy('tanggal_target', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $produksis = $query->paginate(10)->withQueryString();
        $operators = User::where('role', 'operator')->orderBy('name')->get();

        // Jika user adalah operator, tampilkan view operator
        if (auth()->check() && auth()->user()->role === 'operator') {
            // Group data untuk view operator
            $grouped = $produksis->groupBy(fn($item) => $item->produk_jadi_id . '-' . $item->status);
            $pendingTasks = collect();
            $processTasks = collect();
            $doneTasks = collect();
            
            foreach ($grouped as $items) {
                $first = $items->first();
                $groupedItem = (object)[
                    'id' => $first->id,
                    'produk_jadi_id' => $first->produk_jadi_id,
                    'product' => $first->produk_jadi,
                    'jumlah_produksi' => (int) $items->sum('jumlah_produksi'),
                    'jumlah_selesai' => (int) $items->sum('jumlah_selesai'),
                    'status' => $first->status,
                    'created_at' => $items->max('created_at'),
                    'tanggal_target' => $items->min('tanggal_target'),
                    'tanggal_mulai' => $items->min('tanggal_mulai'),
                    'kode_spk_list' => $items->pluck('kode_spk')->unique()->values(),
                    'total_spk' => $items->count(),
                ];
                
                if ($first->status === 'PENDING') {
                    $pendingTasks->push($groupedItem);
                } elseif ($first->status === 'PROSES') {
                    $processTasks->push($groupedItem);
                } elseif ($first->status === 'SELESAI') {
                    $doneTasks->push($groupedItem);
                }
            }
            
            return view('operator.tugas.index', compact('produksis', 'pendingTasks', 'processTasks', 'doneTasks', 'operators'));
        }

        return view('admin.produksi.index', compact('produksis', 'operators'));
    }

    /**
     * Halaman Perintah Produksi untuk Admin.
     */
    public function perintahProduksi()
    {
        // Hanya tampilkan operator dengan sub_role 'kepala_gudang' dan aktif
        $operators = User::where('role', 'operator')->where('sub_role', 'kepala_gudang')->where('is_active', true)->get();
        $boms = ProdukJadi::with(['boms.bahanBaku'])->get();

        $boms = $boms->map(function ($produk) {
            $produk->loadMissing('boms.bahanBaku');
            $produk->status_warna = 'green';
            $produk->is_ready = $produk->is_ready;
            $produk->reason = $produk->reason;
            $produk->max_production = $produk->max_production;

            if (!$produk->is_ready) {
                $produk->status_warna = 'red';
            } elseif ($produk->max_production < 10) {
                $produk->status_warna = 'yellow';
            }

            return $produk;
        });

        $productsForJs = $boms->mapWithKeys(function ($produk) {
            return [$produk->id => [
                'id'       => $produk->id,
                'nama'     => $produk->nama_produk,
                'foto'     => $produk->foto ?? '',
                'kode'     => $produk->kode_produk ?? '',
                'warna'    => $produk->warna ?? '',
                'motif'    => $produk->jenis_motif ?? '',
                'stok'     => $produk->stok ?? 0,
                'max'      => $produk->max_production,
                'is_ready' => $produk->is_ready,
                'reason'   => $produk->reason,
            ]];
        });

        return view('admin.produksi.perintah_produksi', compact('operators', 'boms', 'productsForJs'));
    }

    /**
     * Store a newly created SPK (bulk, multi-product).
     */
/**
     * Store a newly created SPK (bulk, multi-product).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'operator_id'       => 'required',
            'tanggal_target'    => 'required|date|after_or_equal:today',
            'products'          => 'required|array|min:1',
            'products.*.bom_id' => 'required|exists:bom,id',
            'products.*.jumlah' => 'required|integer|min:1',
            'products.*.catatan'=> 'nullable|string|max:255',
        ], [
            'products.required'              => 'Pilih setidaknya satu produk untuk dibuatkan SPK.',
            'tanggal_target.after_or_equal'  => 'Tanggal target tidak boleh sebelum tanggal mulai.',
        ]);

        DB::beginTransaction();

        try {
            $createdSpks = [];
            $operatorId  = $validated['operator_id'] === 'all' ? null : $validated['operator_id'];

// ── Validasi ketersediaan bahan baku ──────────────────────────
$bahanDibutuhkan = [];
foreach ($validated['products'] as $productData) {
    $mainBom   = Bom::findOrFail($productData['bom_id']);
    $productId = $mainBom->id_produk;
    $bomItems  = Bom::where('id_produk', $productId)->with('bahanBaku')->get();

    if ($bomItems->isEmpty()) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'products' => 'Produk dengan BOM ID ' . $productData['bom_id'] . ' tidak memiliki resep bahan baku.',
        ]);
    }

    foreach ($bomItems as $item) {
        if (!$item->bahanBaku) continue;

        $totalKebutuhan = $item->jumlah_kebutuhan * $productData['jumlah'];
        
        // Gunakan kode_material sebagai key agar stok digabung semua varian
        $kodeMaterial = $item->bahanBaku->kode_material;
        if (!isset($bahanDibutuhkan[$kodeMaterial])) {
            // Hitung total stok dari semua varian dengan kode_material yang sama
            $totalStokSemuaVarian = (float) \App\Models\BahanBaku::where('kode_material', $kodeMaterial)->sum('stok');
            $bahanDibutuhkan[$kodeMaterial] = [
                'nama'  => $item->bahanBaku->nama_material,
                'total' => 0,
                'stok'  => $totalStokSemuaVarian,
                'is_inventory' => $item->bahanBaku->is_inventory ?? 1,
                'kategori' => $item->bahanBaku->kategori ?? '',
            ];
        }
        $bahanDibutuhkan[$kodeMaterial]['total'] += $totalKebutuhan;
    }
}

foreach ($bahanDibutuhkan as $id => $bahan) {
    // ✅ PERBAIKAN: Skip validasi stok untuk bahan pelengkap ATAU is_inventory = 0
    if (($bahan['is_inventory'] ?? 1) == 0 || strtolower($bahan['kategori']) === 'bahan pelengkap') {
        continue;
    }
    
    if ($bahan['stok'] < $bahan['total']) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'products' => 'Stok bahan "' . $bahan['nama'] . '" tidak mencukupi. Dibutuhkan ' . $bahan['total'] . ', tersedia ' . $bahan['stok'] . '.',
        ]);
    }
}

// ── Potong stok (Dengan Logika FIFO yang Benar) ───────────────
foreach ($bahanDibutuhkan as $kodeMaterial => $bahan) {
    // ✅ PERBAIKAN: Skip pemotongan stok untuk bahan pelengkap ATAU is_inventory = 0
    if (($bahan['is_inventory'] ?? 1) == 0 || strtolower($bahan['kategori']) === 'bahan pelengkap') {
        continue;
    }

    $sisaKebutuhan = $bahan['total'];

    // Ambil semua varian bahan dengan kode_material ini, urut FIFO
    $semuaVarian = BahanBaku::where('kode_material', $kodeMaterial)
        ->orderBy('id', 'asc')
        ->get();

    foreach ($semuaVarian as $varian) {
        if ($sisaKebutuhan <= 0) break;

        // Ambil layer stok untuk varian ini
        $layers = \App\Models\BahanStokLayer::where('bahan_baku_id', $varian->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($layers as $layer) {
            if ($sisaKebutuhan <= 0) break;

            $ambil = min($layer->qty_sisa, $sisaKebutuhan);
            $layer->qty_sisa -= $ambil;
            $sisaKebutuhan -= $ambil;
            $layer->save();
        }

        // Potong stok di master bahan_bakus untuk varian ini
        $potongVarian = min($varian->stok, $bahan['total'] - ($sisaKebutuhan > 0 ? 0 : $bahan['total'] - $sisaKebutuhan));
        if ($potongVarian > 0) {
            BahanBaku::where('id', $varian->id)->decrement('stok', $potongVarian);
        }

        // Catat aktivitas di log_stok untuk varian ini
        DB::table('log_stok')->insert([
            'bahan_baku_id'    => $varian->id,
            'operator_id'      => auth()->id(), 
            'jenis_transaksi'  => 'PENGELUARAN_PRODUKSI',
            'jumlah'           => $ambil ?? 0,
            'keterangan'       => 'Pengeluaran otomatis untuk pembuatan SPK Baru (kode: ' . $kodeMaterial . ')',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}

            // ── Buat SPK per produk ───────────────────────────────────────
            foreach ($validated['products'] as $productData) {
                $bom = Bom::with('produk_jadi')->findOrFail($productData['bom_id']);

                if (!$bom->produk_jadi) {
                    throw new \Exception("BOM ID {$bom->id} tidak terhubung dengan produk manapun.");
                }

                $lastSpk   = Produksi::latest('id')->first();
                $nextNumber = $lastSpk ? $lastSpk->id + 1 : 1;
                $kodeSpk   = 'SPK-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

                $spk = Produksi::create([
                    'kode_spk'        => $kodeSpk,
                    'bom_id'          => $bom->id,
                    'produk_jadi_id'  => $bom->produk_jadi->id,
                    'operator_id'     => $operatorId,
                    'jumlah_produksi' => $productData['jumlah'],
                    'tanggal_mulai'   => now(),
                    'tanggal_target'  => $validated['tanggal_target'],
                    'catatan'         => $productData['catatan'],
                    'status'          => 'PENDING',
                ]);

                $createdSpks[] = $spk;
            }

            DB::commit();
            DB::commit();

            // ── PERBAIKAN LOGIKA NOTIFIKASI DISINI (ANTI-BOCOR & ANTI-NONAKTIF) ──
            $adminName = auth()->user()->name ?? 'Admin';

            // 1. Kirim notifikasi ke Kepala Gudang yang ditugaskan (HANYA JIKA AKUN AKTIF)
            if ($operatorId) {
                $kepalaGudang = User::where('id', $operatorId)
                    ->where('is_active', true) // Pastikan akun aktif
                    ->first();
                
                if ($kepalaGudang && $kepalaGudang->email) {
                    // Parameter ke-3 bernilai true karena dia Kepala Gudang (Dapat Link)
                    Notification::send($kepalaGudang, new PerintahProduksiNotification($createdSpks, $adminName, true));
                }
            } else {
                // Jika Admin memilih opsi "All" (Kirim ke semua Kepala Gudang yang AKTIF)
                $semuaKepalaGudang = User::where('role', 'operator')
                    ->where('sub_role', 'kepala_gudang')
                    ->where('is_active', true) // Pastikan akun aktif
                    ->whereNotNull('email')
                    ->get();
                
                if ($semuaKepalaGudang->isNotEmpty()) {
                    Notification::send($semuaKepalaGudang, new PerintahProduksiNotification($createdSpks, $adminName, true));
                }
            }

            // 2. Kirim instruksi ke Pekerja biasa (HANYA YANG AKTIF & TANPA LINK LOGIN)
            $pekerjaAktif = User::where('role', 'operator')
                ->where('sub_role', 'pekerja')
                ->where('is_active', true) // Proteksi: Akun nonaktif fiks tidak akan dapat email
                ->whereNotNull('email')
                ->get();

            if ($pekerjaAktif->isNotEmpty()) {
                // Parameter ke-3 diganti 'false' agar di dalam Gmail TIDAK ADA LINK LOGIN
                Notification::send($pekerjaAktif, new PerintahProduksiNotification($createdSpks, $adminName, false));
            }
            // ───────────────────────────────────────────────────────────────────

            $total = count($createdSpks);
            return redirect()->route('admin.produksi.index')
                ->with('success', "✅ BERHASIL! {$total} Surat Perintah Kerja baru telah diterbitkan. Bahan baku otomatis dipotong dari gudang.");

        } catch (\Exception $e) {
            // Ubah tanda \ menjadi / agar tidak bikin crash JavaScript XAMPP
            $pesanAman = str_replace('\\', '/', $e->getMessage());
            return back()->withInput()->with('error', 'Gagal kirim tugas: ' . $pesanAman);
        }
    }

    /**
     * Hapus banyak SPK selesai sekaligus.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:produksis,id',
        ]);

        $count = Produksi::whereIn('id', $request->ids)
            ->where('status', 'SELESAI')
            ->delete();

        if ($count > 0) {
            $total = (float) count($createdSpks);
            return redirect()->route('admin.produksi.index')
                ->with('success', "Berhasil menghapus {$count} SPK yang sudah selesai.");
        }

        return redirect()->route('admin.produksi.index')
            ->with('error', 'Tidak ada SPK selesai yang dipilih atau gagal menghapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // OPERATOR AREA
    // ══════════════════════════════════════════════════════════════

    /**
     * Daftar tugas operator — di-group per kode produk + status.
     */
/**
     * Daftar tugas operator — di-group per kode produk + status + Fitur Filter.
     */
    public function operatorIndex(Request $request)
    {
        $operatorId = auth()->id();
        
        // 1. Ambil Input Filter dari Request
        $search = $request->query('search');
        $fromDate = $request->query('from');
        $toDate = $request->query('to');

        // 2. Query Dasar
        $query = Produksi::with(['product', 'operator', 'produk_jadi'])
            ->where('operator_id', $operatorId);

        // 3. Logika Pencarian (Nama Produk atau Kode SPK)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('nama_produk', 'like', "%{$search}%");
                })
                ->orWhere('kode_spk', 'like', "%{$search}%");
            });
        }

        // 4. Logika Filter Tanggal
        if ($fromDate && $toDate) {
            $query->whereBetween('updated_at', [
                \Carbon\Carbon::parse($fromDate)->startOfDay(),
                \Carbon\Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $produksis = $query->latest()->get();

        // 5. Grouping Data untuk Tampilan Dashboard
        $grouped = $produksis->groupBy(fn($item) => $item->produk_jadi_id . '-' . $item->status);

        $result = collect();
        foreach ($grouped as $items) {
            $first = $items->first();

            $result->push((object)[
                'id'              => $first->id,
                'produk_jadi_id'  => $first->produk_jadi_id,
                'product'         => $first->produk_jadi,
                // ✅ Perbaikan: Pakai (int) agar angka seperti 7,94 otomatis jadi 7
                'jumlah_produksi' => (int) $items->sum('jumlah_produksi'),
                'jumlah_selesai'  => (int) $items->sum('jumlah_selesai'),
                'status'          => $first->status,
                'created_at'      => $items->max('created_at'),
                'tanggal_target'  => $items->min('tanggal_target'),
                'tanggal_mulai'   => $items->min('tanggal_mulai'),
                'kode_spk_list'   => $items->pluck('kode_spk')->unique()->values(),
                'total_spk'       => $items->count(),
            ]);
        }

        $pendingTasks = $result->where('status', 'PENDING')->values();
        $processTasks = $result->where('status', 'PROSES')->values();
        $doneTasks    = $result->where('status', 'SELESAI')->values();

        // Kirim data filter balik ke view agar input tetap terisi
        return view('operator.tugas.index_v2', compact('pendingTasks', 'processTasks', 'doneTasks', 'search', 'fromDate', 'toDate'));
    }

    /**
     * Detail tugas operator — menampilkan BOM + form input qty + gabungan SPK.
     */
    public function operatorDetail($id)
    {
        $produksi = Produksi::with(['operator', 'product'])
            ->where('operator_id', auth()->id())
            ->findOrFail($id);

        $produksi->loadMissing('produk_jadi.boms.material');

        // ── Hitung total gabungan semua SPK produk yang sama + status sama ─
        $baseQuery = Produksi::where('operator_id', auth()->id())
            ->where('produk_jadi_id', $produksi->produk_jadi_id)
            ->where('status', $produksi->status);

        $produksi->total_gabungan         = (int) $baseQuery->sum('jumlah_produksi');
        $produksi->total_selesai_gabungan = (int) $baseQuery->sum('jumlah_selesai');
        $produksi->total_sisa_gabungan    = $produksi->total_gabungan - $produksi->total_selesai_gabungan;
        $produksi->jumlah_spk_gabungan    = $baseQuery->count();

        return view('operator.produksi.detail', compact('produksi'));
    }

    /**
     * ✅ FIXED: Simpan progres produksi (PENDING → PROSES) dengan batch update yang benar.
     *
     * Bug sebelumnya: ->limit()->update() tidak deterministik di MySQL melalui Eloquent.
     * Solusi: ambil ID dulu dengan pluck(), lalu update pakai whereIn().
     */
    public function savePartialProduction(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $produksi = Produksi::where('operator_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            // ── Ambil jumlah dari hidden input (dikirim modal) ────────────
            // Prioritas: jumlah_produksi (dari hidden modal) → jumlah_selesai (fallback)
            $qtyNow = (int) $request->input('jumlah_produksi');
            if ($qtyNow < 1) {
                $qtyNow = (int) $request->input('jumlah_selesai', 1);
            }

            $produkJadiId = $produksi->produk_jadi_id;

            // ════════════════════════════════════════════════════════════
            // MODE PENDING → Konfirmasi & ubah ke PROSES (batch update)
            // ════════════════════════════════════════════════════════════
            if ($produksi->status === 'PENDING') {

                // 1. Hitung sisa SPK yang masih PENDING untuk produk ini
                $totalSisa = Produksi::where('produk_jadi_id', $produkJadiId)
                    ->where('operator_id', auth()->id())
                    ->where('status', 'PENDING')
                    ->sum('jumlah_produksi');

                // 2. Clamp agar tidak melebihi sisa yang tersedia
                $qtyNow = min($qtyNow, (int) $totalSisa);

                if ($qtyNow < 1) {
                    throw new \Exception('Tidak ada sisa produksi yang bisa dikerjakan.');
                }

                // ── BATCH UPDATE (CARA BENAR) ─────────────────────────
                // Langkah A: Ambil ID baris yang akan diupdate menggunakan pluck()
                //            supaya limit benar-benar membatasi jumlah baris.
                // Langkah B: Update hanya baris dengan ID tersebut pakai whereIn().
                // Ini menghindari bug MySQL di mana LIMIT pada UPDATE diabaikan.
                // ─────────────────────────────────────────────────────

                // Pendekatan yang lebih aman: proses satu per satu dari database
                $remainingToProcess = $qtyNow;
                $idsToUpdate = [];

                while ($remainingToProcess > 0) {
                    // Ambil baris PENDING tertua yang masih ada
                    $row = Produksi::where('produk_jadi_id', $produkJadiId)
                        ->where('operator_id', auth()->id())
                        ->where('status', 'PENDING')
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if (!$row) {
                        break; // Tidak ada lagi baris yang bisa diproses
                    }

                    $rowJumlah = (int) $row->jumlah_produksi;
                    $ambil = min($rowJumlah, $remainingToProcess);

                    if ($ambil == $rowJumlah) {
                        // Ambil SEMUA jumlah dari baris ini
                        $idsToUpdate[] = $row->id;
                        $remainingToProcess -= $rowJumlah;
                    } else {
                        // Hanya ambil SEBAGIAN, bagi barisnya
                        // Update baris asli
                        $row->jumlah_produksi = $ambil;
                        $row->save();
                        $idsToUpdate[] = $row->id;
                        $remainingToProcess -= $ambil;

                        // Buat baris baru untuk sisa
                        $sisa = $rowJumlah - $ambil;
                        if ($sisa > 0) {
                            $lastSpk   = Produksi::latest('id')->first();
                            $nextNumber = $lastSpk ? $lastSpk->id + 1 : 1;
                            $kodeSpk   = 'SPK-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                            
                            $newSpk = $row->replicate();
                            $newSpk->kode_spk = $kodeSpk;
                            $newSpk->jumlah_produksi = $sisa;
                            $newSpk->jumlah_selesai = 0;
                            $newSpk->status = 'PENDING';
                            $newSpk->tanggal_mulai_proses = null;
                            $newSpk->tanggal_selesai = null;
                            $newSpk->save();
                        }
                    }
                }

                if (empty($idsToUpdate)) {
                    throw new \Exception('Tidak ada SPK PENDING yang cocok untuk diproses.');
                }
                // --- 4. Update baris yang dipilih → PROSES --------------------
                // ✅ Update hanya ID yang sudah dipilih — dijamin tepat jumlahnya
                $rowsToUpdate = Produksi::whereIn('id', $idsToUpdate)->get();
                foreach ($rowsToUpdate as $row) {
                    $row->status = 'PROSES';
                    $row->operator_id = auth()->id();
                    $row->tanggal_mulai_proses = now();
                    if (is_null($row->tanggal_mulai)) {
                        $row->tanggal_mulai = now();
                    }
                    $row->save();
                }
                $jumlahTerupdate = count($idsToUpdate);

                DB::commit();

                return redirect()->route('operator.tugas.index')
                    ->with('success', "✅ BERHASIL! {$jumlahTerupdate} SPK (total ±{$qtyNow} pcs) berhasil dimulai. Cek tab PROSES.");
            }

            // ════════════════════════════════════════════════════════════
            // MODE PROSES → Update progress (jumlah_selesai)
            // Tidak ada pemotongan stok — stok sudah dipotong saat SPK dibuat.
            // ════════════════════════════════════════════════════════════
            $jumlahSelesai = min((float) $qtyNow, (float) $produksi->jumlah_produksi);

            $produksi->jumlah_selesai = $jumlahSelesai;
            $produksi->save();

            DB::commit();

            return redirect()->route('operator.tugas.index')
                ->with('success', 'Progress produksi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan progres produksi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan progres: ' . $e->getMessage());
        }
    }

    /**
     * Halaman "Lanjut" dari list tugas — tampilkan form selesaikan batch.
     * Menyertakan data gabungan semua SPK PROSES untuk produk yang sama.
     */
    public function operatorLanjut($id)
    {
        $produksi = Produksi::with(['operator', 'product'])
            ->findOrFail($id);

        if ($produksi->operator_id !== auth()->id()) {
            abort(403, 'AKSES DITOLAK. Anda tidak memiliki izin untuk melanjutkan tugas ini.');
        }

        $produksi->loadMissing('produk_jadi.boms.material');

        if ($produksi->status === 'SELESAI') {
            return redirect()->route('operator.tugas.index')->with('success', 'Tugas ini sudah selesai.');
        }

        // ── Hitung data SEMUA SPK produk yang sama (PENDING + PROSES) ──
        //   Sama seperti perhitungan di operatorDetail / detail.blade.php
        $allSpk = Produksi::where('operator_id', auth()->id())
            ->where('produk_jadi_id', $produksi->produk_jadi_id)
            ->get();

        $selesaiTotal = 0;
        $prosesTotal  = 0;
        $targetTotal  = 0;

        foreach ($allSpk as $spk) {
            $targetTotal  += (float) $spk->jumlah_produksi;
            $selesaiTotal += (float) $spk->jumlah_selesai;

            if ($spk->status === 'PROSES') {
                $prosesTotal += ((float) $spk->jumlah_produksi - (float) $spk->jumlah_selesai);
            }
        }

        $totalDiproses   = $selesaiTotal + $prosesTotal;
        $sisa            = max(0, $targetTotal - $totalDiproses);
        $progressPercent = $targetTotal > 0 ? ($totalDiproses / $targetTotal) * 100 : 0;

        $produksi->total_proses_gabungan      = (int) $targetTotal;
        $produksi->total_selesai_gabungan     = (int) $selesaiTotal;
        $produksi->total_sisa_proses_gabungan = (int) $sisa;
        $produksi->jumlah_spk_proses          = $allSpk->count();

        // ── Data tambahan untuk MAX_QTY (hitung sisa PROSES yang bisa diselesaikan) ──
        $produksi->sisa_proses_max_qty = (int) Produksi::where('operator_id', auth()->id())
            ->where('produk_jadi_id', $produksi->produk_jadi_id)
            ->where('status', 'PROSES')
            ->sum(DB::raw('jumlah_produksi - jumlah_selesai'));

        // ── Kumpulkan deadline unik dengan qty (sama seperti di detail.blade.php) ──
        $deadlineData = [];
        $deadlines = $allSpk->pluck('tanggal_target')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        foreach ($deadlines as $dl) {
            $spkByDl = $allSpk->where('tanggal_target', $dl);
            $deadlineData[] = [
                'tanggal'      => $dl,
                'total_qty'    => (int) $spkByDl->sum('jumlah_produksi'),
                'selesai_qty'  => (int) $spkByDl->sum('jumlah_selesai'),
                'jumlah_spk'   => $spkByDl->count(),
            ];
        }
        $produksi->deadlineData = $deadlineData;

        return view('operator.tugas.lanjut', compact('produksi'));
    }

    /**
     * ✅ BATCH SELESAI — logika pluck yang sama dengan PENDING → PROSES.
     *
     * Alur:
     *  1. Operator input qty yang ingin diselesaikan (bisa cicil).
     *  2. Ambil ID baris PROSES dengan akumulasi pluck (bukan limit langsung).
     *  3. Update status → SELESAI, set jumlah_selesai, tanggal_selesai.
     *  4. Tambah stok produk jadi sebesar total qty yang diselesaikan.
     *  5. Simpan foto bukti (multi-upload) ke semua baris yang diselesaikan.
     *  6. Kirim notifikasi ke admin.
     */
    public function operatorSelesai(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Produksi anchor — dipakai untuk konteks produk & operator
            $produksi = Produksi::with(['product', 'operator'])
                ->where('operator_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            $produkJadiId = $produksi->produk_jadi_id;
            $operatorId   = auth()->id();

            // ── 1. Ambil qty yang ingin diselesaikan ──────────────────────
            $qtySelesai = (int) $request->input('jumlah_selesai', 0);

            // Hitung total sisa PROSES untuk produk ini
            $totalProses = (int) Produksi::where('produk_jadi_id', $produkJadiId)
                ->where('operator_id', $operatorId)
                ->where('status', 'PROSES')
                ->sum('jumlah_produksi');

            if ($totalProses === 0) {
                DB::rollBack();
                return redirect()->route('operator.tugas.index')
                    ->with('info', 'Tidak ada SPK PROSES yang tersedia untuk diselesaikan.');
            }

            // Clamp — tidak boleh melebihi total yang sedang diproses
            $qtySelesai = max(1, min($qtySelesai, $totalProses));

            // ── 2. Pluck ID dengan akumulasi (cara yang benar) ────────────
            $prosesRows = Produksi::where('produk_jadi_id', $produkJadiId)
                ->where('operator_id', $operatorId)
                ->where('status', 'PROSES')
                ->orderBy('created_at', 'asc')
                ->get(['id', 'jumlah_produksi']);

// ✅ KODE YANG BENAR:
$idsToFinish = [];
$accumulated = 0;
foreach ($prosesRows as $row) {
    $rowJumlah = (int) $row->jumlah_produksi;
    
    // Cek SEBELUM menambah, jika sudah cukup maka berhenti
    if ($accumulated >= $qtySelesai) {
        break;
    }

    // Hitung berapa banyak yang bisa diambil dari baris ini
    $ambil = min($rowJumlah, $qtySelesai - $accumulated);
    
    // Tambahkan ID ke list untuk diupdate
    $idsToFinish[] = $row->id;
    $accumulated += $ambil;
}

if (empty($idsToFinish)) {
    throw new \Exception('Tidak ada SPK PROSES yang bisa diselesaikan.');
}

            // ── 3. Multi-upload foto bukti ────────────────────────────────
            // Form: <input type="file" name="foto_bukti[]" multiple accept="image/*">
            $fotoPaths = [];
            if ($request->hasFile('foto_bukti')) {
                $request->validate([
                    'foto_bukti'   => 'array|max:10',
                    'foto_bukti.*' => 'image|mimes:jpeg,png,jpg,webp|max:20480',
                ], [
                    'foto_bukti.*.max' => 'Ukuran file terlalu besar, maksimal adalah 20 MB.',
                    'foto_bukti.*.image' => 'Hanya file gambar (JPG, PNG, WEBP) yang diperbolehkan.',
                    'foto_bukti.*.mimes' => 'Format foto tidak didukung. Gunakan JPG, PNG atau WEBP.',
                    'foto_bukti.max' => 'Maksimal 10 foto yang bisa diupload sekaligus.',
                ]);
                foreach ($request->file('foto_bukti') as $file) {
                    $fotoPaths[] = $file->store('produksi/bukti', 'public');
                }
            }
            $fotoJson = count($fotoPaths) > 0 ? json_encode($fotoPaths) : null;

            // ── 4. Update baris yang dipilih → SELESAI ────────────────────
            $jumlahBarisDiselesaikan = Produksi::whereIn('id', $idsToFinish)
                ->update([
                    'status'          => 'SELESAI',
                    'jumlah_selesai'  => DB::raw('jumlah_produksi'), // tiap baris selesai 100%
                    'tanggal_selesai' => now(),
                    'foto_bukti'      => $fotoJson,  
                    'operator_id'     => auth()->id(),           // foto yang sama untuk semua baris kloter ini
                ]);

            // ── 5. Tambah stok produk jadi ────────────────────────────────
            // Total qty yang benar-benar diselesaikan (berdasarkan baris yang diupdate)
            $totalQtyDiselesaikan = Produksi::whereIn('id', $idsToFinish)->sum('jumlah_produksi');

            // ✅ Cari produk yang sudah ada berdasarkan produk_jadi_id
            $produkJadi = ProdukJadi::findOrFail($produkJadiId);

            // ✅ Tambah stok berdasarkan TOTAL qty yang benar-benar diselesaikan kloter ini
            $produkJadi->increment('stok', $totalQtyDiselesaikan);

            // ✅ UPDATE HARGA OTOMATIS SETELAH STOK DITAMBAHKAN
            // Hitung ulang harga jual dengan margin yang benar
            $margin = (float) Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50;
            
            // Hitung total HPP dari BOM
            $produkJadi->load('boms.material');
            $totalHpp = 0;
            foreach ($produkJadi->boms as $bom) {
                if ($bom->material) {
                    $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                    $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                    $totalHpp += $hargaSatuan * $jumlahKebutuhan;
                }
            }
            
            // Hitung harga jual otomatis dengan margin
            if ($totalHpp > 0) {
                $hargaJual = ceil($totalHpp + ($totalHpp * ($margin / 100)));
                $produkJadi->update(['harga_jual' => $hargaJual]);
                Log::info('Harga produk diupdate otomatis setelah produksi selesai', [
                    'produk_jadi_id' => $produkJadiId,
                    'hpp' => $totalHpp,
                    'margin' => $margin,
                    'harga_jual_baru' => $hargaJual
                ]);
            }

            // ── 6. Kirim notifikasi ke admin ──────────────────────────────
            $isProdNotifAktif = Pengaturan::where('nama_pengaturan', 'notif_produksi_selesai')->value('nilai') ?? '1';
            $isEmailAktif     = Pengaturan::where('nama_pengaturan', 'notif_gmail_aktif')->value('nilai') ?? '1';

            if ($isProdNotifAktif == '1' && $isEmailAktif == '1') {
                $admins = User::where('role', 'admin')->whereNotNull('email')->get();
                foreach ($admins as $admin) {
                    \Mail::to($admin)->send(new \App\Mail\SystemAlertMail(
                        '✅ Produksi Selesai: ' . $produksi->product->nama_produk,
                        'Batch Produksi Selesai — ' . $produksi->product->nama_produk,
                        'Operator ' . $produksi->operator->name . ' menyelesaikan kloter produksi.',
                        [
                            'Produk'        => $produksi->product->nama_produk,
                            'Jumlah Kloter' => $totalQtyDiselesaikan . ' Unit (' . count($idsToFinish) . ' SPK)',
                            'Foto Bukti'    => count($fotoPaths) . ' foto',
                            'Operator'      => $produksi->operator->name,
                            'Selesai Pada'  => now()->format('d/m/Y H:i'),
                        ],
                        'Lihat Produksi',
                        route('admin.produksi.index'),
                        'success'
                    ));

                    // Tambahkan Database Notification - ambil 1 instance Produksi untuk notifikasi
                    $produksiItem = Produksi::find($idsToFinish[0]);
                    $admin->notify(new \App\Notifications\ProduksiSelesaiNotification($produksiItem));
                }
            }

            // Tambahkan logging untuk validasi stok
            Log::info('Produksi Selesai - Stok Update', [
                'produk_jadi_id' => $produkJadiId,
                'stok_sebelum' => $produkJadi->stok,
                'jumlah_ditambahkan' => $totalQtyDiselesaikan,
                'stok_setelah' => $produkJadi->stok + $totalQtyDiselesaikan,
                'total_qty_diselesaikan' => $totalQtyDiselesaikan,
                'ids_spk_selesai' => $idsToFinish,
            ]);

            DB::commit();

            $sisaProses = (int) Produksi::where('produk_jadi_id', $produkJadiId)
                ->where('operator_id', $operatorId)
                ->where('status', 'PROSES')
                ->sum('jumlah_produksi');

                $totalQtySelesaiBersih = (float) $totalQtyDiselesaikan;
            $pesanSukses = "✅ {$totalQtyDiselesaikan} Pcs berhasil diselesaikan"
                . ($sisaProses > 0 ? " — masih ada {$sisaProses} Pcs lagi di tab PROSES." : ". Semua produksi selesai!");

            return redirect()->route('operator.tugas.index')->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyelesaikan produksi: ' . $e->getMessage());
            return redirect()->route('operator.tugas.index')
                ->with('error', 'Gagal menyelesaikan tugas: ' . $e->getMessage());
        }
    }

    /**
     * Detail produksi untuk Admin.
     */
    public function show($id)
    {
        $produksi = Produksi::with(['product', 'operator', 'produk_jadi.boms.bahanBaku'])
            ->findOrFail($id);

        // Flash jika datang dari notifikasi
        if (request()->get('from') === 'notification') {
            session()->flash('notif_success', '✅ Berhasil membuka detail produksi dari notifikasi');
        }

        // Helper function format angka tanpa trailing .00
        $formatQty = function($num) {
            $num = (float)$num;
            return fmod($num, 1) === 0.0 ? (int)$num : number_format($num, 2);
        };

        // Gunakan tampilan khusus jika status SELESAI
        if ($produksi->status === 'SELESAI') {
            return view('admin.produksi.review_selesai', compact('produksi', 'formatQty'));
        }

        // Jika user adalah operator, tampilkan view operator
        if (auth()->check() && auth()->user()->role === 'operator') {
            return view('operator.produksi.detail', compact('produksi'));
        }

        return view('admin.produksi.show', compact('produksi', 'formatQty'));
    }

    /**
     * Cancel SPK (hanya dalam 10 detik pertama, kembalikan stok).
     */
    public function cancelProduksi(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $produksi = Produksi::findOrFail($id);

            if ($produksi->status !== 'PENDING' || $produksi->created_at->diffInSeconds(now()) > 10) {
                return back()->with('error', 'SPK tidak dapat dibatalkan, waktu cancel sudah habis.');
            }

            $bomItems = Bom::where('id_produk', $produksi->produk_jadi_id)->with('bahanBaku')->get();
            foreach ($bomItems as $item) {
                if ($item->bahanBaku) {
                    BahanBaku::where('id', $item->id_bahan)
                        ->increment('stok', $item->jumlah_kebutuhan * $produksi->jumlah_produksi);
                }
            }

            $produksi->status = 'CANCELLED';
            $produksi->save();

            DB::commit();
            return back()->with('success', 'SPK berhasil dibatalkan. Stok bahan baku telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal cancel SPK: ' . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan SPK: ' . $e->getMessage());
        }
    }

    // ── Placeholder endpoints ──────────────────────────────────────────────
    public function autoLogin(Request $request)
    {
        $user = User::where('role', 'operator')->findOrFail((int) $request->query('user'));
        auth()->login($user);
        return redirect()->route('operator.dashboard');
    }

    /**
     * Halaman review produksi untuk Admin (mengarah ke show).
     */
    public function review($id)
    {
        return $this->show($id);
    }

    public function ambilTugas(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $produksi   = Produksi::where('operator_id', auth()->id())->where('status', 'PENDING')->lockForUpdate()->findOrFail($id);
            $sisaProduksi = $produksi->jumlah_produksi - $produksi->jumlah_selesai;
            $validated  = $request->validate(['jumlah' => 'required|numeric|min:1|max:' . $sisaProduksi]);
            $jumlahDiambil = (float) $validated['jumlah'];

            if ($jumlahDiambil < $produksi->jumlah_produksi) {
                $new = $produksi->replicate(['status', 'jumlah_produksi', 'jumlah_selesai', 'tanggal_mulai_proses', 'tanggal_selesai']);
                $new->status          = 'PENDING';
                $new->jumlah_produksi = $produksi->jumlah_produksi - $jumlahDiambil;
                $new->jumlah_selesai  = 0;
                $new->kode_spk        = ($produksi->kode_spk ?? 'SPK') . '-SISA-' . substr((string) now()->timestamp, -6);
                $new->save();
                $produksi->jumlah_produksi = $jumlahDiambil;
            }

            $produksi->status             = 'PROSES';
            $produksi->tanggal_mulai_proses = now();
            $produksi->tanggal_mulai      = $produksi->tanggal_mulai ?: now();
            $produksi->save();

            DB::commit();
            return redirect()->route('operator.tugas.lanjut', $produksi->id)
                ->with('success', "Tugas berhasil dimulai untuk {$jumlahDiambil} pcs.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai tugas: ' . $e->getMessage());
        }
    }

    public function startTaskAjax($id)
    {
        DB::beginTransaction();
        try {
            $produksi = Produksi::where('id', $id)
                ->where('operator_id', auth()->id())
                ->where('status', 'PENDING')
                ->lockForUpdate()
                ->firstOrFail();

            $produksi->status = 'PROSES';
            $produksi->tanggal_mulai_proses = now();
            $produksi->tanggal_mulai = $produksi->tanggal_mulai ?: now();
            $produksi->save();

            DB::commit();
            return response()->json(['success' => true, 'status' => 'PROSES', 'message' => 'Tugas berhasil dimulai.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal start task AJAX untuk produksi ID {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memulai tugas.'], 500);
        }
    }

    public function updateProductQty(Request $request, $productId)
    {
        return response()->json(['success' => false, 'message' => 'Fitur belum diaktifkan.']);
    }

    public function startProductProduction(Request $request, $productId)
    {
        return response()->json(['success' => false, 'message' => 'Fitur belum diaktifkan.']);
    }
    public function destroy($id)
{
    // Cari data produksinya
    $produksi = \App\Models\Produksi::find($id);

    // Cek kalau datanya ada
    if ($produksi) {
        // Cek status, biasanya operator cuma boleh hapus yang PENDING
        if ($produksi->status != 'PENDING') {
            return back()->with('error', 'Hanya tugas PENDING yang bisa dihapus, Bang!');
        }

        $produksi->delete();
        return redirect()->route('operator.tugas.index')->with('success', 'Data produksi berhasil dihapus!');
    }

    return back()->with('error', 'Aduh, datanya nggak ketemu!');
}
}