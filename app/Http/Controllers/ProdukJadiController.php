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
        $query = ProdukJadi::query();
        
        // Filter hanya produk yang sudah memiliki stok (sudah selesai diproduksi)
        $query->where('stok', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_produk', 'LIKE', "%{$search}%")
                ->orWhere('kode_produk', 'LIKE', "%{$search}%");
        }

        $produkJadis = $query->orderBy('nama_produk')->paginate(15)->withQueryString();
        $satuans = \App\Models\Satuan::all();
        $kategories = \App\Models\Kategori::where('tipe', 'produk')->get();
        $warnas = \App\Models\Warna::all();
        $motifs = \App\Models\Motif::all();

        // Ambil ID produk yang sudah pernah selesai diproduksi
        $produkSelesaiIds = Produksi::where('status', 'SELESAI')
            ->whereNotNull('produk_jadi_id')
            ->pluck('produk_jadi_id')
            ->unique();

        // Ambil daftar kategori unik untuk ditampilkan di UI (otomatis dari database)
        $kategoriList = ProdukJadi::query()
            ->select('kategori')
            ->whereIn('id', $produkSelesaiIds)
            ->has('boms')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // Ambil daftar warna unik untuk filter (otomatis dari database)
        $warnaList = ProdukJadi::query()
            ->select('warna')
            ->whereIn('id', $produkSelesaiIds)
            ->has('boms')
            ->whereNotNull('warna')
            ->where('warna', '!=', '')
            ->distinct()
            ->orderBy('warna')
            ->pluck('warna');

        // 2. Logika Filter Kategori
        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->where('kategori', $request->kategori);
        });

        // Filter warna
        $query->when($request->filled('warna'), function ($q) use ($request) {
            $q->where('warna', $request->warna);
        });

        // 3. Logika Pencarian (Berdasarkan nama atau kode)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->string('search')->toString();
            $q->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        });

        // 4. Filter Status Stok
        $query->when($request->filled('status_stok'), function ($q) use ($request) {
            if ($request->status_stok == 'tersedia') {
                $q->where('stok', '>', 5);
            } elseif ($request->status_stok == 'menipis') {
                $q->whereBetween('stok', [1, 5]);
            } elseif ($request->status_stok == 'habis') {
                $q->where('stok', 0);
            }
            // 'semua' = tidak filter
        });

        // 5. Sorting / Urutkan
        $query->when($request->filled('sort_by'), function ($q) use ($request) {
            switch ($request->sort_by) {
                case 'nama_az':
                    $q->orderBy('nama_produk', 'asc');
                    break;
                case 'harga_termahal':
                    $q->orderBy('harga_jual', 'desc');
                    break;
                case 'harga_termurah':
                    $q->orderBy('harga_jual', 'asc');
                    break;
                case 'stok_terbanyak':
                    $q->orderBy('stok', 'desc');
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

        // 6. Eksekusi Query dengan Pagination
        $produk_jadi = $query->paginate(12)->withQueryString();

        return view('admin.produk-jadi.index', compact('produk_jadi', 'kategoriList', 'warnaList'));
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
$kategories = Kategori::where('tipe', 'produk')->get(['id', 'nama_kategori']);
$warnas = Warna::all(['id', 'nama_warna']);
$motifs = Motif::all(['id', 'nama_motif']);
        return view('admin.produk-jadi.create', compact('kategories', 'warnas', 'motifs'));
    }

    /**
     * STORE – SIMPAN PRODUK BARU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'  => ['required', 'string', 'max:255'],
            'kategori'     => ['nullable', 'array'],
            'kategori.*'   => ['string', 'max:100'],
            'warna'        => ['nullable', 'array'],
            'warna.*'      => ['string', 'max:100'],
            'jenis_motif'  => ['nullable', 'array'],
            'jenis_motif.*'=> ['string', 'max:100'],
            'harga_jual'   => ['required', 'string', 'min:1'],
            'deskripsi'    => ['nullable', 'string'],
            'keterangan'   => ['nullable', 'string'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
            'foto'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'panjang'      => ['nullable', 'numeric', 'min:0'],
            'lebar'        => ['nullable', 'numeric', 'min:0'],
            'tinggi'       => ['nullable', 'numeric', 'min:0'],
            'detail_pola'  => ['nullable', 'string'],
            'sketsa'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // ✅ FIX FINAL - PARSING HARGA JUAL
        $hargaJual = 0;
        $hargaInput = $request->input('harga_jual');
        
        if (!empty($hargaInput) && $hargaInput !== '0' && $hargaInput !== 'Rp 0') {
            // Bersihkan SEMUA karakter non numerik
            $hargaJual = preg_replace('/[^0-9]/', '', $hargaInput);
            $hargaJual = (int) $hargaJual;
        }
        
        // Validasi harga tidak boleh 0
        if ($hargaJual < 1) {
            throw ValidationException::withMessages([
                'harga_jual' => 'Harga jual wajib diisi dan tidak boleh Rp 0!'
            ]);
        }

        return DB::transaction(function () use ($validated, $request, $hargaJual) {
            $kode = $this->generateKodeProduk();

            $pathFoto = null;
            if ($request->hasFile('foto')) {
                $pathFoto = $request->file('foto')->store('produk-jadi', 'public');
            }

            $pathSketsa = null;
            if ($request->hasFile('sketsa')) {
                $pathSketsa = $request->file('sketsa')->store('produk/sketsa', 'public');
            }

            $product = ProdukJadi::create([
                'kode_produk'  => $kode,
                'nama_produk'  => $validated['nama_produk'],
                'kategori'     => isset($validated['kategori']) ? json_encode($validated['kategori']) : null,
                'warna'        => isset($validated['warna']) ? json_encode($validated['warna']) : null,
                'jenis_motif'  => isset($validated['jenis_motif']) ? json_encode($validated['jenis_motif']) : null,
                'harga_jual'   => $hargaJual,
                'deskripsi'    => $validated['deskripsi'],
                'keterangan'   => $validated['keterangan'],
                'stok'         => 0,
                'stok_minimum' => $validated['stok_minimum'] ?? 10,
                'foto'         => $pathFoto,
                'panjang'      => $validated['panjang'] ?? null,
                'lebar'        => $validated['lebar'] ?? null,
                'tinggi'       => $validated['tinggi'] ?? null,
                'detail_pola'  => $validated['detail_pola'] ?? null,
                'sketsa'       => $pathSketsa,
            ]);

            return redirect()->route('admin.bom.create', $product->id)
                ->with('success', 'Produk ' . $kode . ' berhasil dibuat. Sekarang, silakan isi resep BOM-nya.');
        });
    }

    /**
     * SHOW – DETAIL PRODUK
     */
    public function show($id)
    {
        $product = ProdukJadi::with(['boms.material', 'produkKeluars'])->findOrFail($id);
        return view('admin.produk-jadi.show', compact('product'));
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
        
        return view('admin.produk-jadi.edit', compact('product', 'kategories', 'warnas', 'motifs'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $product = ProdukJadi::findOrFail($id);

        $validated = $request->validate([
            'nama_produk'  => ['required', 'string', 'max:255'],
            'kategori'     => ['nullable', 'array'],
            'kategori.*'   => ['string', 'max:100'],
            'warna'        => ['nullable', 'array'],
            'warna.*'      => ['string', 'max:100'],
            'jenis_motif'  => ['nullable', 'array'],
            'jenis_motif.*'=> ['string', 'max:100'],
            'harga_jual'   => ['required', 'string', 'min:1'],
            'deskripsi'    => ['nullable', 'string'],
            'keterangan'   => ['nullable', 'string'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
            'foto'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'panjang'      => ['nullable', 'numeric', 'min:0'],
            'lebar'        => ['nullable', 'numeric', 'min:0'],
            'tinggi'       => ['nullable', 'numeric', 'min:0'],
            'detail_pola'  => ['nullable', 'string'],
            'sketsa'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // ✅ FIX: Jika mode harga otomatis, hitung ulang HPP dari BOM
        $hargaJual = 0;
        
        if ($request->filled('tipe_harga') && $request->tipe_harga === 'otomatis') {
            // Hitung ulang HPP secara realtime dari BOM
            $product->load('boms.material');
            $totalHpp = 0;
            foreach ($product->boms as $bom) {
                if ($bom->material) {
                    // ✅ FIX: Paksa cast ke float agar perkalian desimal akurat
                    $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                    $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                    
                    $totalHpp += $hargaSatuan * $jumlahKebutuhan;
                }
            }
            $margin = 0.30;
            $hargaJual = ceil($totalHpp + ($totalHpp * $margin));
        } else {
            // Mode manual: pakai input user
            if (!empty($request->harga_jual)) {
                $hargaJual = (int) str_replace(['.', ',', 'Rp', ' '], '', $request->harga_jual);
            }
        }

        if ($request->hasFile('foto')) {
            if ($product->foto) Storage::disk('public')->delete($product->foto);
            $product->foto = $request->file('foto')->store('produk-jadi', 'public');
        }

        if ($request->hasFile('sketsa')) {
            if ($product->sketsa) Storage::disk('public')->delete($product->sketsa);
            $product->sketsa = $request->file('sketsa')->store('produk/sketsa', 'public');
        }

        $product->update([
            'nama_produk'  => $validated['nama_produk'],
            'kategori'     => isset($validated['kategori']) ? json_encode($validated['kategori']) : $product->kategori,
            'warna'        => isset($validated['warna']) ? json_encode($validated['warna']) : $product->warna,
            'jenis_motif'  => isset($validated['jenis_motif']) ? json_encode($validated['jenis_motif']) : $product->jenis_motif,
            'harga_jual'   => $hargaJual,
            'deskripsi'    => $validated['deskripsi'] ?? $product->deskripsi,
            'keterangan'   => $validated['keterangan'] ?? $product->keterangan,
            'stok_minimum' => $validated['stok_minimum'] ?? $product->stok_minimum,
            'panjang'      => $validated['panjang'] ?? $product->panjang,
            'lebar'        => $validated['lebar'] ?? $product->lebar,
            'tinggi'       => $validated['tinggi'] ?? $product->tinggi,
            'detail_pola'  => $validated['detail_pola'] ?? $product->detail_pola,
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
        
        // Margin 30% (0.3)
        $margin = 0.30;
        $hargaJual = $totalHpp + ($totalHpp * $margin);
        
        return response()->json([
            'hpp' => $totalHpp,
            'margin' => $margin * 100,
            'harga_jual' => ceil($hargaJual) // Pembulatan ke atas
        ]);
    }

    /**
     * Generate Kode Otomatis
     */
    private function generateKodeProduk(): string
    {
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PRD-';

        // Cari kode produk terakhir dengan prefix yang sama
        $lastProduct = ProdukJadi::where('kode_produk', 'LIKE', $prefix . '%')
            ->orderBy('kode_produk', 'desc')
            ->first();

        if ($lastProduct) {
            // Ambil nomor dari kode terakhir
            $lastNumber = (int) str_replace($prefix, '', $lastProduct->kode_produk);
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada, mulai dari 1
            $newNumber = 1;
        }

        // Format nomor dengan padding nol (misal: 0001)
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
