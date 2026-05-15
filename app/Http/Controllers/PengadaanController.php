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
        $query = Pengadaan::with('bahan', 'operator')->orderBy('tanggal_dibutuhkan', 'asc');
    
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('bahan', function($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                  ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }
    
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }
    
        $pengadaans = $query->paginate(15)->withQueryString();
    
        $kategories = \App\Models\Kategori::all();
        $suppliers = Supplier::all();
        // Hanya tampilkan operator dengan sub_role 'kepala_gudang' dan aktif
        $operators = User::where('role', 'operator')->where('sub_role', 'kepala_gudang')->where('is_active', true)->get();
    
        // 1. Ambil data mentah untuk Alpine.js
        // Ambil semua bahan baku KECUALI yang kategorinya Bahan Pelengkap
// (Pakai LOWER buat jaga-jaga kalau pas ngetik di database hurufnya besar/kecil)
$allBahanBakus = BahanBaku::whereRaw('LOWER(kategori) != ?', ['bahan pelengkap'])->get(); // <--- Pastikan baris ini ada!
        $uniqueBahanBakus = $allBahanBakus->unique(function ($item) {
            return $item['nama_material'] . $item['warna'] . $item['harga_satuan'];
        });
    
        // 2. Buat variabel $bahanBakus untuk Katalog (Urutkan yang ada fotonya dulu)
        $bahanBakus = $uniqueBahanBakus->groupBy('kode_material')->map(function ($group) {
            return $group->sort(function ($a, $b) {
                // Prioritas utama: item dengan foto harus selalu di atas.
                if (!empty($a->foto) && empty($b->foto)) return -1;
                if (empty($a->foto) && !empty($b->foto)) return 1;

                // Prioritas kedua: jika status foto sama, urutkan berdasarkan tanggal terbaru.
                return $b->updated_at <=> $a->updated_at;
            })->values(); // Re-index array setelah sorting
        });

        $batasStokGlobal = (int) (Pengaturan::where('nama_pengaturan', 'stok_minimum_bahan')->value('nilai') ?? 10);
        $criticalQuery = BahanBaku::where('is_inventory', 1);
        if ($batasStokGlobal == 0) {
            $criticalQuery->where('stok', '=', 0);
        } else {
            $criticalQuery->where('stok', '<=', $batasStokGlobal);
        }
        $lowStockMaterials = $criticalQuery->get()->groupBy('kode_material');
    
        // 4. Kirim semua ke view
        return view('admin.pengadaan.index', compact(
            'pengadaans', 
            'kategories', 
            'suppliers', 
            'operators', 
            'bahanBakus',        // <--- Pastikan namanya persis
            'lowStockMaterials', 
            'allBahanBakus'      // <--- Tambahkan ini juga buat Alpine.js
        ));
    }

    public function create()
    {
        return redirect()->route('admin.pengadaan.index');
    }

    public function showOperatorGroup($nama_material)
    {
        $user = Auth::user();
        $nama_material_decoded = trim(urldecode($nama_material));
    
        $items = Pengadaan::where('operator_id', $user->id)
            ->where('status', 'PENDING')
            ->whereHas('bahan', function ($query) use ($nama_material_decoded) {
                $query->where('nama_material', $nama_material_decoded);
            })
            ->with('bahan')
            ->orderBy('tanggal_dibutuhkan', 'asc')
            ->get();
    
        if ($items->isEmpty()) {
            return redirect()->route('operator.belanja.index')->with('error', 'Grup belanja tidak ditemukan.');
        }

        // --- PROSES GROUPING YANG AMAN ---
        // Kita kelompokkan berdasarkan Warna dan Status (Prioritas)
        $groupedItems = $items->groupBy(function($item) {
            $warna = strtolower(trim($item->warna ?? $item->bahan->warna ?? 'tidak_ada'));
            $statusUrgent = strtolower(trim($item->prioritas ?? 'biasa')); 
            return $warna . '-' . $statusUrgent;
        })->map(function($group) {
            $perwakilan = $group->first();
            
            // Bikin objek (stdClass) baru biar Eloquent nggak ngamuk
            $newItem = new \stdClass();
            $newItem->id = $perwakilan->id; // Ambil 1 ID untuk referensi
            $newItem->bahan = $perwakilan->bahan; // Bawa data relasi bahan bakunya
            $newItem->warna = $perwakilan->warna ?? $perwakilan->bahan->warna ?? '-';
            $newItem->prioritas = $perwakilan->prioritas ?? 'BIASA';
            
            // ✅ JUMLAHKAN QTY & AMBIL TANGGAL TERAWAL
            // Gunakan (float) agar Accessor nggak rewel
            $newItem->jumlah = (float) $group->sum('jumlah'); 
            $newItem->received = (float) $group->sum('received'); 
            $newItem->tanggal_dibutuhkan = $group->min('tanggal_dibutuhkan');
            
            return $newItem;
        })->values();
        // ... (kode yang ada sebelumnya) ...
        $groupedItems = $items->groupBy(function($item) {
            // ...
        })->map(function($group) {
            $perwakilan = $group->first();
            
            $newItem = new \stdClass();
            $newItem->id = $perwakilan->id; 
            $newItem->bahan = $perwakilan->bahan; 
            $newItem->warna = $perwakilan->warna ?? $perwakilan->bahan->warna ?? '-';
            $newItem->prioritas = $perwakilan->prioritas ?? 'BIASA';
            
            // ✅ TAMBAHKAN BARIS INI: Ambil catatan dari perwakilan
            $newItem->catatan = $perwakilan->catatan ?? null; 
            
            $newItem->jumlah = (float) $group->sum('jumlah'); 
            $newItem->received = (float) $group->sum('received'); 
            $newItem->tanggal_dibutuhkan = $group->min('tanggal_dibutuhkan');
            
            return $newItem;
        })->values();

        $firstItem = $items->first(); // Buat Header/Judul halaman
        $totalJumlah = (float) $items->sum('jumlah');
        $totalReceived = (float) $items->sum('received');
        $totalSisa = $totalJumlah - $totalReceived;
    
        // ✅ Bikin kloningan variabel biar Blade nggak cerewet nyariin
        $pengadaans = $groupedItems; 

        // ✅ Kirim SEMUANYA ke view
        return view('operator.belanja.show-group', compact(
            'items', 
            'groupedItems', 
            'pengadaans',     // <--- TAMBAHKAN INI
            'firstItem', 
            'totalJumlah', 
            'totalReceived', 
            'totalSisa', 
            'nama_material_decoded'
    ));
    }

    public function indexOperator(Request $request)
    {
        $user = Auth::user();

        $query = Pengadaan::where('operator_id', $user->id)
        ->whereIn('status', ['PENDING', 'PROSES'])
            ->whereHas('bahan') // hanya tampilkan yang bahannya masih ada
            ->with('bahan')
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_dibutuhkan', 'asc');

        // Filter pencarian berdasarkan nama bahan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('bahan', function($q) use ($search) {
                $q->where('nama_material', 'LIKE', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'overdue':
                    // Tanggal sudah lewat (kemarin atau sebelumnya)
                    $query->where('tanggal_dibutuhkan', '<', today()->startOfDay());
                    break;
                case 'urgent':
                    // Hanya yang prioritas URGENT
                    $query->where('prioritas', 'URGENT');
                    break;
                case 'on_time':
                    // Masih on time (hari ini atau setelahnya)
                    $query->where('tanggal_dibutuhkan', '>=', today()->startOfDay());
                    break;
            }
        }

        if ($request->filled('deadline_filter')) {
            switch ($request->deadline_filter) {
                case 'hari_ini':
                    $query->whereDate('tanggal_dibutuhkan', today());
                    break;
                case 'minggu_ini':
                    $query->whereBetween('tanggal_dibutuhkan', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'terlewat':
                    $query->where('tanggal_dibutuhkan', '<', today()->startOfDay());
                    break;
            }
        }

        $pendingPengadaan = $query->get();
        $groupedPengadaan = $pendingPengadaan->groupBy('bahan.nama_material');
        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return view('operator.belanja.index', compact('groupedPengadaan', 'unreadCount'));
    }

    public function showReceiveForm($id)
    {
        $pengadaan = Pengadaan::with('bahan')->findOrFail($id);
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

    public function receiveMaterial(Request $request, $id)
    {
        $request->validate([
            'jumlah_diterima' => 'required|numeric|min:0.01',
            'warna' => 'required|string',
            'harga_satuan' => 'nullable|numeric|min:0',
        ]);

        $jumlahDiterima = (float) str_replace(',', '.', $request->jumlah_diterima);
        $warnaInput = $request->warna;
        $hargaManual = $request->filled('harga_satuan') ? (float) $request->harga_satuan : null;

        try {
            DB::transaction(function () use ($id, $jumlahDiterima, $warnaInput, $hargaManual, $request) {
                $pengadaanSample = Pengadaan::with('bahan')->findOrFail($id);
                $namaMaterial = $pengadaanSample->bahan->nama_material;

                // 1. LOGIKA MASTER-VARIANT: Cari bahan yang Nama & Warnanya cocok (case-insensitive)
                $bahan = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                    ->whereRaw('LOWER(warna) = LOWER(?)', [$warnaInput])
                    ->first();

                if (!$bahan) {
                    $master = BahanBaku::where('nama_material', $namaMaterial)->first();
                    $kodeMaterial = $master ? $master->kode_material : $pengadaanSample->bahan->kode_material;

                    $bahan = BahanBaku::create([
                        'kode_material' => $kodeMaterial,
                        'nama_material' => $namaMaterial,
                        'warna'         => $warnaInput,
                        'stok'          => 0,
                        'satuan'        => $pengadaanSample->bahan->satuan,
                        'kategori'      => $pengadaanSample->bahan->kategori,
                        'harga_satuan'  => $hargaManual ?? $pengadaanSample->bahan->harga_satuan,
                        'is_inventory'  => true
                    ]);
                }

                // 2. UPDATE STOK
                $bahan->increment('stok', $jumlahDiterima);
                if ($hargaManual) { $bahan->update(['harga_satuan' => $hargaManual]); }

                // 3. UPDATE PENGADAAN INI DAHULU (prioritas utama)
                $sisaDistribusi = $jumlahDiterima;
                $pengadaan = Pengadaan::findOrFail($id);
                $sisaPesanan = $pengadaan->jumlah - ($pengadaan->received ?? 0);
                $yangDiambil = min($sisaDistribusi, $sisaPesanan);
                $pengadaan->received = ($pengadaan->received ?? 0) + $yangDiambil;
                if ($pengadaan->received >= $pengadaan->jumlah) { $pengadaan->status = 'DONE'; }
                $pengadaan->save();
                $sisaDistribusi -= $yangDiambil;

                // 4. FIFO: Jika masih ada sisa, distribusikan ke pengadaan PENDING lain dengan JOIN langsung
                if ($sisaDistribusi > 0) {
                    $warnaBersih = trim($warnaInput);
                    $isWarnaKosong = empty($warnaBersih) || strtolower($warnaBersih) === 'tidak ada warna';

                    $query = \Illuminate\Support\Facades\DB::table('pengadaans')
                        ->join('bahan_bakus', 'bahan_bakus.id', '=', 'pengadaans.bahan_baku_id')
                        ->whereRaw('LOWER(bahan_bakus.nama_material) = LOWER(?)', [$namaMaterial])
                        ->where('pengadaans.status', 'PENDING')
                        ->where('pengadaans.id', '!=', $id)
                        ->select('pengadaans.*')
                        ->orderBy('pengadaans.created_at', 'asc');

                    if (!$isWarnaKosong) {
                        $query->where(function($q) use ($warnaBersih) {
                            $q->whereRaw('LOWER(pengadaans.warna) = LOWER(?)', [$warnaBersih])
                              ->orWhereNull('pengadaans.warna')
                              ->orWhere('pengadaans.warna', '')
                              ->orWhereRaw('LOWER(pengadaans.warna) = ?', ['tidak ada warna']);
                        });
                    }

                    $antrianRaw = $query->get();
                    $ids = $antrianRaw->pluck('id')->toArray();
                    
                    if (!empty($ids)) {
                        $antrianPengadaan = Pengadaan::whereIn('id', $ids)->orderBy('created_at', 'asc')->get();
                        foreach ($antrianPengadaan as $p) {
                            if ($sisaDistribusi <= 0) break;
                            $sisaPesanan = $p->jumlah - ($p->received ?? 0);
                            $yangDiambil = min($sisaDistribusi, $sisaPesanan);

                            $p->received = ($p->received ?? 0) + $yangDiambil;
                            if ($p->received >= $p->jumlah) { $p->status = 'DONE'; }
                            $p->save();
                            $sisaDistribusi -= $yangDiambil;
                        }
                    }
                }

                // 5. CREATE STOK LAYER
                BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk'     => $jumlahDiterima,
                    'qty_sisa'      => $jumlahDiterima,
                    'harga_beli'    => $hargaManual ?? $bahan->harga_satuan,
                    'sumber_harga'  => 'Penerimaan Pengadaan',
                ]);
                // 6. CATAT RIWAYAT KE LOG STOK (Untuk Laporan)
                DB::table('log_stok')->insert([
                    'bahan_baku_id'   => $bahan->id,
                    'jumlah'          => $jumlahDiterima,
                    'jenis_transaksi' => 'PENGADAAN MASUK',
                    'keterangan'      => 'Penerimaan bahan dari pengadaan',
                    
                    // Ini kunci utamanya agar nama user muncul di laporan!
                    'operator_id'     => auth()->id(), 
                    // Catatan: Jika di LaporanController sebelumnya Anda mengubahnya 
                    // menjadi user_id, maka ubah tulisan 'operator_id' di atas menjadi 'user_id'
                    
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Bahan berhasil diterima dan varian warna diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menerima bahan: ' . $e->getMessage());
        }
    }

    public function requestMaterial(Request $request, $materialId)
    {
        $user = Auth::user();
        $material = BahanBaku::findOrFail($materialId);
        $needed = max(10 - $material->stok, 1);

        $pengadaan = Pengadaan::create([
            'no_referensi' => 'REQ-' . now()->format('YmdHis') . '-' . $materialId,
            'bahan_baku_id' => $materialId,
            'jumlah' => $needed,
            'prioritas' => 'BIASA',
            'tanggal_dibutuhkan' => now()->addDays(7)->toDateString(),
            'operator_id' => $user->id,
            'catatan' => 'Request otomatis dari operator untuk stok kritis',
            'status' => 'PENDING',
            'admin_id' => null,
        ]);

        NotificationService::approvalRequest($pengadaan);
        return back()->with('success', 'Pengadaan bahan berhasil diajukan ke admin.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'items.*.warna' => 'nullable|string',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'prioritas' => 'required|in:BIASA,URGENT',
            'tanggal_dibutuhkan' => 'required|date',
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $refGroup = 'REQ-' . date('YmdHis');
                $admin = Auth::user();
                $operator = User::findOrFail($request->operator_id);

                foreach ($request->items as $item) {
                    Pengadaan::create([
                        'no_referensi'      => $refGroup,
                        'bahan_baku_id'     => $item['bahan_baku_id'],
                        'warna'             => $item['warna'] ?? 'Tidak Ada Warna',
                        'jumlah'            => $item['jumlah'],
                        'prioritas'         => $request->prioritas,
                        'tanggal_dibutuhkan'=> $request->tanggal_dibutuhkan,
                        'operator_id'       => $operator->id,
                        'catatan'           => $item['catatan'] ?? null,
                        'status'            => 'PENDING',
                        'admin_id'          => $admin->id,
                    ]);
                }

                $isPengadaanNotifAktif = Pengaturan::where('nama_pengaturan', 'notif_pengadaan_baru')->value('nilai') ?? '1';
                if ($isPengadaanNotifAktif == '1') {
                    $pengadaanItems = Pengadaan::where('no_referensi', $refGroup)->with('bahan')->get();
                    if ($pengadaanItems->isNotEmpty()) {
                        Notification::send($operator, new PengadaanMaterialNotification($pengadaanItems, $admin));
                    }
                }
            });

            return redirect()->route('admin.pengadaan.index')->with('success', 'Berhasil menerbitkan Order Pengadaan dan notifikasi telah dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pengadaan: ' . $e->getMessage());
        }
    }

    public function markAsDone($id)
    {
        $pengadaan = Pengadaan::findOrFail($id);
        if ($pengadaan->operator_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk tugas ini.');
        }
        $pengadaan->update(['status' => 'DONE']);
        return redirect()->back()->with('success', 'Tugas pengadaan berhasil ditandai selesai.');
    }

    public function updatePhoto(Request $request, $kode_material)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $materials = BahanBaku::where('kode_material', $kode_material)->get();
            if ($materials->isEmpty()) {
                return response()->json(['error' => 'Material tidak ditemukan.'], 404);
            }

            $firstMaterial = $materials->first();
            if ($firstMaterial->foto) {
                Storage::disk('public')->delete($firstMaterial->foto);
            }

            $path = $request->file('photo')->store('bahan-photos', 'public');
            BahanBaku::where('kode_material', $kode_material)->update(['foto' => $path]);

            return response()->json([
                'success' => 'Foto berhasil diperbarui.',
                'new_photo_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui foto: ' . $e->getMessage()], 500);
        }
    }

    public function updatePhotoById(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $material = BahanBaku::find($id);
            if (!$material) {
                return response()->json(['error' => 'Material tidak ditemukan.'], 404);
            }

            if ($material->foto) {
                Storage::disk('public')->delete($material->foto);
            }

            $path = $request->file('photo')->store('bahan-photos', 'public');
            $material->update(['foto' => $path]);

            return response()->json([
                'success' => 'Foto berhasil diperbarui.',
                'new_photo_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui foto: ' . $e->getMessage()], 500);
        }
    }
}