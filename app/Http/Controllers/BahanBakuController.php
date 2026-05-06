<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BahanBakuController extends Controller
{
    public function show($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        
        // Ambil semua varian warna dari material yang SAMA (berdasarkan nama_material)
        $varianWarna = BahanBaku::where('nama_material', $bahan->nama_material)
            ->orderBy('warna')
            ->get();
        
        // Ambil riwayat stok lengkap dengan nama penginput (Admin/Operator)
        $riwayatStok = DB::table('log_stok')
            ->leftJoin('users', 'log_stok.operator_id', '=', 'users.id')
            ->where('log_stok.bahan_baku_id', $id)
            ->select('log_stok.*', 'users.name as nama_penginput')
            ->orderBy('log_stok.created_at', 'desc')
            ->limit(20)
            ->get();

        // Ambil ID dari SEMUA varian (nama_material sama) untuk query FIFO
        $semuaVarianIds = $varianWarna->pluck('id')->toArray();
        if (empty($semuaVarianIds)) {
            $semuaVarianIds = [$id];
        }

        // Ambil SEMUA layer FIFO dari semua varian
        $semuaLayer = \App\Models\BahanStokLayer::whereIn('bahan_baku_id', $semuaVarianIds)
            ->orderBy('tanggal_masuk', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by harga_beli untuk ringkasan per harga
        // Urutkan berdasarkan tanggal masuk TERAKHIR (desc) agar tag TERAKHIR benar
        $ringkasanPerHarga = \App\Models\BahanStokLayer::whereIn('bahan_baku_id', $semuaVarianIds)
            ->select('harga_beli', DB::raw('SUM(qty_masuk) as total_masuk'), DB::raw('SUM(qty_sisa) as total_sisa'), DB::raw('MAX(tanggal_masuk) as tgl_terakhir'))
            ->groupBy('harga_beli')
            ->orderBy('tgl_terakhir', 'desc')
            ->get();

        $warnas = \App\Models\Warna::all();
        return view('admin.bahan.show', compact('bahan', 'riwayatStok', 'varianWarna', 'warnas', 'semuaLayer', 'ringkasanPerHarga'));
    }
    // 1. TAMPILKAN HALAMAN UTAMA (INDEX) + PENCARIAN + FILTER (MASTER-VARIANT)
    public function index(Request $request)
    {
        // Query utama: GROUP BY master material, SUM stok dari semua varian warna
        $query = BahanBaku::select([
            'kode_material',
            'nama_material',
            'kategori',
            'satuan',
            DB::raw('SUM(stok) as total_stok'),
            DB::raw('MAX(harga_satuan) as harga_satuan'),
            DB::raw('MAX(id) as id'),
            DB::raw('MAX(foto) as foto'), // Foto dari varian manapun
        ]);
    
        // Filter Pencarian (Kode/Nama Material)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function($sub) use ($search) {
                $sub->where('nama_material', 'LIKE', "%{$search}%")
                  ->orWhere('kode_material', 'LIKE', "%{$search}%");
            });
        });
    
        // Filter Kategori
        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->where('kategori', $request->kategori);
        });
    
        // Filter Status Stok (berdasarkan total stok dari semua varian)
        $query->when($request->filled('status_stok'), function ($q) use ($request) {
            $batasStokGlobal = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
            
            // HAVING digunakan karena total_stok adalah hasil agregasi SUM
            if ($request->status_stok == 'kritis') {
                $q->having('total_stok', '>', 0)->having('total_stok', '<=', $batasStokGlobal);
            } elseif ($request->status_stok == 'habis') {
                $q->having('total_stok', 0);
            }
        });
    
        // GROUP BY untuk master material (bukan per varian)
        $query->groupBy('kode_material', 'nama_material', 'kategori', 'satuan');
    
        // Sorting / Urutkan
        $query->when($request->filled('sort_by'), function ($q) use ($request) {
            switch ($request->sort_by) {
                case 'stok_terbanyak':
                    $q->orderBy('total_stok', 'desc');
                    break;
                case 'stok_sedikit':
                    $q->orderBy('total_stok', 'asc');
                    break;
                case 'harga_termahal':
                    $q->orderBy('harga_satuan', 'desc');
                    break;
                case 'harga_termurah':
                    $q->orderBy('harga_satuan', 'asc');
                    break;
                case 'terbaru':
                default:
                    $q->orderByDesc('id');
                    break;
            }
        }, function ($q) {
            // Default sorting: terbaru
            $q->orderByDesc('id');
        });
    
        $bahans = $query->paginate(15)->withQueryString();
        $satuans = Satuan::all();
        $kategories = Kategori::where('tipe', 'bahan')->get();
        $warnas = Warna::all();
    
        // --- JALUR CEPAT HITUNG ASET (hitung dari semua varian) ---
        $totalAsetBahan = (float) BahanBaku::all()->sum(function($bahan) {
            return ($bahan->stok ?? 0) * ($bahan->harga_satuan ?? 0);
        });
    
        // Update tabel keuangan biar Dashboard ikutan bener
        \Illuminate\Support\Facades\DB::table('keuangan')->updateOrInsert(
            ['kategori' => 'aset_bahan'],
            ['total' => $totalAsetBahan, 'updated_at' => now()]
        );

        // Ringkasan stok kritis/habis (berdasarkan master grouping)
        $batasStokGlobal = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
        $totalKritis = BahanBaku::select(DB::raw('SUM(stok) as total_stok'))
            ->groupBy('kode_material')
            ->having('total_stok', '>', 0)
            ->having('total_stok', '<=', $batasStokGlobal)
            ->get()
            ->count();
    
        return view('admin.bahan.index', compact('bahans', 'satuans', 'kategories', 'warnas', 'totalAsetBahan', 'totalKritis', 'batasStokGlobal'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $bahanBaku = \App\Models\BahanBaku::where('nama_material', 'LIKE', "{$query}%")
            ->orWhere('kode_material', 'LIKE', "{$query}%")
            ->with('supplier', 'kategori', 'satuan') // Eager load relasi
            ->take(10)
            ->get();

        return response()->json($bahanBaku);
    }

    /**
     * AJAX: pencarian bahan untuk kebutuhan autocomplete (Input Manual / Scan Nota).
     * Hasil di-GROUP BY nama_material agar tidak duplikat per varian warna.
     * Menampilkan total stok dari semua varian warna.
     */
    public function searchApi(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $items = BahanBaku::select([
            DB::raw('MAX(id) as id'),
            'kode_material',
            'nama_material',
            DB::raw('SUM(stok) as stok'),
            'satuan',
            DB::raw('MAX(harga_satuan) as harga_satuan'),
            'kategori',
            DB::raw('MAX(foto) as foto'),
            DB::raw('MAX(warna) as warna'),
        ])
            ->where(function ($query) use ($q) {
                $query->whereRaw('LOWER(nama_material) LIKE LOWER(?)', ["%{$q}%"])
                    ->orWhereRaw('LOWER(kode_material) LIKE LOWER(?)', ["%{$q}%"])
                    ->orWhereRaw('LOWER(kategori) LIKE LOWER(?)', ["%{$q}%"]);
            })
            ->groupBy('kode_material', 'nama_material', 'satuan', 'kategori')
            ->orderBy('nama_material')
            ->limit(20)
            ->get();

        return response()->json($items);
    }

    /**
     * AJAX: Live Search suggestions untuk halaman index (min 3 karakter).
     * Return max 10 results in JSON format.
     */
    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        if (strlen($keyword) < 3) {
            return response()->json([]);
        }

        $suggestions = BahanBaku::query()
            ->where(function ($query) use ($keyword) {
                $query->whereRaw('LOWER(nama_material) LIKE LOWER(?)', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(kode_material) LIKE LOWER(?)', ["%{$keyword}%"]);
            })
            ->orderBy('nama_material')
            ->limit(10)
            ->get(['id', 'kode_material', 'nama_material', 'stok', 'satuan', 'kategori']);

        return response()->json($suggestions);
    }

    // 2. SIMPAN DATA BARU (STORE) - DENGAN LOGIKA RESTOCK & NON-INVENTORY
    public function store(Request $request)
    {
        // Validasi - FIXED: Sesuaikan dengan field dari form (nama_material, tidak ada stok_minimum)
        $request->validate([
            'nama_material' => 'required|string|max:255',
            'kategori' => 'required|string',
            'satuan' => 'required|string',
            'stok' => 'required|numeric', // Ini adalah Jumlah Masuk
            'harga_satuan' => 'required|numeric',
            'is_inventory' => 'nullable|boolean',
            'foto' => 'nullable|image|max:20480',
            'warna' => 'nullable|string',
            'detail' => 'nullable|string',
        ]);

        // Default is_inventory = true (bahan inventory)
        $isInventory = $request->has('is_inventory') ? (bool) $request->is_inventory : true;

        // Default stok_minimum jika tidak diisi (IKUTI Pengaturan Gudang: stok_minimum_global)
        $stokMinimum = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);

        // Upload Foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('bahan_baku', 'public');
        }

        // CEK KETERSEDIAAN BAHAN - Apakah material sudah ada?
        $bahan = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$request->nama_material])->first();

        if ($bahan) {
            // ===== BAHAN SUDAH ADA (RESTOCK) =====
            // Update info lain jika berbeda
            $bahan->kategori = $request->kategori;
            $bahan->satuan = $request->satuan;
            $bahan->supplier = $request->supplier ?? $bahan->supplier;
            $bahan->is_inventory = $isInventory;

            // Hanya update stok & harga jika is_inventory = true
            if ($isInventory) {
                $bahan->stok += $request->stok;
                $bahan->harga_satuan = $request->harga_satuan;
// Cari function store(Request $request)
if ($bahan) {
    // JANGAN UPDATE STOK DI SINI
    // Berikan pesan error bahwa penambahan stok harus melalui Operator (Scan Nota)
    return redirect()->back()->with('error', 'Admin tidak diizinkan menambah stok. Silahkan gunakan akun Operator untuk Scan Nota.');
} else {
    // Admin buat bahan baru, paksa stok awal jadi 0
    $bahan = BahanBaku::create([
        'kode_material' => $this->generateKodeMaterial(),
        'nama_material' => $request->nama_material,
        // ... field lain ...
        'stok' => 0, 
        'harga_satuan' => 0,
    ]);
}
                // Untuk non-inventory, stok dan harga tidak berubah (dianggap 0)
                $bahan->harga_satuan = 0;
            }

            $bahan->save();

            // Tambahkan layer FIFO baru untuk riwayat pembelian ini (hanya jika inventory)
            if ($isInventory && Schema::hasTable('bahan_stok_layers')) {
                \App\Models\BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk' => $request->stok,
                    'qty_sisa' => $request->stok,
                    'harga_beli' => $request->harga_satuan,
                    'supplier' => $request->supplier ?? null,
                ]);
            }

            // CATAT KE LOG_STOK untuk laporan (hanya jika inventory)
            if ($isInventory) {
                DB::table('log_stok')->insert([
                    'bahan_baku_id' => $bahan->id,
                    'operator_id' => Auth::id(),
                    'jenis_transaksi' => 'RESTOCK',
                    'jumlah' => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $msg = $isInventory 
                ? 'Stok material "' . $bahan->nama_material . '" berhasil ditambahkan! (Restock)' 
                : 'Material non-stok "' . $bahan->nama_material . '" berhasil diupdate!';
            return redirect()->route('admin.bahan.index')->with('success', $msg);
        } else {
            // ===== BAHAN BARU (INSERT BARU) =====
            // Generate Kode Otomatis - sekarang baca prefix dari database settings
            $kode = $this->generateKodeMaterial();

            // Simpan ke tabel bahan_bakus
            $bahan = BahanBaku::create([
                'kode_material' => $kode,
                'nama_material' => $request->nama_material,
                'kategori' => $request->kategori,
                'satuan' => $request->satuan,
                'stok' => $isInventory ? $request->stok : 0,
                'stok_minimum' => $stokMinimum,
                'harga_satuan' => $isInventory ? $request->harga_satuan : 0,
                'supplier' => $request->supplier ?? null,
                'is_inventory' => $isInventory,
            ]);

            // SIMPAN KE LAYER FIFO (bahan_stok_layers) - Layer pertama (hanya jika inventory)
            if ($isInventory && Schema::hasTable('bahan_stok_layers')) {
                \App\Models\BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk' => $request->stok,
                    'qty_sisa' => $request->stok,
                    'harga_beli' => $request->harga_satuan,
                    'supplier' => $request->supplier ?? null,
                ]);
            }

            // CATAT KE LOG_STOK untuk laporan (hanya jika inventory)
            if ($isInventory) {
                DB::table('log_stok')->insert([
                    'bahan_baku_id' => $bahan->id,
                    'operator_id' => Auth::id(),
                    'jenis_transaksi' => 'INPUT_BAHAN_BARU',
                    'jumlah' => $request->stok,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $msg = $isInventory 
                ? 'Material baru "' . $bahan->nama_material . '" berhasil ditambahkan dengan kode ' . $kode . '!' 
                : 'Material non-stok "' . $bahan->nama_material . '" berhasil ditambahkan!';
            return redirect()->route('admin.bahan.index')->with('success', $msg);
        }
    }


    // 3. TAMPILKAN FORM EDIT
    public function edit($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $satuans = Satuan::all();
        $kategories = Kategori::where('tipe', 'bahan')->get();
        $warnas = Warna::all();

        // Pecah string kategori dan warna menjadi array
        $bahan->kategori = !is_null($bahan->kategori) ? explode(',', $bahan->kategori) : [];
        $bahan->warna = !is_null($bahan->warna) ? explode(',', $bahan->warna) : [];

        return view('admin.bahan.edit', compact('bahan', 'satuans', 'kategories', 'warnas'));
    }

    // 4. UPDATE DATA (UPDATE) - FIFO Protected
    // Hanya nama_material, kategori, satuan, supplier, ref_no, stok_minimum yang bisa diubah
    // Stok dan harga_satuan TIDAK boleh diubah via edit modal (menggunakan sistem FIFO)
    public function update(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);

        // Validasi untuk field yang boleh diubah
        $request->validate([
            'nama_material' => 'required|string|max:255',
            'kategori' => 'required|array', // Diubah menjadi array
            'satuan' => 'required|string',
            'foto' => 'nullable|image|max:20480',
            'warna' => 'nullable|array', // Diubah menjadi array
            'detail' => 'nullable|string',
        ]);

        // Upload Foto jika ada
        $fotoPath = $bahan->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($bahan->foto && Storage::disk('public')->exists($bahan->foto)) {
                Storage::disk('public')->delete($bahan->foto);
            }
            
            // Upload foto baru
            $fotoPath = $request->file('foto')->store('bahan_baku', 'public');
        }

        // Konversi array ke string yang dipisahkan koma
        $kategoriString = implode(',', $request->kategori);
        $warnaString = $request->has('warna') ? implode(',', $request->warna) : null;

        // Update hanya field yang diizinkan
        $bahan->update([
            'nama_material' => $request->nama_material,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'supplier' => $request->supplier ?? $bahan->supplier,
            'ref_no' => $request->ref_no ?? $bahan->ref_no,
            'foto' => $fotoPath,
            'warna_id' => $request->warna_id,
            'deskripsi_bahan' => $request->deskripsi_bahan,
        ]);

        return redirect()->route('admin.bahan.index')->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    // 4. HAPUS DATA (DESTROY)
    public function destroy($id)
    {
        try {
            // Disable foreign key checks to avoid constraint violations
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $bahan = BahanBaku::findOrFail($id);
            
            // Delete related stock layers first (child records)
            \App\Models\BahanStokLayer::where('bahan_baku_id', $id)->delete();
            
            // Delete the material
            $bahan->delete();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('admin.bahan.index')->with('success', 'Material berhasil dihapus');
        } catch (\Exception $e) {
            // Make sure to re-enable foreign key checks even if error occurs
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('admin.bahan.index')->with('error', 'Gagal menghapus material: ' . $e->getMessage());
        }
    }


    /* =====================================================
     * OPERATOR FLOW: INPUT BAHAN MASUK
     * (sesuai skenario: operator belanja bahan -> stok bertambah + log transaksi)
     * ===================================================== */

    /**
     * Halaman daftar stok bahan untuk operator.
     */
    public function indexOperator(Request $request)
    {
        $query = BahanBaku::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function($q) use ($search) {
                $q->where('nama_material', 'LIKE', "%{$search}%")
                  ->orWhere('kode_material', 'LIKE', "%{$search}%")
                  ->orWhere('kategori', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('satuan')) {
            $query->where('satuan', $request->satuan);
        }

        // SORTING
        if ($request->filled('sort')) {
            if ($request->sort == 'stok_terendah') {
                $query->orderBy('stok', 'ASC');
            } elseif ($request->sort == 'nama_asc') {
                $query->orderBy('nama_material', 'ASC');
            } else {
                $query->latest('id');
            }
        } else {
            $query->orderBy('nama_material');
        }

        $bahanBakus = $query->paginate(12)->withQueryString();

        // TOTAL SKU DAN KRITIS DIHITUNG DARI SEMUA DATA BAHAN, BUKAN HANYA YANG ADA DI PAGE INI
        $allBahanQuery = BahanBaku::where('is_inventory', 1);
        
        $totalSKU = $allBahanQuery->count();
        // Stok kritis bahan baku (IKUTI Pengaturan Gudang: stok_minimum_global)
        $batasStokGlobal = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
        $totalKritis = (clone $allBahanQuery)->where('stok', '<=', $batasStokGlobal)->where('stok', '>', 0)->count();

        $unreadCount = Auth::user()->unreadNotifications()->where(function($q) {
            $q->where('data->target', 'operator')->orWhere('data->target', 'all');
        })->count();
        
        $kategories = \App\Models\Kategori::all();
        $satuans = \App\Models\Satuan::all();

        return view('operator.bahan-baku.index', compact('bahanBakus', 'totalSKU', 'totalKritis', 'unreadCount', 'kategories', 'satuans'));
    }

    /**
     * Form edit stok (operator).
     */
    public function editOperator($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $kategories = \App\Models\Kategori::where('tipe', 'bahan')->get();
        $satuans = \App\Models\Satuan::all();
        $warnas = \App\Models\Warna::all();

        // Pecah string menjadi array untuk multi-select
        $bahan->kategori = !is_null($bahan->kategori) ? explode(',', $bahan->kategori) : [];
        $bahan->warna = !is_null($bahan->warna) ? explode(',', $bahan->warna) : [];

        return view('operator.bahan-baku.edit', compact('bahan', 'kategories', 'satuans', 'warnas'));
    }

    /**
     * Update stok (operator).
     * NOTE: harga_satuan dari view edit berupa string format ribuan (1.000.000)
     */
    public function updateOperator(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $stokLama = $bahan->stok;

        $data = $request->validate([
            'nama_material' => 'required|string|max:255',
            'stok' => 'required|numeric',
            'stok_minimum' => 'nullable|numeric',
            'kategori' => 'required|array', // Diubah menjadi array
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required',
            'supplier' => 'nullable|string|max:255',
            'ref_no' => 'nullable|string|max:255',
            'warna' => 'nullable|array', // Ditambahkan
        ]);

        $harga = (int) preg_replace('/\D+/', '', (string) $data['harga_satuan']);
        $stokBaru = $data['stok'];
        $selisih = $stokBaru - $stokLama;

        // Konversi array ke string
        $kategoriString = implode(',', $data['kategori']);
        $warnaString = isset($data['warna']) ? implode(',', $data['warna']) : null;

        $bahan->update([
            'nama_material' => $data['nama_material'],
            'stok' => $stokBaru,
            'stok_minimum' => $data['stok_minimum'] ?? ($bahan->stok_minimum ?? 10),
            'kategori' => $kategoriString,
            'satuan' => $data['satuan'],
            'harga_satuan' => $harga,
            'supplier' => $data['supplier'] ?? null,
            'ref_no' => $data['ref_no'] ?? null,
            'warna' => $warnaString, // Ditambahkan
        ]);

        // CATAT KE LOG_STOK jika ada perubahan stok
        if ($selisih != 0) {
            DB::table('log_stok')->insert([
                'bahan_baku_id' => $bahan->id,
                'operator_id' => Auth::id(),
                'jenis_transaksi' => $selisih > 0 ? 'TAMBAH_STOK' : 'KELUAR_PRODUKSI',
                'jumlah' => abs($selisih),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('operator.bahan.index')->with('success', 'Data stok berhasil diperbarui.');
    }

    /**
     * Form input bahan masuk (manual).
     */
    public function createOperator()
    {
        $satuans = \App\Models\Satuan::all();
        $kategories = \App\Models\Kategori::where('tipe', 'bahan')->get();
        return view('operator.input-bahan', compact('satuans', 'kategories'));
    }

    /**
     * Simpan bahan masuk.
     * - jika kode_material sudah ada -> stok ditambah
     * - jika belum -> buat item bahan baru
     * - catat transaksi ke transaksi_stok bila tabel tersedia
     */
/**
     * Simpan bahan masuk via SCAN NOTA / INPUT MANUAL (Operator)
     * Menggunakan konsep MASTER-VARIANT:
     * - Kombinasi nama_material + warna adalah UNIK (satu baris per varian)
     * - Semua varian berbagi kode_material yang SAMA
     * - Stok ditambahkan ke varian yang sesuai, bukan ke baris pertama
     */
    public function storeOperator(Request $request)
    {
        $validated = $request->validate([
            'kode_material' => 'nullable|string|max:255',
            'nama_material' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'ref_no' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
            'is_inventory' => 'nullable|boolean',
            'foto_nota' => 'nullable|image|max:2048',
            'foto_bukti' => 'nullable|image|max:2048',
            'foto' => 'nullable|image|max:20480',
        ]);

        $isInventory = $request->has('is_inventory') ? (bool) $request->is_inventory : true;
        $qty = (float) $validated['stok'];
        $warna = $validated['warna'] ?? 'Tidak Ada Warna';
        $detail = $validated['detail'] ?? null;

        return DB::transaction(function () use ($validated, $qty, $isInventory, $request, $warna, $detail) {
            
            // 1. PROSES UPLOAD FOTO (nota / bukti)
            $pathNota = null;
            if ($request->hasFile('foto_nota')) {
                $pathNota = $request->file('foto_nota')->store('notas', 'public');
            } elseif ($request->hasFile('foto_bukti')) {
                $pathNota = $request->file('foto_bukti')->store('notas', 'public');
            }
            
            // Upload foto spesifik varian (gambar bahan per warna)
            $fotoVarian = null;
            if ($request->hasFile('foto')) {
                $fotoVarian = $request->file('foto')->store('bahan_baku', 'public');
            }

            // ============================================================
            // LOGIKA MASTER-VARIANT: Cek by nama_material + warna
            // ============================================================
            $bahan = null;
            
            if ($isInventory) {
                // LANGKAH A: Cari kombinasi nama_material + warna yang EXACT SAMA
                $bahan = BahanBaku::where('nama_material', $validated['nama_material'])
                    ->where('warna', $warna)
                    ->lockForUpdate()
                    ->first();
            }

            if ($bahan) {
                // ===== KOMBINASI (nama_material + warna) SUDAH ADA → RESTOCK =====
                $bahan->kategori = $validated['kategori'];
                $bahan->satuan = $validated['satuan'];
                $bahan->supplier = $validated['supplier'] ?? $bahan->supplier;
                $bahan->ref_no = $validated['ref_no'] ?? $bahan->ref_no;
                $bahan->detail = $detail ?? $bahan->detail;
                $bahan->foto_nota_terakhir = $pathNota ?? $bahan->foto_nota_terakhir;
                if ($fotoVarian) $bahan->foto = $fotoVarian;

                if ($isInventory) {
                    $bahan->increment('stok', $qty);
                    $bahan->harga_satuan = $validated['harga_satuan'];
                }
                $bahan->save();
            } else {
                // ===== KOMBINASI BARU → BUAT VARIAN BARU =====
                
                // LANGKAH B: Cek apakah nama_material sudah ada dengan warna lain
                // Jika ya, gunakan kode_material yang sama. Jika belum, generate baru.
                $existingMaster = BahanBaku::where('nama_material', $validated['nama_material'])
                    ->lockForUpdate()
                    ->first();
                
                $kodeMaterial = $validated['kode_material'] ?? null;
                
                if ($existingMaster) {
                    $kodeMaterial = $existingMaster->kode_material;
                } else {
                    $kodeMaterial = $kodeMaterial ?: $this->generateKodeMaterial();
                }

                // LANGKAH C: Buat baris data baru untuk varian warna ini
                $bahan = BahanBaku::create([
                    'kode_material' => $kodeMaterial,
                    'nama_material' => $validated['nama_material'],
                    'kategori' => $validated['kategori'],
                    'satuan' => $validated['satuan'],
                    'stok' => $isInventory ? $qty : 0,
                    'stok_minimum' => 10,
                    'harga_satuan' => $isInventory ? $validated['harga_satuan'] : 0,
                    'supplier' => $validated['supplier'] ?? null,
                    'ref_no' => $validated['ref_no'] ?? null,
                    'warna' => $warna,
                    'detail' => $detail,
                    'foto' => $fotoVarian,
                    'is_inventory' => $isInventory,
                    'foto_nota_terakhir' => $pathNota,
                ]);
            }

            // 2. BUAT LAYER FIFO (hanya jika inventory)
            if ($isInventory && Schema::hasTable('bahan_stok_layers')) {
                \App\Models\BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk' => $qty,
                    'qty_sisa' => $qty,
                    'harga_beli' => (float) $validated['harga_satuan'],
                    'supplier' => $validated['supplier'] ?? $bahan->supplier,
                    'ref_no' => $validated['ref_no'] ?? $bahan->ref_no,
                    'foto_nota' => $pathNota,
                ]);
            }

            // 3. LOG STOK UNTUK LAPORAN
            $jenisTransaksi = $isInventory ? 'SCAN_NOTA_MASUK' : 'BAHAN_PELENGKAP';
            DB::table('log_stok')->insert([
                'bahan_baku_id' => $bahan->id,
                'operator_id' => Auth::id(),
                'jenis_transaksi' => $jenisTransaksi,
                'jumlah' => $qty,
                'keterangan' => $warna ? "Ditambah via Scan Nota (Warna: {$warna})" : 'Ditambah via Scan Nota (Verifikasi Diperlukan)',
                'created_at' => now(),
            ]);

            // 4. KIRIM NOTIFIKASI VERIFIKASI KHUSUS KE ADMIN
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SystemNotification(
                        '📸 Verifikasi Scan Nota Baru',
                        "Operator " . Auth::user()->name . " mengunggah nota belanja {$bahan->nama_material}" . ($warna ? " (Warna: {$warna})" : "") . ". Klik untuk verifikasi.",
                        'info',
                        route('admin.bahan.index'),
                        'admin'
                    ));
                }
            } catch (\Throwable $e) { 
                \Log::error('Gagal kirim notifikasi scan: ' . $e->getMessage()); 
            }

            // 5. UPDATE PENGADAAN JIKA TERKAIT
            $pendingPengadaan = \App\Models\Pengadaan::where('bahan_baku_id', $bahan->id)
                ->where('status', 'PENDING')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($pendingPengadaan as $pengadaan) {
                $remaining = $pengadaan->jumlah - $pengadaan->received;
                if ($qty >= $remaining) {
                    $pengadaan->update(['received' => $pengadaan->jumlah, 'status' => 'DONE']);
                    $qty -= $remaining;
                } else {
                    $pengadaan->increment('received', $qty);
                    $qty = 0;
                    break;
                }
            }

            return redirect()->route('operator.pengadaan')->with('success', 'Bahan & Nota berhasil masuk!');
        });
    }

    // PENTING: Function create() dan edit() BOLEH DIHAPUS 
    // karena kita sudah pakai Modal, tidak butuh halaman terpisah lagi.
    
    /**
     * AJAX: Get stock layers for a specific material (FIFO visualization)
     */
    public function getStockLayers($id)
    {
        $bahan = BahanBaku::findOrFail($id);
    
        // --- START PERBAIKAN SINKRONISASI ---
    
        // 1. Hitung total stok yang sebenarnya dari semua layer FIFO.
        // Ini adalah "source of truth" untuk stok.
        $stokSebenarnya = (float) \App\Models\BahanStokLayer::where('bahan_baku_id', $id)->sum('qty_sisa');
    
        // 2. Bandingkan dengan stok yang tercatat di tabel utama (bahan_bakus).
        if ((float) $bahan->stok !== $stokSebenarnya) {
            // 3. Jika tidak cocok, lakukan sinkronisasi: update tabel utama.
            $bahan->stok = $stokSebenarnya;
            $bahan->save();
        }
    
        // --- END PERBAIKAN SINKRONISASI ---
    
        // Ambil semua layer stok yang sisanya masih ada, urutkan dari yang paling lama (FIFO)
        $layers = \App\Models\BahanStokLayer::where('bahan_baku_id', $id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc')
            ->get();
    
        // Hitung total nilai aset dari layer yang ada
        $totalAssetValue = $layers->sum(function($layer) {
            return $layer->qty_sisa * $layer->harga_beli;
        });
    
        // Format data layer untuk dikirim sebagai JSON
        $formattedLayers = $layers->map(function($layer) {
            return [
                'tanggal_masuk' => $layer->tanggal_masuk->format('d M Y'),
                'no_nota' => $layer->ref_no ?? 'N/A', // FIX: Menggunakan ref_no yang lebih konsisten
                'stok_sisa' => (float) $layer->qty_sisa,
                'harga_satuan' => (float) $layer->harga_beli,
            ];
        });
    
        return response()->json([
            'bahan' => $bahan,
            'layers' => $formattedLayers,
            'total_asset_value' => $totalAssetValue,
            'sync_error' => false,
        ]);
    }

    /**
     * API: Cek bahan (Auto-fill deskripsi & preview foto BAHAN/INDUK)
     * - Deskripsi: dari induk (nama_material, warna apapun)
     * - Foto: dari INDUK (nama_material saja) — cari varian manapun yang punya foto
     * - Stok & harga: dari kombinasi nama_material + warna spesifik
     */
    public function cekBahanApi(Request $request)
    {
        $nama = $request->query('nama_material');
        $warna = $request->query('warna');

        if (empty($nama)) {
            return response()->json(null);
        }

        // 1. Cari detail/deskripsi & FOTO dari INDUK (nama_material saja, warna apapun)
        // Ambil varian PERTAMA yang punya foto (untuk preview foto bahan)
        $induk = BahanBaku::where('nama_material', $nama)
            ->whereNotNull('foto')
            ->where('foto', '!=', '')
            ->first();

        // Jika tidak ada yang punya foto, ambil varian pertama untuk detail
        if (!$induk) {
            $induk = BahanBaku::where('nama_material', $nama)->first();
        }
        
        // 2. Cari data spesifik dari kombinasi nama + warna (untuk stok & harga)
        $varianSpesifik = null;
        if (!empty($warna)) {
            $varianSpesifik = BahanBaku::where('nama_material', $nama)
                ->where('warna', $warna)
                ->first();
        }

        $detail = null;
        $fotoUrl = null;

        if ($induk) {
            $detail = $induk->detail ?? $induk->deskripsi_bahan ?? null;
            if ($induk->foto) {
                $fotoUrl = asset('storage/' . $induk->foto);
            }
        }

        return response()->json([
            'detail' => $detail,
            'deskripsi_bahan' => $detail,
            'foto_url' => $fotoUrl,
            'stok' => $varianSpesifik ? $varianSpesifik->stok : 0,
            'harga_satuan' => $varianSpesifik ? $varianSpesifik->harga_satuan : 0,
        ]);
    }

    /**
     * Update varian warna (hanya warna & foto saja)
     */
    public function updateVarian(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);

        $request->validate([
            'warna' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:20480',
        ]);

        $data = [];

        if ($request->filled('warna')) {
            $data['warna'] = $request->warna;
        }

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($bahan->foto && Storage::disk('public')->exists($bahan->foto)) {
                Storage::disk('public')->delete($bahan->foto);
            }
            $data['foto'] = $request->file('foto')->store('bahan_baku', 'public');
        }

        if (!empty($data)) {
            $bahan->update($data);
        }

        return redirect()->route('admin.bahan.show', $id)->with('success', 'Varian berhasil diperbarui!');
    }

    /**
     * Generate kode material otomatis - baca prefix dari database settings
     */
    private function generateKodeMaterial(): string
    {
        // Ambil prefix dari database settings, fallback ke 'BHN-' jika tidak ada
        $prefixSetting = \App\Models\Pengaturan::where('nama_pengaturan', 'prefix_bahan')->first();
        $prefix = $prefixSetting ? $prefixSetting->nilai : 'BHN-';

        // Pakai lock agar aman dari race-condition
        $last = BahanBaku::lockForUpdate()
            ->where('kode_material', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $next = 1;
        if ($last && is_string($last->kode_material)) {
            // Match pattern based on the prefix (e.g., BHN-001, BHN-1000)
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $last->kode_material, $m)) {
                $next = ((int) $m[1]) + 1;
            }
        }

        // Pastikan unik
        do {
            $kode = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (BahanBaku::where('kode_material', $kode)->exists());

        return $kode;
    }

    /**
     * Adjust stock and log the changes.
     */
    public function adjustStock(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $stokLama = $bahan->stok;
        $stokBaru = $request->stok;
        $selisih = $stokBaru - $stokLama;

        if ($selisih == 0) {
            return back()->with('info', 'Tidak ada perubahan stok.');
        }

        $bahan->update(['stok' => $stokBaru]);

        // Log the stock adjustment
        DB::table('log_stok')->insert([
            'bahan_baku_id' => $bahan->id,
            'operator_id' => Auth::id(),
            'jenis_transaksi' => $selisih > 0 ? 'PENYESUAIAN_MASUK' : 'PENYESUAIAN_KELUAR',
            'jumlah' => abs($selisih),
            'keterangan' => 'Penyesuaian stok manual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Stok berhasil disesuaikan.');
    }
    
    /**
     * Halaman Detail Bahan untuk Operator
     */
    public function showOperator($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        
        // Ambil riwayat stok terbaru
        $riwayatStok = DB::table('log_stok')
            ->where('bahan_baku_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('operator.bahan-baku.show', compact('bahan', 'riwayatStok'));
    }
}