<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Models\BahanBaku;
use App\Notifications\BahanMasukNotification;
use App\Notifications\BahanMasukOperatorNotification;

class OcrController extends Controller
{
    // ==========================================
    // 1. FUNGSI UTAMA: PROCESS SCAN
    // ==========================================
    public function processScan(Request $request)
    {
        
        set_time_limit(120);

        try {
            // A. TANGKAP GAMBAR
            $base64_final = null;

            if ($request->hasFile('image') || $request->hasFile('nota')) {
                $file = $request->file('image') ?? $request->file('nota');
                $path = $file->getRealPath();
                $type = $file->getMimeType();
                $data = file_get_contents($path);
                $base64_final = 'data:' . $type . ';base64,' . base64_encode($data);
            } elseif ($request->input('image_base64')) {
                $base64_final = $request->input('image_base64');
            }

            if (!$base64_final) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar tidak ditemukan. Coba foto ulang.'
                ], 400);
            }

            // B. SIMPAN ARSIP GAMBAR
            $publicPath = null;
            try {
                $image_parts = explode(";base64,", $base64_final);
                $image_clean = $image_parts[1] ?? $base64_final;
                $image_decoded = base64_decode($image_clean);

                $fileName = 'nota_' . date('Ymd_His') . '.jpg';
                $publicPath = 'notas/' . $fileName;

                if (!Storage::disk('public')->exists('notas')) {
                    Storage::disk('public')->makeDirectory('notas');
                }
                Storage::disk('public')->put($publicPath, $image_decoded);
            } catch (\Exception $e) {
                \Log::error("Gagal simpan arsip gambar: " . $e->getMessage());
            }

            // C. PRE-PROCESSING GAMBAR (Intervention Image v3)
            $base64_for_api = $this->preprocessImage($base64_final);

            // D. KIRIM KE OCR.SPACE API UNTUK BACA TEKS
            $response = Http::timeout(120)
                ->asForm()
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => env('OCR_SPACE_API_KEY', 'K85657372788957'), // Ambil dari .env
                    'base64Image' => $base64_for_api,
                    'language' => 'eng',
                    'OCREngine' => '2',
                    'isTable' => 'true',
                    'scale' => 'true',
                    'detectOrientation' => 'true',
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi server OCR.'
                ], 500);
            }

            $result = $response->json();

            // E. CEK HASIL PARSING OCR.SPACE
            if (isset($result['ParsedResults'][0]['ParsedText'])) {
                $text = $result['ParsedResults'][0]['ParsedText'];
                
                // F. BERSIHKAN TYPO OCR
                $text = $this->cleanOcrText($text);
                
                // Hapus session lama
                session()->forget(['ocr_metadata', 'ocr_result']);
                
                // G. KIRIM KE GROQ AI UNTUK DISUSUN JADI JSON
                $aiResult = $this->parseWithGroq($text);
                
                if ($aiResult && isset($aiResult['items']) && count($aiResult['items']) > 0) {
                    // Simpan ke Session jika AI berhasil
                    session(['ocr_result' => [
                        'supplier' => $aiResult['supplier'] ?? 'Supplier Tidak Terdeteksi',
                        'metadata' => [
                            'no_nota' => $aiResult['no_nota'] ?? '',
                            'tanggal' => $aiResult['tanggal'] ?? date('Y-m-d'),
                            'alamat_kontak' => '',
                            'total_bayar' => $aiResult['total_harga'] ?? 0
                        ],
                        'items' => $aiResult['items'],
                        'image_url' => $publicPath ? asset('storage/' . $publicPath) : null
                    ]]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Scan Berhasil dirapikan oleh AI!',
                        'redirect_url' => route('operator.scan.result')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'AI gagal menyusun format nota, coba foto ulang dengan lebih jelas.'
                    ], 400);
                }

            } else {
                $errMsg = $result['ErrorMessage'][0] ?? 'Gagal mendeteksi teks. Pastikan cahaya cukup.';
                \Log::error("OCR Error: " . $errMsg);
                return response()->json([
                    'success' => false,
                    'message' => $errMsg
                ], 400);
            }

        } catch (\Throwable $e) {
            \Log::error("System Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // 2. PRE-PROCESSING GAMBAR 
    // ==========================================
    private function preprocessImage($base64Image)
    {
        try {
            $image_parts = explode(";base64,", $base64Image);
            $image_clean = $image_parts[1] ?? $base64Image;
            $image_decoded = base64_decode($image_clean);
            
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $img = $manager->read($image_decoded);
            
            $img->greyscale();
            $img->contrast(20);
            $img->brightness(10);
            
            $maxWidth = 1200;
            if ($img->width() > $maxWidth) {
                $img->resize(width: $maxWidth);
            }
            
            return $img->encodeByExtension('jpg', quality: 90)->toDataUri();
            
        } catch (\Exception $e) {
            \Log::error("Pre-processing error: " . $e->getMessage());
            return $base64Image; 
        }
    }

    // ==========================================
    // 3. CLEAN OCR TEXT (Membantu AI agar tidak bingung)
    // ==========================================
    private function cleanOcrText($text)
    {
        $replacements = [
            'Sa i' => 'Sapi', 'Sap i' => 'Sapi', 'Kult' => 'Kulit', 'Kuit' => 'Kulit',
            'Benan' => 'Benang', 'Iem' => 'Lem', 'Pofon' => 'Potongan', 'Kambin' => 'Kambing',
            'Ve efable' => 'Vegetable', 'Taned' => 'Tanned', 'Scra s' => 'Scrap', 'POI' => 'Polyester',
        ];

        $text = str_ireplace(array_keys($replacements), array_values($replacements), $text);
        $text = preg_replace('/[|_~^{}\\[\\]]/', '', $text);
        $text = preg_replace('/(?<!\d)(\d+)[\.\-\)]\s/', "\n$1. ", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\r\n|\r/', "\n", $text);

        return $text;
    }

    // ==========================================
    // 4. PARSER PINTAR (GROQ AI LLAMA 3)
    // ==========================================
    private function parseWithGroq($rawText)
    {
        $apiKey = env('GROQ_API_KEY');
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $prompt = "Kamu adalah sistem ERP. Rapikan teks hasil OCR nota toko material kulit ini.
        Kembalikan HANYA format JSON murni tanpa markdown, tanpa penjelasan apapun.
        
        Struktur JSON WAJIB seperti ini:
        {
            \"supplier\": \"Nama Toko/CV (isi 'Tidak Terdeteksi' jika tidak ada)\",
            \"no_nota\": \"Nomor nota\",
            \"tanggal\": \"YYYY-MM-DD\",
            \"total_harga\": 150000,
            \"items\": [
                { \"nama\": \"Nama Barang\", \"qty\": 2, \"satuan\": \"Pcs/Lembar/Meter\", \"harga\": 75000 }
            ]
        }
        
        Teks OCR Mentah: \n" . $rawText;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post($url, [
                    'model' => 'llama-3.3-70b-versatile', // Model Llama 3 yang super cepat
                    'messages' => [
                        ['role' => 'system', 'content' => 'You always reply in pure JSON format.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.1, // Jawaban konsisten, tidak berhalusinasi
                    'response_format' => ['type' => 'json_object'] // Memaksa Groq mengeluarkan JSON
                ]);

            if ($response->successful()) {
                $groqResponse = $response->json('choices.0.message.content');
                
                // Pembersihan ekstra
                $groqResponse = str_replace(['```json', '```'], '', $groqResponse);
                $parsedData = json_decode(trim($groqResponse), true);
                
                return $parsedData;
            }
            
            \Log::error("Groq API Gagal: " . $response->body());
            return null;

        } catch (\Exception $e) {
            \Log::error("Groq Request Error: " . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // 5. STORE STOK (SISTEM DATABASE AMAN)
    // ==========================================
    public function storeStok(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.nama' => 'required|string',
            'items.*.qty' => 'required|numeric',
            'items.*.harga' => 'nullable|numeric'
        ]);

        try {
            DB::beginTransaction();

            $updatedItems = [];
            $newItems = [];

            foreach ($request->input('items') as $item) {
                $namaInput = trim($item['nama']);
                $qtyMasuk  = (float) $item['qty'];
                $hargaBeli = (int) ($item['harga'] ?? 0);
                $satuan    = $item['satuan'] ?? 'Pcs';

                $barang = BahanBaku::whereRaw('LOWER(TRIM(nama_material)) = LOWER(TRIM(?))', [$namaInput])->first();

                if ($barang) {
                    $barang->stok += $qtyMasuk;
                    if ($hargaBeli > 0) $barang->harga_satuan = $hargaBeli;
                    $barang->save();
                    $updatedItems[] = "$namaInput (+$qtyMasuk)";
                    
                    DB::table('log_stok')->insert([
                        'bahan_baku_id' => $barang->id,
                        'jenis_transaksi' => 'MASUK - SCAN NOTA',
                        'jumlah' => $qtyMasuk,
                        'keterangan' => 'Bahan masuk via AI OCR',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    auth()->user()->notify(new BahanMasukOperatorNotification($barang, $qtyMasuk));
                    
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new BahanMasukNotification($barang, $qtyMasuk));
                    }
                } else {
                    $prefixBahan = \App\Models\Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
                    
                    do {
                        $kode = $prefixBahan . rand(1000, 9999);
                    } while (BahanBaku::where('kode_material', $kode)->exists());

                    $barangBaru = BahanBaku::create([
                        'kode_material' => $kode,
                        'nama_material' => $namaInput,
                        'stok' => $qtyMasuk,
                        'harga_satuan' => $hargaBeli,
                        'kategori' => 'Aksesoris',
                        'satuan' => $satuan,
                        'status' => 'Aman',
                        'supplier' => session('ocr_metadata.supplier') ?? 'Umum',
                        'user_id' => auth()->id()
                    ]);
                    $newItems[] = $namaInput;
                    
                    DB::table('log_stok')->insert([
                        'bahan_baku_id' => $barangBaru->id,
                        'jenis_transaksi' => 'MASUK - BARU',
                        'jumlah' => $qtyMasuk,
                        'keterangan' => 'Bahan baru via AI OCR',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            $pesan = "Berhasil! ";
            if ($updatedItems) $pesan .= "Restock: " . implode(', ', $updatedItems) . ". ";
            if ($newItems) $pesan .= "Baru: " . implode(', ', $newItems) . ".";

            return redirect()->route('operator.bahan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("STORE STOK ERROR: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}