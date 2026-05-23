<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Bom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BomController extends Controller
{
    public function index(Request $request)
    {
        // Start query on ProdukJadi model
        $query = ProdukJadi::query();

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        // Filter by recipe status
        // UBAH: Ambil nilai berdasarkan nama dropdown di view yaitu 'status'
        $status = trim($request->input('status', '')); 
        
        if ($status === 'sudah_ada') {
            $query->whereHas('boms');
        } elseif ($status === 'belum_ada') {
            $query->whereDoesntHave('boms');
        }

        // Eager load relationships AFTER filtering
        $products = $query->with(['boms.bahanBaku'])
                          ->latest()
                          ->paginate(15)
                          ->withQueryString();

        return view('admin.bom.index', compact('products'));
    }

    public function indexOperator(Request $request)
    {
        // Start query on ProdukJadi model - hanya tampilkan yang punya BOM/resep
        $query = ProdukJadi::query();

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        // Hanya tampilkan produk yang sudah punya resep/BOM
        $query->whereHas('boms');

        // Eager load relationships
        $products = $query->with(['boms.bahanBaku'])
                          ->latest()
                          ->paginate(15)
                          ->withQueryString();

        return view('operator.resep-produk.index', compact('products'));
    }

    public function create($produk_jadi_id)
    {
        $product = ProdukJadi::findOrFail($produk_jadi_id);
    $materials = BahanBaku::select('id', 'nama_material', 'warna', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                    ->orderBy('nama_material', 'asc')->get();

    // Load master Warna untuk gambar & kode_warna
    $warnaMasterList = \App\Models\Warna::all()->keyBy('nama_warna');
    foreach ($materials as $mat) {
        if ($mat->warna && isset($warnaMasterList[$mat->warna])) {
            $w = $warnaMasterList[$mat->warna];
            $mat->gambar_warna = $w->gambar;
            $mat->kode_warna_warna = $w->kode_warna;
        } else {
            $mat->gambar_warna = null;
            $mat->kode_warna_warna = null;
        }
    }
        
        return view('admin.bom.create', compact('product', 'materials'));
    }

    public function store(Request $request, $produk_jadi_id)
{
    // 1. Validasi
    $request->validate([
        'material_id'   => 'required|array',
        'material_id.*' => 'required',
        'quantity.*'    => 'required|numeric',
        'unit.*'        => 'required|string',
    ]);

    $product = ProdukJadi::findOrFail($produk_jadi_id);

    // 2. Gunakan DB Query Builder biar lebih pasti masuknya
    DB::beginTransaction();
    try {
        // Hapus resep lama
        DB::table('bom')->where('id_produk', $product->id)->delete();

        $bomsData = [];
        foreach ($request->material_id as $index => $materialId) {
            if (!empty($materialId) && $request->quantity[$index] > 0) {
                $bomsData[] = [
                    'id_produk'        => $product->id,
                    'id_bahan'         => $materialId,
                    'jumlah_kebutuhan' => $request->quantity[$index],
                    'satuan'           => $request->unit[$index],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        if (count($bomsData) > 0) {
            // Kita pakai DB table langsung buat mastiin gak ada masalah di Model
            DB::table('bom')->insert($bomsData);
        }

        DB::commit();
        // Redirect ke halaman edit BOM setelah BOM berhasil disimpan
        return redirect()->route('admin.bom.edit', $product->id)->with('success', '✅ Resep BOM berhasil disimpan! Anda sekarang dapat mengedit detailnya.');
    } catch (\Exception $e) {
        DB::rollback();
        // Kalau error, dia bakal kasih tau kolom mana yang salah
        return "Gagal simpan! Errornya: " . $e->getMessage();
    }
}

public function edit($id)
{
    \Illuminate\Support\Facades\Log::info('BOM Edit Page Request', ['product_id' => $id]);
    $product = ProdukJadi::with(['boms.bahanBaku'])->findOrFail($id);
    
    $existingBoms = [];
    foreach ($product->boms as $index => $bom) {
        if ($bom->bahanBaku) {
            $existingBoms[] = [
                'id'            => $index + 1,
                'material_id'   => $bom->id_bahan,
                'quantity'      => $bom->jumlah_kebutuhan,
                'unit'          => $bom->satuan,
                'name'          => $bom->bahanBaku->nama_material,
                'price'         => $bom->bahanBaku->harga_satuan,
            ];
        }
    }
    
    $materials = BahanBaku::select('id', 'nama_material', 'warna', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                    ->orderBy('nama_material', 'asc')->get();

    // Load master Warna untuk gambar & kode_warna
    $warnaMasterList = \App\Models\Warna::all()->keyBy('nama_warna');
    foreach ($materials as $mat) {
        if ($mat->warna && isset($warnaMasterList[$mat->warna])) {
            $w = $warnaMasterList[$mat->warna];
            $mat->gambar_warna = $w->gambar;
            $mat->kode_warna_warna = $w->kode_warna;
        } else {
            $mat->gambar_warna = null;
            $mat->kode_warna_warna = null;
        }
    }
    
    return view('admin.bom.edit', compact('product', 'existingBoms', 'materials'));
}

public function update(Request $request, $id)
{
    // 1. Validasi: Semua wajib diisi KECUALI detail_pola
    $request->validate([
        'panjang'       => 'required|numeric|min:0.01',
        'lebar'         => 'required|numeric|min:0.01',
        'tinggi'        => 'required|numeric|min:0.01',
        'detail_pola'   => 'nullable|string', // Hanya ini yang boleh kosong
        'material_id'   => 'nullable|array',
        'material_id.*' => 'nullable|exists:bahan_bakus,id',
        'quantity'      => 'nullable|array',
        'quantity.*'    => 'nullable|numeric|min:0.01',
        'unit'          => 'nullable|array',
        'unit.*'        => 'nullable|string',
        'sketsa.*'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
    ], [
        // Pesan Error Bahasa Indonesia agar mudah saat Demo Skripsi
        'panjang.required' => 'Dimensi Panjang wajib diisi.',
        'lebar.required'   => 'Dimensi Lebar wajib diisi.',
        'tinggi.required'  => 'Dimensi Tinggi wajib diisi.',
        'quantity.*.min'   => 'Jumlah bahan tidak boleh 0.',
        'material_id.*.exists' => 'Bahan yang dipilih tidak valid atau sudah dihapus.',
    ]);

    DB::beginTransaction();
    try {
        $product = ProdukJadi::findOrFail($id);
        
        // --- PROSES SKETSA (FOTO) ---
        $decodedSketsas = json_decode($product->sketsa, true);
        $existingSketsas = [];
        if (is_array($decodedSketsas)) {
            $existingSketsas = $decodedSketsas;
        } elseif (is_string($decodedSketsas)) {
            $existingSketsas = [$decodedSketsas];
        }

        if ($request->has('delete_sketsa')) {
            foreach ($request->delete_sketsa as $path) {
                Storage::disk('public')->delete($path);
                $existingSketsas = array_diff($existingSketsas, [$path]);
            }
        }
        $newSketsas = [];
        if ($request->hasFile('sketsa')) {
            foreach ($request->file('sketsa') as $file) {
                $newSketsas[] = $file->store('produk/sketsa', 'public');
            }
        }
        $allSketsas = array_merge(array_values($existingSketsas), $newSketsas);

        // --- 1. UPDATE DATA PRODUK ---
        $product->update([
            'panjang'     => $request->panjang,
            'lebar'       => $request->lebar,
            'tinggi'      => $request->tinggi,
            'detail_pola' => $request->detail_pola,
            'sketsa'      => !empty($allSketsas) ? json_encode(array_values($allSketsas)) : null,
        ]);

        // --- 2. UPDATE RESEP BOM ---
        // Hapus resep lama (Relasi id_produk sesuai tabel bom kamu)
        DB::table('bom')->where('id_produk', $product->id)->delete();

        $bomsData = [];
        if ($request->has('material_id') && is_array($request->material_id)) {
            foreach ($request->material_id as $index => $materialId) {
                if (!empty($materialId) && isset($request->quantity[$index]) && $request->quantity[$index] > 0) {
                    $bomsData[] = [
                        'id_produk'        => $product->id,
                        'id_bahan'         => $materialId,
                        'jumlah_kebutuhan' => $request->quantity[$index],
                        'satuan'           => $request->unit[$index] ?? 'Satuan',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }
        }

        if (!empty($bomsData)) {
            DB::table('bom')->insert($bomsData);
        }

        DB::commit();
        return redirect()->route('admin.bom.index')->with('success', '✅ BOM ' . $product->nama_produk . ' Berhasil Disimpan!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('error', "Gagal simpan: " . $e->getMessage());
    }
}
}