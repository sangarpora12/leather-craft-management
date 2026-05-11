<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BahanController extends Controller
{
    /**
     * Search bahan by name
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            
            if (strlen($query) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query minimal 3 karakter',
                    'data' => []
                ]);
            }
            
            $queryBuilder = BahanBaku::where('nama_bahan', 'like', '%' . $query . '%');

            if ($request->has('is_inventory')) {
                $isInventory = $request->input('is_inventory');
                if ($isInventory === '1' || $isInventory === '0') {
                    $queryBuilder->where('is_inventory', $isInventory);
                }
            }

$bahans = $queryBuilder->select('id', 'kode_bahan', 'nama_bahan', 'stok', 'satuan', 'harga_satuan', 'foto', 'kategori', 'warna_id', 'deskripsi_bahan', 'detail')
                    ->limit(10)
                    ->get();
            
            // Tambahkan full URL foto - 100% BENAR
            $bahans->transform(function ($bahan) {
                // ✅ PINDAH KE FOLDER PUBLIC UPLOADS (TANPA STORAGE SYMLINK)
                $bahan->foto_url = $bahan->foto ? asset('uploads/bahan_baku/' . $bahan->foto) : '';
                $bahan->nama_material = $bahan->nama_bahan;
                $bahan->nama_bahan = $bahan->nama_bahan;
                $bahan->kode_material = $bahan->kode_bahan;
                $bahan->kode_bahan = $bahan->kode_bahan;
                return $bahan;
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $bahans
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    
    /**
     * Generate kode bahan otomatis
     */
    public function generateKodeBahan(Request $request)
    {
        try {
            $namaBahan = $request->input('nama_bahan', '');
            
            if (empty($namaBahan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama bahan harus diisi',
                    'kode_bahan' => null
                ]);
            }
            
            // Cek apakah bahan sudah ada
            $existing = BahanBaku::where('nama_bahan', $namaBahan)->first();
            
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bahan sudah ada, menggunakan kode yang tersedia',
                    'kode_bahan' => $existing->kode_bahan
                ]);
            }
            
            // Generate kode bahan otomatis
            $kodeBahan = $this->generateKodeBahanOtomatis($namaBahan);
            
            return response()->json([
                'success' => true,
                'message' => 'Kode bahan berhasil digenerate',
                'kode_bahan' => $kodeBahan
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'kode_bahan' => null
            ], 500);
        }
    }
    
    /**
     * Handle manual input bahan (Master-Variant + FIFO Layer)
     */
    public function manualInput(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_bahan' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0.01',
                'satuan' => 'required|string',
                'warna' => 'nullable|string',
                'harga_satuan' => 'nullable|numeric|min:0',
                'action' => 'required|string|in:update_stok,create_new',
                'no_nota' => 'nullable|string',
                'supplier' => 'nullable|string',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            $warna = $request->input('warna', 'Tidak Ada Warna');
            $jumlah = (float) $request->input('jumlah');
            $hargaSatuan = (float) ($request->input('harga_satuan', 0));

            // ============================================================
            // PROSES FOTO (FILE UPLOAD BIASA)
            // ============================================================
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                try {
                    $fotoPath = $request->file('foto')->store('bahan_baku', 'public');
                } catch (\Exception $e) {
                    \Log::warning('Gagal menyimpan foto: ' . $e->getMessage());
                }
            }

            // ============================================================
            // LOGIKA MASTER-VARIANT (SAMA SEPERTI storeOperator di Web)
            // ============================================================
            \DB::transaction(function () use ($request, $warna, $jumlah, $hargaSatuan, $fotoPath) {
                $namaMaterial = $request->input('nama_bahan');

                if ($request->input('action') === 'update_stok') {
                    // Cari berdasarkan kombinasi nama_material + warna
                    $bahan = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                        ->whereRaw('LOWER(warna) = LOWER(?)', [$warna])
                        ->lockForUpdate()
                        ->first();

                    if (!$bahan) {
                        // Jika tidak ditemukan, buat varian baru untuk warna ini
                        $existingMaster = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                            ->lockForUpdate()
                            ->first();

                        $kodeMaterial = $existingMaster ? $existingMaster->kode_material : $this->generateKodeBahanOtomatis($namaMaterial);

                        $bahan = BahanBaku::create([
                            'kode_material' => $kodeMaterial,
                            'nama_material' => $namaMaterial,
                            'warna' => $warna,
                            'stok' => 0,
                            'satuan' => $request->input('satuan'),
                            'harga_satuan' => $hargaSatuan,
                            'kategori' => $request->input('kategori', 'Bahan Baku'),
                            'is_inventory' => true,
                            'foto' => $fotoPath,
                        ]);
                    } else {
                        // Update foto jika ada
                        if ($fotoPath) {
                            $bahan->foto = $fotoPath;
                        }
                    }

                    $bahan->increment('stok', $jumlah);
                    if ($hargaSatuan > 0) {
                        $bahan->harga_satuan = $hargaSatuan;
                    }
                    $bahan->save();
                } else {
                    // Buat master baru + varian pertama
                    $existingMaster = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                        ->lockForUpdate()
                        ->first();

                    if ($existingMaster) {
                        // Cek apakah varian dengan warna ini sudah ada
                        $existingVarian = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                            ->whereRaw('LOWER(warna) = LOWER(?)', [$warna])
                            ->lockForUpdate()
                            ->first();

                        if ($existingVarian) {
                            $existingVarian->increment('stok', $jumlah);
                            if ($hargaSatuan > 0) {
                                $existingVarian->harga_satuan = $hargaSatuan;
                            }
                            if ($fotoPath) {
                                $existingVarian->foto = $fotoPath;
                            }
                            $existingVarian->save();
                            $bahan = $existingVarian;
                        } else {
                            // Buat varian baru dengan kode_material yang sama
                            $bahan = BahanBaku::create([
                                'kode_material' => $existingMaster->kode_material,
                                'nama_material' => $namaMaterial,
                                'warna' => $warna,
                                'stok' => $jumlah,
                                'satuan' => $request->input('satuan'),
                                'harga_satuan' => $hargaSatuan,
                                'kategori' => $request->input('kategori', 'Bahan Baku'),
                                'is_inventory' => true,
                                'foto' => $fotoPath,
                            ]);
                        }
                    } else {
                        // Bahan baru, generate kode
                        $kodeMaterial = $this->generateKodeBahanOtomatis($namaMaterial);

                        $bahan = BahanBaku::create([
                            'kode_material' => $kodeMaterial,
                            'nama_material' => $namaMaterial,
                            'warna' => $warna,
                            'stok' => $jumlah,
                            'satuan' => $request->input('satuan'),
                            'harga_satuan' => $hargaSatuan,
                            'kategori' => $request->input('kategori', 'Bahan Baku'),
                            'is_inventory' => true,
                            'foto' => $fotoPath,
                        ]);
                    }
                }

                // ============================================================
                // BUAT LAYER FIFO
                // ============================================================
                if (\Illuminate\Support\Facades\Schema::hasTable('bahan_stok_layers')) {
                    \App\Models\BahanStokLayer::create([
                        'bahan_baku_id' => $bahan->id,
                        'tanggal_masuk' => now()->toDateString(),
                        'qty_masuk' => $jumlah,
                        'qty_sisa' => $jumlah,
                        'harga_beli' => $hargaSatuan ?: ($bahan->harga_satuan ?: 0),
                        'supplier' => $request->input('supplier', null),
                        'ref_no' => $request->input('no_nota', null),
                    ]);
                }

                // Catat log stok
                $this->catatLogStok($bahan->id, 'masuk', $jumlah, $request->no_nota, $request->supplier, $fotoPath);
            });

            return response()->json(['success' => true, 'message' => 'Bahan berhasil disimpan dengan layer FIFO!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Generate kode bahan otomatis berdasarkan pengaturan gudang
     * ✅ UNIVERSAL INCREMENT COUNTER - SEMUA BAHAN MENGGUNAKAN NOMOR URUT YANG SAMA
     */
    private function generateKodeBahanOtomatis($namaBahan)
    {
        try {
            // Ambil pengaturan gudang
            $pengaturan = Pengaturan::first();
            
            if ($pengaturan && $pengaturan->kode_bahan_format) {
                // Gunakan format dari pengaturan
                $format = $pengaturan->kode_bahan_format;
                $counter = $pengaturan->kode_bahan_counter;
                
                // Generate kode sesuai format
                $kode = str_replace('{counter}', str_pad($counter, 4, '0', STR_PAD_LEFT), $format);
                
                // ✅ UPDATE COUNTER GLOBAL - SETIAP KALI DI GENERATE SELALU INCREMENT 1
                $pengaturan->kode_bahan_counter = $counter + 1;
                $pengaturan->save();
                
                // ✅ CEK UNIK JIKA SUDAH ADA, LEWATKAN NOMOR
                while(BahanBaku::where('kode_bahan', $kode)->exists()) {
                    $counter++;
                    $kode = str_replace('{counter}', str_pad($counter, 4, '0', STR_PAD_LEFT), $format);
                    $pengaturan->kode_bahan_counter = $counter + 1;
                    $pengaturan->save();
                }
                
                return $kode;
            }
            
            // ✅ GUNAKAN COUNTER GLOBAL SEBAGAI FALLBACK
            $lastBahan = BahanBaku::orderBy('id', 'desc')->first();
            $nextNumber = $lastBahan ? $lastBahan->id + 1 : 1;
            
            return 'BAH-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
        } catch (\Exception $e) {
            // Fallback ekstrem
            $lastBahan = BahanBaku::orderBy('id', 'desc')->first();
            $nextNumber = $lastBahan ? $lastBahan->id + 1 : 1;
            return 'BAH-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Get initials from nama bahan
     */
    private function getInitials($namaBahan)
    {
        $words = explode(' ', $namaBahan);
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
        }
        
        return substr($initials, 0, 3);
    }
    
    /**
     * Catat log stok
     */
    private function catatLogStok($bahanId, $jenis, $jumlah, $noNota = null, $supplier = null, $foto = null)
    {
        try {
            // Catat ke tabel log_stoks
            \DB::table('log_stoks')->insert([
                'bahan_baku_id' => $bahanId,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'no_nota' => $noNota,
                'supplier' => $supplier,
                'foto' => $foto,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Catat ke tabel log_stok_v2
            \DB::table('log_stok_v2')->insert([
                'bahan_baku_id' => $bahanId,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'no_nota' => $noNota,
                'supplier' => $supplier,
                'foto' => $foto,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan proses
            \Log::error('Gagal mencatat log stok: ' . $e->getMessage());
        }
    }
}