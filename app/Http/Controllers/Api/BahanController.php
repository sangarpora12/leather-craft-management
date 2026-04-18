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
            
            $bahans = BahanBaku::where('nama_bahan', 'like', '%' . $query . '%')
                    ->select('id', 'kode_bahan', 'nama_bahan', 'stok', 'satuan', 'harga_satuan')
                    ->limit(10)
                    ->get();
            
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
                'satuan' => 'required|string|in:pcs,kg,meter,liter',
                'no_nota' => 'nullable|string|max:100',
                'supplier' => 'nullable|string|max:255',
                'foto' => 'nullable|string',
                'action' => 'required|string|in:update_stok,create_new',
                'kode_bahan' => 'nullable|string|max:50'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $action = $request->input('action');
            $namaBahan = $request->input('nama_bahan');
            $jumlah = $request->input('jumlah');
            $satuan = $request->input('satuan');
            $noNota = $request->input('no_nota');
            $supplier = $request->input('supplier');
            $foto = $request->input('foto');
            
            if ($action === 'update_stok') {
                // Update stok bahan yang sudah ada
                $kodeBahan = $request->input('kode_bahan');
                
                if (empty($kodeBahan)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode bahan harus diisi untuk update stok'
                    ], 400);
                }
                
                $bahan = BahanBaku::where('kode_bahan', $kodeBahan)->first();
                
                if (!$bahan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bahan dengan kode ' . $kodeBahan . ' tidak ditemukan'
                    ], 404);
                }
                
                // Update stok
                $bahan->stok += $jumlah;
                $bahan->save();
                
                // Catat log stok
                $this->catatLogStok($bahan->id, 'masuk', $jumlah, $noNota, $supplier, $foto);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Stok bahan berhasil diperbarui',
                    'redirect_url' => route('operator.dashboard')
                ]);
                
            } else {
                // Buat bahan baru
                $kodeBahan = $this->generateKodeBahanOtomatis($namaBahan);
                
                // Cek apakah nama bahan sudah ada
                $existing = BahanBaku::where('nama_bahan', $namaBahan)->first();
                
                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nama bahan sudah ada, gunakan nama yang berbeda'
                    ], 400);
                }
                
                // Buat bahan baru
                $bahan = new BahanBaku();
                $bahan->kode_bahan = $kodeBahan;
                $bahan->nama_bahan = $namaBahan;
                $bahan->stok = $jumlah;
                $bahan->satuan = $satuan;
                $bahan->status = 'aktif';
                $bahan->save();
                
                // Catat log stok
                $this->catatLogStok($bahan->id, 'masuk', $jumlah, $noNota, $supplier, $foto);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Bahan baru berhasil ditambahkan',
                    'redirect_url' => route('operator.dashboard')
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate kode bahan otomatis berdasarkan pengaturan gudang
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
                
                // Update counter
                $pengaturan->kode_bahan_counter = $counter + 1;
                $pengaturan->save();
                
                return $kode;
            }
            
            // Fallback: generate kode manual
            $timestamp = time();
            $random = rand(100, 999);
            $initials = $this->getInitials($namaBahan);
            
            return strtoupper($initials) . '-' . $timestamp . '-' . $random;
            
        } catch (\Exception $e) {
            // Fallback ekstrem
            $timestamp = time();
            $random = rand(1000, 9999);
            return 'BB-' . $timestamp . '-' . $random;
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