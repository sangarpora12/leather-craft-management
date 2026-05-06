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
     * Handle manual input bahan
     */
    public function manualInput(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_bahan' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0.01',
                'satuan' => 'required|string',
                'warna' => 'required|string', // Tambahkan validasi warna
                'action' => 'required|string|in:update_stok,create_new',
                // ... validasi lainnya ...
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            // OTOMATIS: Gabungkan Nama + Warna seperti di Controller Web
            $namaLengkap = $request->input('nama_bahan') . " (" . $request->input('warna') . ")";
            $jumlah = $request->input('jumlah');

            if ($request->input('action') === 'update_stok') {
                // Cari berdasarkan nama lengkap (varian spesifik)
                $bahan = BahanBaku::where('nama_bahan', $namaLengkap)->first();

                if (!$bahan) {
                    return response()->json(['success' => false, 'message' => 'Varian ' . $namaLengkap . ' tidak ditemukan'], 404);
                }

                $bahan->stok += $jumlah;
                $bahan->save();

                $this->catatLogStok($bahan->id, 'masuk', $jumlah, $request->no_nota, $request->supplier, $request->foto);

                return response()->json(['success' => true, 'message' => 'Stok varian berhasil diperbarui']);
            } else {
                // Buat bahan/varian baru
                $bahan = new BahanBaku();
                $bahan->kode_bahan = $this->generateKodeBahanOtomatis($request->nama_bahan);
                $bahan->nama_bahan = $namaLengkap; // Simpan Nama (Warna)
                $bahan->warna_id = $request->input('warna'); // Pastikan kolom ini ada di DB
                $bahan->stok = $jumlah;
                $bahan->satuan = $request->input('satuan');
                $bahan->status = 'aktif';
                $bahan->save();

                $this->catatLogStok($bahan->id, 'masuk', $jumlah, $request->no_nota, $request->supplier, $request->foto);

                return response()->json(['success' => true, 'message' => 'Varian baru berhasil ditambahkan']);
            }
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