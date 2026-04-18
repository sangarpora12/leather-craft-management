<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Bom;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukJadi::with(['boms.bahanBaku']); // Tambahkan eager loading

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bom.index', compact('products'));
    }

    public function create($produk_jadi_id)
{
    // [FIX] Pastikan memanggil relasi 'boms.bahanBaku' (Sesuai nama model mas)
    $product = ProdukJadi::with(['boms.bahanBaku'])->findOrFail($produk_jadi_id);
    
    $existingBoms = [];
    foreach ($product->boms as $index => $bom) {
        // [FIX] Kita proteksi, hanya tampilkan jika relasi bahannya ada
        if ($bom->bahanBaku) {
            $existingBoms[] = [
                'id'            => $index + 1,
                'material_id'   => $bom->id_bahan,
                'quantity'      => $bom->jumlah_kebutuhan,
                'unit'          => $bom->satuan,
                'name'          => $bom->bahanBaku->nama_material, // Sesuaikan ini!
                'price'         => $bom->bahanBaku->harga_satuan,  // Sesuaikan ini!
            ];
        }
    }
    
    // Ambil daftar semua bahan baku untuk pilihan di form
    // [MODIFIED] Mengambil semua bahan baku tanpa filter inventaris
    $materials = BahanBaku::select('id', 'nama_material', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                    ->orderBy('nama_material', 'asc')->get();
    
    return view('admin.bom.create', compact('product', 'existingBoms', 'materials'));
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
        // Redirect ke halaman Perintah Produksi setelah BOM berhasil disimpan
        return redirect()->route('admin.produksi.create')->with('success', '✅ Resep BOM berhasil disimpan! Silahkan buat perintah produksi untuk ' . $product->nama_produk);
    } catch (\Exception $e) {
        DB::rollback();
        // Kalau error, dia bakal kasih tau kolom mana yang salah
        return "Gagal simpan! Errornya: " . $e->getMessage();
    }
}

public function edit($id)
{
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
    
    $materials = BahanBaku::select('id', 'nama_material', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                    ->orderBy('nama_material', 'asc')->get();
    
    return view('admin.bom.edit', compact('product', 'existingBoms', 'materials'));
}

public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'material_id'   => 'required|array',
        'material_id.*' => 'required',
        'quantity.*'    => 'required|numeric',
        'unit.*'        => 'required|string',
    ]);

    $product = ProdukJadi::findOrFail($id);

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
            DB::table('bom')->insert($bomsData);
        }

        DB::commit();
        return redirect()->route('admin.bom.index')->with('success', 'BOM untuk produk ' . $product->nama_produk . ' berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('error', "Gagal memperbarui BOM: " . $e->getMessage());
    }
}
}