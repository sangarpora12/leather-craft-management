<?php

namespace App\Http\Controllers;

use App\Models\ProdukJadi;
use App\Models\ProdukKeluar;
use App\Models\Produksi;
use App\Models\Bom;
use App\Models\Pengaturan;
use App\Models\Kategori;
use App\Models\Warna;
use App\Models\Motif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProdukJadiController extends Controller
{
    /**
     * INDEX – DAFTAR PRODUK (Pencarian & Filter)
     */
    public function index(Request $request)
    {
        // 1. Ambil data master untuk dropdown filter
        $warnaList = ProdukJadi::query()
            ->select('warna')
            ->whereNotNull('warna')
            ->where('warna', '!=', '')
            ->distinct()
            ->orderBy('warna')
            ->pluck('warna');

        // Ambil daftar kategori produk dari tabel kategori (sama seperti bahan baku)
        $kategories = Kategori::where('tipe', 'produk')
            ->orderBy('nama_kategori')
            ->get();
            
        // Ambil juga kategori yang sudah ada di produk untuk kompatibilitas
        $existingCategories = ProdukJadi::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '[]')
            ->get()
            ->flatMap(function ($product) {
                return is_array($product->kategori) ? $product->kategori : [];
            })
            ->unique()
            ->sort()
            ->values();

        // 2. Mulai query builder
        $query = ProdukJadi::query();

        // ATURAN BARU: Hanya tampilkan produk yang relevan (punya stok / pernah diproduksi)
        $query->whereHas('produksi', function ($q) {
            $q->where('status', 'SELESAI');
        });

        // 3. Terapkan semua filter SECARA BERURUTAN

        // Filter berdasarkan status produksi (jika ada)
        $query->when($request->filled('status_produksi'), function ($q) use ($request) {
            $status = $request->string('status_produksi');
            // Filter melalui relasi produksi
            $q->whereHas('produksi', function ($subQuery) use ($status) {
                $subQuery->where('status', $status);
            });
        });

        // Filter Pencarian (nama, kode, deskripsi)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->string('search')->trim();
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        });

        // Filter Warna
        $query->when($request->filled('warna'), function ($q) use ($request) {
            $q->where('warna', $request->warna);
        });

        // Filter Kategori
        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->whereJsonContains('kategori', $request->kategori);
        });

        // Filter Status Stok
        $stokMinimum = Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 2;
        $query->when($request->filled('status_stok'), function ($q) use ($request, $stokMinimum) {
            if ($request->status_stok == 'tersedia') {
                $q->where('stok', '>', 0);
            } elseif ($request->status_stok == 'menipis') {
                $q->whereBetween('stok', [1, $stokMinimum]);
            } elseif ($request->status_stok == 'habis') {
                $q->where('stok', '=', 0);
            }
        });

        // 4. Terapkan Urutan (Sorting)
        $query->when($request->filled('sort_by'), function ($q) use ($request) {
            switch ($request->sort_by) {
                case 'harga_termahal':
                    $q->orderBy('harga_jual', 'desc');
                    break;
                case 'harga_termurah':
                    $q->orderBy('harga_jual', 'asc');
                    break;
                case 'stok_terbanyak':
                    $q->orderBy('stok', 'desc');
                    break;
                case 'stok_terkecil':
                    $q->orderBy('stok', 'asc');
                    break;
                default:
                    $q->orderByDesc('id'); // Default: terbaru
                    break;
            }
        }, function ($q) {
            // Default sorting jika tidak ada parameter: terbaru
            $q->orderByDesc('id');
        });

        // 5. EKSEKUSI QUERY (PAGINATE) SETELAH SEMUA FILTER DITERAPKAN
        $produk_jadi = $query->paginate(12)->withQueryString();

        // 6. Kirim data ke view
        $allCategories = $kategories->pluck('nama_kategori')->merge($existingCategories)->unique()->sortBy(function ($item) {
            return $item;
        })->values();
        return view('admin.produk-jadi.index', compact('produk_jadi', 'warnaList', 'allCategories'));
    }

    /**
     * AJAX: Live Search suggestions untuk produk (min 3 karakter).
     * Return max 10 results in JSON format.
     */
    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        if (strlen($keyword) < 3) {
            return response()->json([]);
        }

        // Hanya tampilkan produk yang sudah selesai produksi
        $produkSelesaiIds = Produksi::query()
            ->where('status', 'SELESAI')
            ->whereNotNull('produk_jadi_id')
            ->pluck('produk_jadi_id')
            ->unique();

        $suggestions = ProdukJadi::query()
            ->whereIn('id', $produkSelesaiIds)
            ->has('boms')
            ->where(function ($query) use ($keyword) {
                $query->whereRaw('LOWER(nama_produk) LIKE LOWER(?)', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(kode_produk) LIKE LOWER(?)', ["%{$keyword}%"]);
            })
            ->orderBy('nama_produk')
            ->limit(10)
            ->get(['id', 'kode_produk', 'nama_produk', 'stok', 'harga_jual', 'kategori']);

        return response()->json($suggestions);
    }

    /**
     * CREATE – FORM BUAT PRODUK BARU
     */
    public function create()
    {
        // 1. Ambil prefix dari pengaturan gudang
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PROD';
    
        // 2. Cari produk terakhir yang kodenya mirip dengan prefix tersebut
        $lastProduct = ProdukJadi::where('kode_produk', 'like', $prefix . '-%')
            ->orderByRaw("CAST(REGEXP_REPLACE(kode_produk, '[^0-9]+', '') AS UNSIGNED) DESC")
            ->first();
    
        if ($lastProduct) {
            // Ambil angka saja dari kode produk tersebut
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastProduct->kode_produk);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
    
        // 3. Format kode baru sesuai prefix (Contoh: PROD-0001)
        $newCode = $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    
        $kategories = Kategori::where('tipe', 'produk')->get(['id', 'nama_kategori']);
        $warnas = Warna::all(['id', 'nama_warna']);
        $motifs = Motif::all(['id', 'nama_motif']);
        
        return view('admin.produk-jadi.create', compact('newCode', 'kategories', 'warnas', 'motifs'));
    }

    /**
     * STORE – SIMPAN PRODUK BARU
     */
    public function store(Request $request)
    {
        // 1. Validasi data dasar (kode_produk tidak perlu divalidasi 'required' dari input lagi)
        $validated = $request->validate([
            'nama_produk'  => ['required', 'string', 'max:255'],
            'kategori'     => ['required', 'array', 'min:1'],
            'warna'        => ['required', 'array', 'min:1'],
            'jenis_motif'  => ['required', 'array', 'min:1'],
            'tipe_harga'   => ['nullable', 'string', 'in:manual,otomatis'],
            'harga_jual'   => ['nullable', 'string', 'required_if:tipe_harga,manual'],
            'deskripsi'    => ['nullable', 'string'],
            'keterangan'   => ['nullable', 'string'],
            'foto'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        // 2. Generate KODE OTOMATIS di sini
        $kodeOtomatis = $this->generateKodeProduk();

        // 3. Parsing Harga Jual
        $hargaJual = 0;
        $hargaInput = $request->input('harga_jual');
        if ($request->tipe_harga == 'manual' && !empty($hargaInput)) {
            $hargaJual = (int) preg_replace('/[^0-9]/', '', $hargaInput);
        }

        return DB::transaction(function () use ($validated, $request, $hargaJual, $kodeOtomatis) {
            $pathFoto = null;
            if ($request->hasFile('foto')) {
                $pathFoto = $request->file('foto')->store('produk-jadi', 'public');
            }

            // 4. Simpan ke database menggunakan $kodeOtomatis
            $product = ProdukJadi::create([
                'kode_produk'  => $kodeOtomatis,
                'nama_produk'  => $validated['nama_produk'],
                'kategori'     => $validated['kategori'],
                'warna'        => $validated['warna'],
                'jenis_motif'  => $validated['jenis_motif'],
                'harga_jual'   => $hargaJual,
                'tipe_harga'   => $validated['tipe_harga'] ?? 'otomatis',
                // Gunakan ?? null agar tidak error jika inputnya kosong/tidak ada
                'deskripsi'    => $validated['deskripsi'] ?? null, 
                'keterangan'   => $validated['keterangan'] ?? null,
                'stok'         => 0,
                'foto'         => $pathFoto,
            ]);

            // 5. Redirect ke halaman buat BOM (Resep)
            return redirect()->route('admin.bom.edit', $product->id)
    ->with('success', 'Produk berhasil dibuat! Sekarang silakan isi resep bahannya.');
        });
    
    }

    /**
     * SHOW – DETAIL PRODUK
     */
    public function show($id)
    {
        $product = ProdukJadi::with(['boms.bahanBaku', 'produkKeluars'])->findOrFail($id);
        $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50);
        return view('admin.produk-jadi.show', compact('product', 'margin'));
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $product = ProdukJadi::findOrFail($id);
        
        // Decode JSON array fields to PHP array
        $product->kategori = is_string($product->kategori) ? json_decode($product->kategori, true) : $product->kategori;
        $product->warna = is_string($product->warna) ? json_decode($product->warna, true) : $product->warna;
        $product->jenis_motif = is_string($product->jenis_motif) ? json_decode($product->jenis_motif, true) : $product->jenis_motif;
        
        // Ensure defaults as array
        if (!is_array($product->kategori)) $product->kategori = [];
        if (!is_array($product->warna)) $product->warna = [];
        if (!is_array($product->jenis_motif)) $product->jenis_motif = [];
        
        $kategories = Kategori::where('tipe', 'produk')->get(['id', 'nama_kategori']);
        $warnas = Warna::all(['id', 'nama_warna']);
        $motifs = Motif::all(['id', 'nama_motif']);
        
        // ✅ AMBIL MARGIN GLOBAL DARI PENGATURAN
        $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50);
        
        return view('admin.produk-jadi.edit', compact('product', 'kategories', 'warnas', 'motifs', 'margin'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $product = ProdukJadi::findOrFail($id);

        $validated = $request->validate([
            'nama_produk'  => ['required', 'string', 'max:255'],
            'kategori'     => ['required', 'array', 'min:1'],
            'kategori.*'   => ['string', 'max:100'],
            'warna'        => ['required', 'array', 'min:1'],
            'warna.*'      => ['string', 'max:100'],
            'jenis_motif'  => ['required', 'array', 'min:1'],
            'jenis_motif.*'=> ['string', 'max:100'],
            'harga_jual'   => ['required', 'string', 'min:1'],
            'deskripsi'    => ['nullable', 'string'],
            'keterangan'   => ['nullable', 'string'],
            'foto'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'delete_foto'  => ['nullable', 'string', 'in:1'],
            'panjang'      => ['nullable', 'numeric', 'min:0'],
            'lebar'        => ['nullable', 'numeric', 'min:0'],
            'tinggi'       => ['nullable', 'numeric', 'min:0'],
            'detail_pola'  => ['nullable', 'string'],
            'tipe_harga'   => ['nullable', 'string', 'in:manual,otomatis'],
            'sketsa'       => ['nullable', 'array', 'max:4'],
            'sketsa.*'     => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
        ]);

        // ✅ ✅ LOGIKA BARU FIX DATABASE ERROR
        $hargaJual = 0;
        
        if ($request->filled('tipe_harga') && $request->tipe_harga === 'otomatis') {
            // ✅ HITUNG HARGA OTOMATIS LANGSUNG DISINI
            $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50);
            
            // Hitung total HPP dari BOM
            $totalHpp = 0;
            $product->load('boms.material');
            foreach ($product->boms as $bom) {
                if ($bom->material) {
                    $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                    $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                    $totalHpp += $hargaSatuan * $jumlahKebutuhan;
                }
            }
            
            $hargaJual = ceil($totalHpp + ($totalHpp * ($margin / 100)));
        } else {
            // ✅ JIKA MANUAL: PAKAI NILAI INPUT USER
            if (!empty($request->harga_jual)) {
                $hargaJual = (int) str_replace(['.', ',', 'Rp', ' '], '', $request->harga_jual);
            }
        }

        $pathFoto = $product->foto;
         
         // Handle upload foto baru (prioritas lebih tinggi dari penghapusan)
         if ($request->hasFile('foto')) {
             if ($product->foto) Storage::disk('public')->delete($product->foto);
             $pathFoto = $request->file('foto')->store('produk-jadi', 'public');
         } elseif ($request->has('delete_foto') && $request->delete_foto == '1') {
             // Handle penghapusan foto hanya jika tidak ada upload baru
             if ($product->foto) {
                 Storage::disk('public')->delete($product->foto);
                 $pathFoto = null;
             }
         }

        $pathsSketsa = $product->sketsa ?: [];
        if ($request->hasFile('sketsa')) {
            // Hapus file lama jika ada
            if (is_array($pathsSketsa)) {
                foreach ($pathsSketsa as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            // Upload file baru
            $pathsSketsa = [];
            foreach ($request->file('sketsa') as $file) {
                $pathsSketsa[] = $file->store('produk/sketsa', 'public');
            }
        }

        $product->update([
            'nama_produk'  => $validated['nama_produk'],
            'kategori'     => isset($validated['kategori']) ? $validated['kategori'] : $product->kategori,
            'warna'        => isset($validated['warna']) ? $validated['warna'] : $product->warna,
            'jenis_motif'  => isset($validated['jenis_motif']) ? $validated['jenis_motif'] : $product->jenis_motif,
            'harga_jual'   => $hargaJual,
            'deskripsi'    => $validated['deskripsi'] ?? $product->deskripsi,
            'keterangan'   => $validated['keterangan'] ?? $product->keterangan,
            'panjang'      => $validated['panjang'] ?? $product->panjang,
            'lebar'        => $validated['lebar'] ?? $product->lebar,
            'tinggi'       => $validated['tinggi'] ?? $product->tinggi,
            'detail_pola'  => $validated['detail_pola'] ?? $product->detail_pola,
            'sketsa'       => !empty($pathsSketsa) ? json_encode($pathsSketsa) : null,
            'foto'         => $pathFoto,
            'tipe_harga'   => $validated['tipe_harga'] ?? $product->tipe_harga ?? 'manual',
        ]);

        return redirect()->route('admin.produk-jadi.index')->with('success', 'Perubahan disimpan.');
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $product = ProdukJadi::findOrFail($id);
        if ($product->foto) Storage::disk('public')->delete($product->foto);
        if ($product->sketsa) Storage::disk('public')->delete($product->sketsa);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * COPY – DUPLIKAT PRODUK BESERTA BOM
     */
    public function copy(Request $request, $id)
    {
        $originalProduct = ProdukJadi::with('boms')->findOrFail($id);

        return DB::transaction(function () use ($originalProduct) {
            // 1. Duplikat data produk
            $newProduct = $originalProduct->replicate([
                'kode_produk', // Jangan duplikat kode produk
            ]);

            $newProduct->nama_produk = $originalProduct->nama_produk . ' (Salinan)';
            $newProduct->kode_produk = $this->generateKodeProduk();
            $newProduct->stok = 0; // Stok produk baru selalu 0
            $newProduct->created_at = now();
            $newProduct->updated_at = now();

            // Jika ada foto, duplikat juga filenya
            if ($originalProduct->foto && Storage::disk('public')->exists($originalProduct->foto)) {
                $newPath = 'produk-jadi/' . uniqid() . '.' . pathinfo($originalProduct->foto, PATHINFO_EXTENSION);
                Storage::disk('public')->copy($originalProduct->foto, $newPath);
                $newProduct->foto = $newPath;
            }
            
            $newProduct->save();

            // 2. Duplikat BOM jika ada
            if ($originalProduct->boms->isNotEmpty()) {
                $newBoms = [];
                foreach ($originalProduct->boms as $originalBom) {
                    $newBoms[] = [
                        'id_produk' => $newProduct->id,
                        'id_bahan' => $originalBom->id_bahan,
                        'jumlah_kebutuhan' => $originalBom->jumlah_kebutuhan,
                        'satuan' => $originalBom->satuan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                Bom::insert($newBoms);
            }

            return redirect()->route('admin.bom.index')->with('success', 'Produk berhasil disalin dengan nama "' . $newProduct->nama_produk . '".');
        });
    }

    /**
     * ADJUST STOCK
     */
    public function adjustStock(Request $request, $id)
    {
        $validated = $request->validate([
            'type'   => ['required', Rule::in(['add', 'reduce'])],
            'amount' => ['required', 'integer', 'min:1'],
            'alasan' => ['nullable', 'string', 'max:255'],
            
        ]);

        $product = ProdukJadi::findOrFail($id);

        if ($validated['type'] === 'reduce' && $product->stok < $validated['amount']) {
            return back()->with('error', 'Stok tidak cukup.');
        }

        $difference = $validated['type'] === 'add' ? $validated['amount'] : -$validated['amount'];

        $validated['type'] === 'add' 
            ? $product->increment('stok', $validated['amount']) 
            : $product->decrement('stok', $validated['amount']);

        // Create produk_keluar record for tracking when reducing stock
        if ($validated['type'] === 'reduce') {
            ProdukKeluar::create([
                'produk_jadi_id' => $product->id,
                'jumlah_keluar' => $validated['amount'],
                'tanggal_keluar' => now(),
                'user_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Stok diupdate.');
    }

    /**
     * AJAX: Get Total HPP Produk dari BOM
     */
    public function getHpp($id)
    {
        $product = ProdukJadi::with('boms.material')->findOrFail($id);
        
        $totalHpp = 0;
        
        foreach ($product->boms as $bom) {
            if ($bom->material) {
                // ✅ FIX: Paksa cast ke float agar perkalian desimal akurat
                $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                
                // Hitung HPP per bahan: harga satuan * jumlah kebutuhan
                $totalHpp += $hargaSatuan * $jumlahKebutuhan;
            }
        }
        
        // ✅ AMBIL MARGIN DARI PENGATURAN DATABASE (BUKAN HARDCODE!)
        $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50);
        $hargaJual = $totalHpp + ($totalHpp * ($margin / 100));
        
        return response()->json([
            'hpp' => $totalHpp,
            'margin' => $margin,
            'harga_jual' => ceil($hargaJual) // Pembulatan ke atas
        ]);
    }

    /**
     * Generate Kode Otomatis
     */
    private function generateKodeProduk(): string
{
    $prefix = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PROD';

    $lastProduct = ProdukJadi::where('kode_produk', 'LIKE', $prefix . '-%')
        ->orderByRaw("CAST(REGEXP_REPLACE(kode_produk, '[^0-9]+', '') AS UNSIGNED) DESC")
        ->first();

    $newNumber = 1;
    if ($lastProduct) {
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastProduct->kode_produk);
        $newNumber = $lastNumber + 1;
    }

    return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}
}