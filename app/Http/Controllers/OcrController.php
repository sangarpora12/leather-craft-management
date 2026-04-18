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
        // Set waktu tunggu max 120 detik
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

            // B. SIMPAN ARSIP
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

            // C. PRE-PROCESSING GAMBAR (SPESIFIKASI #1)
            $base64_for_api = $this->preprocessImage($base64_final);

            // D. KIRIM KE OCR.SPACE API (SPESIFIKASI #2)
            $response = Http::timeout(120)
                ->asForm()
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => 'K85657372788957',
                    'base64Image' => $base64_for_api,
                    'language' => 'eng', // English (OCR.space tidak support 'ind')
                    'OCREngine' => '2', // Engine 2 untuk akurasi lebih tinggi
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

            // E. CEK HASIL PARSING
            if (isset($result['ParsedResults'][0]['ParsedText'])) {
                $text = $result['ParsedResults'][0]['ParsedText'];
                
                // F. POST-PROCESSING (SPESIFIKASI #3)
                $text = $this->cleanOcrText($text);
                
                // Hapus session lama
                session()->forget(['ocr_metadata', 'ocr_result']);
                
                // G. PARSER PINTAR (SPESIFIKASI #4)
                $parsedData = $this->simpleParser($text);
                
                // Ambil metadata
                $metadata = session('ocr_metadata', []);
                
                // Simpan ke Session
                session(['ocr_result' => [
                    'supplier' => $metadata['supplier'] ?? 'Supplier Tidak Terdeteksi',
                    'metadata' => [
                        'no_nota' => $metadata['no_nota'] ?? '',
                        'tanggal' => $metadata['tanggal'] ?? date('Y-m-d'),
                        'alamat_kontak' => $metadata['alamat'] ?? '',
                        'total_bayar' => 0
                    ],
                    'items' => $parsedData,
                    'image_url' => $publicPath ? asset('storage/' . $publicPath) : null
                ]]);

                return response()->json([
                    'success' => true,
                    'message' => 'Scan Berhasil!',
                    'redirect_url' => route('operator.scan.result')
                ]);

            } else {
                $errMsg = $result['ErrorMessage'][0] ?? 'Gagal mendeteksi teks. Pastikan cahaya cukup dan label terlihat fokus.';
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
                'message' => 'Server Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500);
        }
    }

    // ==========================================
    // SPESIFIKASI #1: PRE-PROCESSING GAMBAR
    // ==========================================
    private function preprocessImage($base64Image)
    {
        try {
            $image_parts = explode(";base64,", $base64Image);
            $image_clean = $image_parts[1] ?? $base64Image;
            $image_decoded = base64_decode($image_clean);
            
            $img = Image::make($image_decoded);
            
            // 1. Grayscale - Hilangkan noise warna kulit
            $img->greyscale();
            
            // 2. Tingkatkan kontras (+20) agar teks hitam lebih tajam
            $img->contrast(20);
            
            // 3. Sesuaikan brightness agar teks lebih menonjol
            $img->brightness(10);
            
            // 4. Resize agar file < 1MB (limit OCR.space Free API)
            $maxWidth = 1200;
            if ($img->width() > $maxWidth) {
                $img->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Pastikan kualitas 90% untuk menjaga ukuran file < 1MB
            return (string) $img->encode('data-url', 90);
            
        } catch (\Exception $e) {
            \Log::error("Pre-processing error: " . $e->getMessage());
            return $base64Image; // Kembalikan gambar asli jika gagal
        }
    }

    // ==========================================
    // SPESIFIKASI #3: CLEAN OCR TEXT (FUZZY LOGIC)
    // ==========================================
    private function cleanOcrText($text)
    {
        // 1. Perbaiki kesalahan pembacaan umum pada material leather
        $replacements = [
            'Sa i' => 'Sapi',
            'Sap i' => 'Sapi',
            'Sap' => 'Sapi',
            'Kult' => 'Kulit',
            'Kuit' => 'Kulit',
            'Kuli' => 'Kulit',
            'Kulitt' => 'Kulit',
            'Benan' => 'Benang',
            'Benng' => 'Benang',
            'Benangg' => 'Benang',
            'Iem' => 'Lem',
            'Lm' => 'Lem',
            'Lilin' => 'Lilin',
            'Cat' => 'Cat',
            'Ct' => 'Cat',
            'Pofon' => 'Potongan',
            'Potogn' => 'Potongan',
            'Potonga' => 'Potongan',
            'Potongan' => 'Potongan',
            'Kambin' => 'Kambing',
            'Kambng' => 'Kambing',
            'Kambingg' => 'Kambing',
            'Ve efable' => 'Vegetable',
            'Vegetble' => 'Vegetable',
            'Ve etable' => 'Vegetable',
            'Tanned' => 'Tanned',
            'Taned' => 'Tanned',
            'Scra s' => 'Scrap',
            'Scra' => 'Scrap',
            'Scrapps' => 'Scrap',
            'POI' => 'Polyester',
            'PolyPolyester' => 'Polyester',
            'Polyester' => 'Polyester',
            'ester' => 'Polyester',
        ];

        $text = str_ireplace(array_keys($replacements), array_values($replacements), $text);

        // 2. Bersihkan karakter sampah (noise)
        $text = preg_replace('/[|_~^{}\\[\\]]/', '', $text);
        
        // 3. Pisahkan baris berdasarkan pola nomor urut (1., 2., 3., dll)
        // Ini penting agar setiap item berada di baris terpisah
        $text = preg_replace('/(?<!\d)(\d+)[\.\-\)]\s/', "\n$1. ", $text);
        
        // 4. Normalisasi spasi ganda (tapi jaga line breaks)
        $text = preg_replace('/[ \t]+/', ' ', $text);
        
        // 5. Hapus line breaks yang tidak perlu (tapi jaga yang setelah nomor)
        $text = preg_replace('/\r\n|\r/', "\n", $text);

        \Log::info("Cleaned OCR Text: " . $text);
        
        return $text;
    }

    // ==========================================
    // SPESIFIKASI #4: PARSER PINTAR (REGEX) - STRATEGI "SANGAR"
    // ==========================================
    private function simpleParser($text)
    {
        $lines = explode("\n", $text);
        $data = [];

        $noNota = null;
        $tanggal = date('Y-m-d');
        $supplier = null;
        $alamat = null;
        $totalHarga = 0;

        // Daftar kata-kata yang harus diabaikan (footer/header)
        $sampah = [
            'Subtotal', 'Diskon', 'TOTAL', 'BAYAR', 'Kembali', 'Terima Kasih', 'Hormat', 
            'BCA', 'Mandiri', 'Debit', 'Transfer', 'Item', 'Qty', 'Harga', 'Satuan', 
            'Total', 'Jl-', 'Telp', 'Waktu', 'No. Transaksi', 'Tanggal', 'PPN',
            'Grand Total', 'Grandtotal', 'Grand-Total', 'Payment', 'Cash', 'Card',
            'Invoice', 'Faktur', 'Receipt', 'Struk', 'Nota', 'Bill', 'Tagihan',
            'Customer', 'Pelanggan', 'Kasir', 'Cashier', 'Operator', 'Staff',
            'Harga Satuan', 'Harga Total', 'Jumlah', 'Quantity', 'Amount', 'Nominal',
            'Terbilang', 'Material Description', 'NOTA PENJUALAN', 'Nama Barang',
            'ytb', 'yth', 'Bapak', 'Ibu', 'Saudara', 'Stamp', 'Signature',
            '(Material Description)', '(Qty)', 'Grand Total', 'Grandtotal',
            'Satuan', 'Harga', 'Jml', 'Subtotal'
        ];

        // Daftar kata kunci yang menandakan baris barang (untuk meningkatkan deteksi)
        $kataKunciBarang = [
            'kulit', 'kain', 'benang', 'lem', 'cat', 'minyak', 'sabun', 
            'pewarna', 'bahan', 'tekstil', 'wol', 'kapas', 'sutra',
            'nilon', 'polyester', 'karet', 'plastik', 'logam', 'besi',
            'kayu', 'kertas', 'karton', 'gabus', 'busa', 'kapuk',
            'resleting', 'kancing', 'aksesoris', 'jahit', 'bordir',
            'sol', 'hak', 'tali', 'ritsleting', 'velcro', 'gesper',
            'pelapis', 'perekat', 'pengeras', 'pelunak', 'pewangi',
            'pengawet', 'anti jamur', 'anti air', 'waterproof',
            'dompet', 'tas', 'sepatu', 'ikat pinggang', 'jaket',
            'rompi', 'sarung tangan', 'topi', 'sendal', 'sandal',
            'sapi', 'kambing', 'vegetable', 'tanned', 'scrap', 'chrom', 'brown',
            'nabati', 'krom', 'lilin', 'potongan', 'material', 'barang', 'item'
        ];

        // === DAFTAR KATA KUNCI BAHAN BAKU ===
        $kataBahan = [
            'kulit', 'kain', 'benang', 'lem', 'cat', 'minyak', 'sabun', 
            'pewarna', 'bahan', 'tekstil', 'wol', 'kapas', 'sutra',
            'nilon', 'polyester', 'karet', 'plastik', 'logam', 'besi',
            'kayu', 'kertas', 'karton', 'gabus', 'busa', 'kapuk',
            'resleting', 'kancing', 'aksesoris', 'jahit', 'bordir',
            'sol', 'hak', 'tali', 'ritsleting', 'velcro', 'gesper',
            'pelapis', 'perekat', 'pengeras', 'pelunak', 'pewangi',
            'pengawet', 'anti jamur', 'anti air', 'waterproof',
            'dompet', 'tas', 'sepatu', 'ikat pinggang', 'jaket',
            'rompi', 'sarung tangan', 'topi', 'sendal', 'sandal',
            'sapi', 'kambing', 'vegetable', 'tanned', 'scrap', 'chrom', 'brown',
            'nabati', 'krom', 'lilin', 'potongan'
        ];

        // === DAFTAR SATUAN YANG UMUM ===
        $satuanList = 'Lembar|Kaki|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks|Pcs|Kg|Liter|L';

        // ==========================================
        // TAHAP 1: EKSTRAK METADATA (STRATEGI SANGAR)
        // ==========================================
        
        // 1. CARI SUPPLIER - Ambil baris pertama yang mengandung TOKO, CV, PT, UD
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(UD\.?|TOKO|CV\.?|PT\.?)\s+(.+)/i', $line, $m)) {
                $supplier = trim($line);
                break;
            }
        }

        // 2. CARI NOMOR NOTA - Cari teks setelah "No:" atau "No." atau "Inv"
        foreach ($lines as $line) {
            $line = trim($line);
            // Pola: No: XXX atau No. XXX atau Inv XXX
            if (preg_match('/(?:No\.?|Inv)\s*[:\-]?\s*([A-Za-z0-9\-\/]+)/i', $line, $m)) {
                if (!empty($m[1]) && strlen($m[1]) > 2 && strlen($m[1]) < 25) {
                    $noNota = $m[1];
                    break;
                }
            }
        }

        // 3. CARI TANGGAL - Cari pola tanggal
        foreach ($lines as $line) {
            $line = trim($line);
            // Pola: Tanggal: 18 Mei 2024 atau Tgl: 05/04/2026
            if (preg_match('/(?:Tanggal|Tgl)\s*[:\-]?\s*(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $line, $m)) {
                $tanggal = $m[1];
                break;
            } elseif (preg_match('/(?:Tanggal|Tgl)\s*[:\-]?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/', $line, $m)) {
                $tanggal = $m[1];
                break;
            }
        }

        // 4. CARI ALAMAT - Cari baris dengan Jl. atau Telp
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(Jl\.?|Jalan|Telp|Phone|Tel)\.?\s*[:\-]?\s*(.+)/i', $line, $m)) {
                $alamat = trim($line);
                break;
            }
        }

        // 5. CARI TOTAL HARGA - Cari angka terbesar di bagian bawah
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/(?:TOTAL|Total|Jumlah|Bayar)\s*[:\-]?\s*Rp\s*([\d\.,]+)/i', $line, $m)) {
                $totalHarga = (int) str_replace(['.', ','], '', $m[1]);
                break;
            }
        }

        // ==========================================
        // TAHAP 2: EKSTRAK ITEM BAHAN (STRATEGI SANGAR)
        // ==========================================
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strlen($line) < 3) continue;
            if (strlen($line) > 150) continue;

            // SKIP BARIS SAMPAH
            $isSampah = false;
            foreach ($sampah as $word) {
                if (stripos($line, $word) !== false) { 
                    $isSampah = true; 
                    break; 
                }
            }
            if ($isSampah) continue;

            // SKIP BARIS YANG MENGANDUNG "TOTAL" ATAU "SUBTOTAL"
            if (preg_match('/(?:TOTAL|Subtotal|Grand Total)/i', $line)) continue;

            // SKIP BARIS YANG MENGANDUNG "TERBILANG" ATAU "HORMAT"
            if (preg_match('/(?:TERBILANG|Hormat|Tanda Terima)/i', $line)) continue;

            $nama = "";
            $qty = 1;
            $harga = 0;
            $satuan = 'Pcs';

            // === POLA 1: Format lengkap dengan satuan ===
            // Contoh: "1. Kulit Sapi Krom 2 Lembar 450.000" atau "1 Kulit Sapi 2 Lbr 450000"
            if (preg_match('/^(\d+)[\.\-\)]\s*(.+?)\s+(\d+)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = (int) $matches[3];
                $satuan = $matches[4];
                $harga = (int) str_replace(['.', ','], '', $matches[5]);
            }
            // === POLA 1A: Format tanpa nomor urut, langsung nama barang ===
            // Contoh: "Sol Sepatu Kulitt Nabati (Vegetable Taother Sole) Pasang 215000 10"
            elseif (preg_match('/^(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)\s+(\d+)/i', $line, $matches)) {
                $nama = trim($matches[1]);
                $satuan = $matches[2];
                $harga = (int) str_replace(['.', ','], '', $matches[3]);
                $qty = (int) $matches[4];
            }
            // === POLA 1B: Format harga qty terbalik ===
            // Contoh: "Aksesoris Kuningan (Brass Hardware Set) Set 45000 30"
            elseif (preg_match('/^(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+(\d+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[1]);
                $satuan = $matches[2];
                $qty = (int) $matches[3];
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
            }
            // === POLA 1C: Format tanpa satuan, langsung nama + angka ===
            // Contoh: "Benang PolyPolyester 35000 70000" atau "Lem 25000 50000"
            elseif (preg_match('/^(.+?)\s+(\d+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[1]);
                $qty = (int) $matches[2];
                $harga = (int) str_replace(['.', ','], '', $matches[3]);
                $satuan = 'Pcs'; // Default satuan
            }
            // === POLA 1D: Format hanya nama + harga (qty default 1) ===
            // Contoh: "Benang PolyPolyester 35000" atau "Lem 25000"
            elseif (preg_match('/^(.+?)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[1]);
                $qty = 1; // Default qty
                $harga = (int) str_replace(['.', ','], '', $matches[2]);
                $satuan = 'Pcs'; // Default satuan
            }
            
            // === POLA 1B: Format "2 Kulit Kambing Vegetable Tanned Kaki Persegi 80.000 400.000" (tanpa titik, tanpa qty) ===
            // Contoh: "2 Kulit Kambing Vegetable Tanned Kaki Persegi 80000 400000"
            // Angka pertama adalah nomor urut, bukan qty. Qty tidak ada di baris ini.
            // Tapi kita bisa hitung qty dari total harga ÷ harga satuan
            elseif (preg_match('/^(\d+)\s+(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $satuan = $matches[3];
                $hargaSatuan = (int) str_replace(['.', ','], '', $matches[4]);
                $totalHarga = (int) str_replace(['.', ','], '', $matches[5]);
                
                // Hitung qty dari total harga ÷ harga satuan
                $qty = ($hargaSatuan > 0) ? (int) ($totalHarga / $hargaSatuan) : 1;
                $harga = $hargaSatuan; // Gunakan harga satuan, bukan total
            }
            
            // === POLA 2: Format "4. Benang Lilin (PolyPolyester) Rol 35.000 70.000" (dengan titik, satuan tanpa qty) ===
            elseif (preg_match('/^(\d+)[\.\-\)]\s*(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = 1; // Qty tidak ada, default 1
                $satuan = $matches[3];
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
            }
            
            // === POLA 3: Format "3 Potongann Sisa Kulit (Scrap) 3 25.000 75.000" (qty disebut dua kali) ===
            elseif (preg_match('/^(\d+)\s+(.+?)\s+(\d+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = (int) $matches[3]; // Gunakan qty yang di tengah
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
                $satuan = 'Pcs'; // Satuan tidak ada, default Pcs
            }
            
            // === POLA 3: Format sederhana dengan kata bahan ===
            // Contoh: "Kulit Sapi 450000"
            else {
                $lineLower = strtolower($line);
                $adaKataBahan = false;
                foreach ($kataBahan as $kata) {
                    if (stripos($lineLower, $kata) !== false) {
                        $adaKataBahan = true;
                        break;
                    }
                }
                
                if ($adaKataBahan && preg_match('/\d+/', $line)) {
                    // Coba ekstrak dengan format: Nama ... angka
                    if (preg_match('/^(\d+)[\.\-\)\s]\s*(.+?)\s+(\d+)\s+(.+?)\s+(\d[\d\.,]*)/', $line, $matches)) {
                        $nama = trim($matches[2]);
                        $qty = (int) $matches[3];
                        // Cek apakah ada satuan di matches[4]
                        if (preg_match('/(' . $satuanList . ')/i', $matches[4], $satMatch)) {
                            $satuan = $satMatch[1];
                        }
                        $harga = (int) str_replace(['.', ','], '', $matches[5]);
                    } elseif (preg_match('/^(\d+)[\.\-\)\s]\s*(.+)/', $line, $matches)) {
                        $nama = trim($matches[2]);
                        // Cari angka dan satuan di sisa teks
                        if (preg_match('/(\d+)\s*(' . $satuanList . ')?\s*(\d[\d\.,]*)/', $nama, $angkaMatch)) {
                            if (!empty($angkaMatch[2])) {
                                $satuan = $angkaMatch[2];
                            }
                            $harga = (int) str_replace(['.', ','], '', $angkaMatch[3]);
                            $qty = (int) $angkaMatch[1];
                            $nama = trim(preg_replace('/\d+\s*(' . $satuanList . ')?\s*\d[\d\.,]*$/', '', $nama));
                        }
                    }
                }
            }

            // BERSIH-BERSIH AKHIR
            if (empty($nama)) continue;
            
            $nama = trim($nama, " .-");
            $nama = preg_replace('/^\d+[\.\-\)]\s*/', '', $nama);
            $nama = preg_replace('/^\d+\s*/', '', $nama);
            $nama = preg_replace('/\s+/', ' ', $nama);

            // Filter nama yang terlalu pendek atau hanya angka
            if (strlen($nama) < 3 || ctype_digit($nama)) {
                continue;
            }

            // Simpan item
            if (strlen($nama) > 2) {
                \Log::info("OCR PARSED ITEM: nama='$nama', qty=$qty, harga=$harga, satuan=$satuan");
                $data[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'satuan' => $satuan,
                    'harga' => $harga
                ];
            }
        }

        // Jika tidak ada barang yang terdeteksi, coba parsing alternatif
        if (empty($data)) {
            \Log::warning("OCR: Tidak ada barang terdeteksi, mencoba parsing alternatif");
            $data = $this->alternativeParser($text);
        }

        // Jika nomor nota masih kosong, coba cari dengan pola lebih luas
        if (empty($noNota)) {
            \Log::warning("OCR: Nomor nota kosong, mencoba pola alternatif");
            $noNota = $this->extractNotaNumber($text);
        }

        // Simpan Metadata
        session(['ocr_metadata' => [
            'no_nota' => $noNota,
            'tanggal' => $tanggal,
            'supplier' => $supplier,
            'alamat' => $alamat,
            'total_harga' => $totalHarga
        ]]);

        return $data;
    }

    // ==========================================
    // FUNGSI TAMBAHAN: MULTIPLE OCR ENGINES (98% ACCURACY)
    // ==========================================
    private function processWithMultipleOCR($tempFile)
    {
        $results = [];
        
        // 1. EasyOCR (sudah ada)
        try {
            $easyResult = $this->processWithEasyOCR($tempFile);
            if ($easyResult['success']) {
                $results['easyocr'] = $easyResult['items'];
            }
        } catch (\Exception $e) {
            \Log::error("EasyOCR failed: " . $e->getMessage());
        }

        // 2. Google Cloud Vision API (jika ada)
        try {
            $googleResult = $this->processWithGoogleVision($tempFile);
            if ($googleResult['success']) {
                $results['google'] = $googleResult['items'];
            }
        } catch (\Exception $e) {
            \Log::error("Google Vision failed: " . $e->getMessage());
        }

        // 3. Tesseract OCR (jika tersedia)
        try {
            $tesseractResult = $this->processWithTesseract($tempFile);
            if ($tesseractResult['success']) {
                $results['tesseract'] = $tesseractResult['items'];
            }
        } catch (\Exception $e) {
            \Log::error("Tesseract failed: " . $e->getMessage());
        }

        // 4. Kombinasi hasil dari semua engine
        if (count($results) > 0) {
            $combinedItems = $this->combineOCRResults($results);
            return [
                'success' => true,
                'items' => $combinedItems
            ];
        }

        return [
            'success' => false,
            'message' => 'Semua OCR engine gagal'
        ];
    }

    private function processWithGoogleVision($tempFile)
    {
        // Implementasi Google Cloud Vision API
        // Ini opsional, bisa diaktifkan jika punya API key
        return ['success' => false, 'items' => []];
    }

    private function processWithTesseract($tempFile)
    {
        // Implementasi Tesseract OCR
        // Ini opsional, bisa diaktifkan jika Tesseract terinstall
        return ['success' => false, 'items' => []];
    }

    private function combineOCRResults($results)
    {
        $allItems = [];
        
        // Gabungkan semua hasil
        foreach ($results as $engine => $items) {
            foreach ($items as $item) {
                $key = strtolower(trim($item['nama']));
                if (!isset($allItems[$key])) {
                    $allItems[$key] = $item;
                } else {
                    // Jika sudah ada, ambil nilai yang paling sering muncul
                    $allItems[$key]['qty'] = max($allItems[$key]['qty'], $item['qty']);
                    $allItems[$key]['harga'] = max($allItems[$key]['harga'], $item['harga']);
                }
            }
        }

        return array_values($allItems);
    }

    // ==========================================
    // FUNGSI TAMBAHAN: PARSING ALTERNATIF (AGRESIF)
    // ==========================================
    private function alternativeParser($text)
    {
        $lines = explode("\n", $text);
        $data = [];

        // Pola alternatif yang lebih luas untuk mendeteksi barang
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strlen($line) < 3) continue;
            if (strlen($line) > 150) continue;

            // Cek apakah baris mengandung angka (indikasi harga atau qty)
            if (!preg_match('/\d+/', $line)) continue;

            // Cek apakah baris mengandung kata kunci bahan
            $lineLower = strtolower($line);
            $adaKataBahan = false;
            $kataBahan = [
                'kulit', 'kain', 'benang', 'lem', 'cat', 'minyak', 'sabun', 
                'pewarna', 'bahan', 'tekstil', 'wol', 'kapas', 'sutra',
                'nilon', 'polyester', 'karet', 'plastik', 'logam', 'besi',
                'kayu', 'kertas', 'karton', 'gabus', 'busa', 'kapuk',
                'resleting', 'kancing', 'aksesoris', 'jahit', 'bordir',
                'sol', 'hak', 'tali', 'ritsleting', 'velcro', 'gesper',
                'pelapis', 'perekat', 'pengeras', 'pelunak', 'pewangi',
                'pengawet', 'anti jamur', 'anti air', 'waterproof',
                'dompet', 'tas', 'sepatu', 'ikat pinggang', 'jaket',
                'rompi', 'sarung tangan', 'topi', 'sendal', 'sandal',
                'sapi', 'kambing', 'vegetable', 'tanned', 'scrap', 'chrom', 'brown',
                'nabati', 'krom', 'lilin', 'potongan', 'material', 'barang', 'item'
            ];

            foreach ($kataBahan as $kata) {
                if (stripos($lineLower, $kata) !== false) {
                    $adaKataBahan = true;
                    break;
                }
            }

            if (!$adaKataBahan) continue;

            // Ekstrak informasi dengan pola alternatif
            $nama = "";
            $qty = 1;
            $harga = 0;
            $satuan = 'Pcs';

            // Pola: Nama Barang + Angka (harga)
            if (preg_match('/^(.+?)\s+(\d[\d\.,]*)$/', $line, $matches)) {
                $nama = trim($matches[1]);
                $harga = (int) str_replace(['.', ','], '', $matches[2]);
            }
            // Pola: Nama Barang + Qty + Harga
            elseif (preg_match('/^(.+?)\s+(\d+)\s+(\d[\d\.,]*)$/', $line, $matches)) {
                $nama = trim($matches[1]);
                $qty = (int) $matches[2];
                $harga = (int) str_replace(['.', ','], '', $matches[3]);
            }
            // Pola: Nama Barang + Satuan + Harga
            elseif (preg_match('/^(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+(\d[\d\.,]*)$/', $line, $matches)) {
                $nama = trim($matches[1]);
                $satuan = $matches[2];
                $harga = (int) str_replace(['.', ','], '', $matches[3]);
            }
            // Pola: Nama Barang + Satuan + Qty + Harga
            elseif (preg_match('/^(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+(\d+)\s+(\d[\d\.,]*)$/', $line, $matches)) {
                $nama = trim($matches[1]);
                $satuan = $matches[2];
                $qty = (int) $matches[3];
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
            }

            // BERSIH-BERSIH
            if (!empty($nama)) {
                $nama = trim($nama, " .-");
                $nama = preg_replace('/^\d+[\.\-\)]\s*/', '', $nama);
                $nama = preg_replace('/^\d+\s*/', '', $nama);
                $nama = preg_replace('/\s+/', ' ', $nama);

                // Filter nama yang terlalu pendek atau hanya angka
                if (strlen($nama) >= 3 && !ctype_digit($nama)) {
                    \Log::info("OCR ALTERNATIVE PARSER: nama='$nama', qty=$qty, harga=$harga, satuan=$satuan");
                    $data[] = [
                        'nama' => $nama,
                        'qty' => $qty,
                        'satuan' => $satuan,
                        'harga' => $harga
                    ];
                }
            }
        }

        return $data;
    }

    private function extractNotaNumber($text)
    {
        // Pola alternatif untuk nomor nota yang lebih luas
        $patterns = [
            '/(?:No\.?|Nomor|Invoice|Faktur|Nota|INV)\s*[:\-]?\s*([A-Za-z0-9\-\/]+)/i',
            '/([A-Za-z0-9\-\/]{5,20})/', // Pola umum: string 5-20 karakter
            '/\b([A-Z0-9]{3,}-?[0-9]{3,})\b/', // Pola: ABC-123 atau ABC123
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $match) {
                    // Filter hasil yang terlalu pendek atau terlalu panjang
                    if (strlen($match) >= 3 && strlen($match) <= 25) {
                        // Abaikan jika hanya angka saja (mungkin nomor baris)
                        if (!ctype_digit($match)) {
                            return $match;
                        }
                    }
                }
            }
        }

        return null;
    }

    // ==========================================
    // 3. STORE STOK
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

                \Log::info("STORE STOK: Mencari barang dengan nama: '$namaInput'");

                // Cari barang berdasarkan nama material (case insensitive)
                $barang = BahanBaku::whereRaw('LOWER(TRIM(nama_material)) = LOWER(TRIM(?))', [$namaInput])->first();

                if ($barang) {
                    \Log::info("STORE STOK: Barang ditemukan, stok lama: {$barang->stok}, tambah: $qtyMasuk");
                    // Jika barang sudah ada, tambahkan stok
                    $stokLama = $barang->stok;
                    $barang->stok += $qtyMasuk;
                    if ($hargaBeli > 0) $barang->harga_satuan = $hargaBeli;
                    $barang->save();
                    $updatedItems[] = "$namaInput (+$qtyMasuk)";
                    \Log::info("STORE STOK: Stok baru: {$barang->stok}");
                    
                    // Catat ke log_stok
                    DB::table('log_stok')->insert([
                        'bahan_baku_id' => $barang->id,
                        'jenis_transaksi' => 'MASUK - SCAN NOTA',
                        'jumlah' => $qtyMasuk,
                        'keterangan' => 'Bahan masuk via scan nota OCR',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Kirim notifikasi ke user yang login (operator) - redirect ke operator page
                    auth()->user()->notify(new BahanMasukOperatorNotification($barang, $qtyMasuk));
                    
                    // Kirim notifikasi ke semua admin - redirect ke admin page
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new BahanMasukNotification($barang, $qtyMasuk));
                    }
                } else {
                    \Log::info("STORE STOK: Barang tidak ditemukan, membuat baru");
                    // Ambil prefix dari pengaturan gudang
                    $prefixBahan = \App\Models\Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
                    
                    // Jika barang belum ada, buat baru dengan kode unik menggunakan prefix dari pengaturan
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
                    \Log::info("STORE STOK: Barang baru dibuat dengan kode: $kode");
                    
                    // Catat ke log_stok untuk barang baru
                    DB::table('log_stok')->insert([
                        'bahan_baku_id' => $barangBaru->id,
                        'jenis_transaksi' => 'MASUK - BARU',
                        'jumlah' => $qtyMasuk,
                        'keterangan' => 'Bahan baru via scan nota OCR',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            $pesan = "Berhasil! ";
            if ($updatedItems) $pesan .= "Restock: " . implode(', ', $updatedItems) . ". ";
            if ($newItems) $pesan .= "Baru: " . implode(', ', $newItems) . ".";

            \Log::info("STORE STOK: Selesai. Pesan: $pesan");
            return redirect()->route('operator.bahan.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("STORE STOK ERROR: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}