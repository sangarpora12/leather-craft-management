<?php

namespace App\Http\Controllers;

use App\Models\BahanMasuk;
use App\Models\User;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Notifications\ScanNotaNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ScanController extends Controller
{
    /**
     * Display scan page for operator
     */
    public function index()
    {
        $satuans = Satuan::all();
        $kategories = Kategori::where('tipe', 'bahan')->get();
        return view('operator.scan-nota', compact('satuans', 'kategories'));
    }

    /**
     * Process OCR and save bahan masuk
     * Mendukung dua mode:
     * 1. OCR Mode: image_base64 only -> proses OCR -> simpan ke session -> redirect ke result
     * 2. Manual Mode: nama_material, jumlah, satuan -> langsung simpan
     */
    public function process(Request $request)
    {
        // Set JSON response header
        $request->headers->set('Accept', 'application/json');
        
        // Cek mode: Manual atau OCR
        if ($request->has('nama_material') && $request->has('jumlah')) {
            return $this->processManual($request);
        } elseif ($request->has('image_base64')) {
            return $this->processOCR($request);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap. Kirim image_base64 untuk OCR atau nama_material+jumlah untuk input manual.',
            ], 400);
        }
    }

    /**
     * Process manual input (tanpa OCR)
     */
    private function processManual(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama_material' => 'required|string',
            'jumlah' => 'required|numeric',
            'satuan' => 'required|string',
            'harga_satuan' => 'nullable|numeric',
            'supplier' => 'nullable|string',
            'ref_no' => 'nullable|string',
            'kategori' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate unique transaction number
            $noTransaksi = 'BM-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Save image if provided
            $imageName = null;
            if ($request->has('image_base64') && !empty($request->image_base64)) {
                $imageData = $request->image_base64;
                $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                
                $imageName = $noTransaksi . '.jpg';
                $imagePath = 'bukti_nota/' . $imageName;
                
                Storage::disk('public')->put($imagePath, base64_decode($imageData));
            }

            // Create bahan masuk record
            $bahanMasuk = BahanMasuk::create([
                'no_transaksi' => $noTransaksi,
                'operator_id' => auth()->id(),
                'nama_material' => $request->nama_material,
                'kode_material' => null,
                'jumlah' => $request->jumlah,
                'satuan' => $request->satuan,
                'harga_satuan' => $request->harga_satuan ?? 0,
                'supplier' => $request->supplier,
                'ref_no' => $request->ref_no,
                'kategori' => $request->kategori ?? 'Bahan Utama',
                'foto_nota' => $imageName,
                'lokasi_gudang' => 'Gudang Utama',
                'status' => 'pending',
                'notes' => null,
            ]);

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            $operatorName = auth()->user()->name ?? 'Operator';
            
            // Prepare scan data for notification
            $scanData = [
                'id' => $bahanMasuk->id,
                'bahan_id' => $bahanMasuk->id,
                'nama_bahan' => $bahanMasuk->nama_material,
                'items' => [
                    [
                        'nama' => $bahanMasuk->nama_material,
                        'jumlah' => $bahanMasuk->jumlah,
                        'satuan' => $bahanMasuk->satuan,
                        'harga' => $bahanMasuk->harga_satuan ?? 0,
                    ]
                ],
                'total_harga' => ($bahanMasuk->jumlah * ($bahanMasuk->harga_satuan ?? 0)),
            ];
            
            $notification = new ScanNotaNotification(
                $scanData, 
                $operatorName, 
                $bahanMasuk->foto_nota
            );
            
            Notification::send($admins, $notification);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'redirect_url' => route('operator.scan.success', ['id' => $bahanMasuk->id]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process OCR from image
     */
    private function processOCR(Request $request)
    {
        set_time_limit(120);

        try {
            // A. TANGKAP GAMBAR
            $base64_final = $request->input('image_base64');

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

            // C. PRE-PROCESSING GAMBAR
            $base64_for_api = $this->preprocessImage($base64_final);

            // D. KIRIM KE OCR.SPACE API
            $response = Http::timeout(120)
                ->asForm()
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => 'K85657372788957',
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

            // E. CEK HASIL PARSING
            if (isset($result['ParsedResults'][0]['ParsedText'])) {
                $text = $result['ParsedResults'][0]['ParsedText'];
                
                // F. POST-PROCESSING
                $text = $this->cleanOcrText($text);
                
                // Hapus session lama
                session()->forget(['ocr_metadata', 'ocr_result']);
                
                // G. PARSER PINTAR
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
    // PRE-PROCESSING GAMBAR
    // ==========================================
    private function preprocessImage($base64Image)
    {
        try {
            $image_parts = explode(";base64,", $base64Image);
            $image_clean = $image_parts[1] ?? $base64Image;
            $image_decoded = base64_decode($image_clean);
            
            $img = Image::make($image_decoded);
            
            // 1. Grayscale
            $img->greyscale();
            
            // 2. Tingkatkan kontras
            $img->contrast(20);
            
            // 3. Sesuaikan brightness
            $img->brightness(10);
            
            // 4. Resize agar file < 1MB
            $maxWidth = 1200;
            if ($img->width() > $maxWidth) {
                $img->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            return (string) $img->encode('data-url', 90);
            
        } catch (\Exception $e) {
            \Log::error("Pre-processing error: " . $e->getMessage());
            return $base64Image;
        }
    }

    // ==========================================
    // CLEAN OCR TEXT
    // ==========================================
    private function cleanOcrText($text)
    {
        $replacements = [
            'Sa i' => 'Sapi',
            'Sap i' => 'Sapi',
            'Sap' => 'Sapi',
            'Kult' => 'Kulit',
            'Kuit' => 'Kulit',
            'Kuli' => 'Kulit',
            'Benan' => 'Benang',
            'Benng' => 'Benang',
            'Iem' => 'Lem',
            'Lm' => 'Lem',
            'Kambin' => 'Kambing',
            'Kambng' => 'Kambing',
            'Ve efable' => 'Vegetable',
            'Vegetble' => 'Vegetable',
            'Taned' => 'Tanned',
            'Scra s' => 'Scrap',
            'POI' => 'Polyester',
        ];

        $text = str_ireplace(array_keys($replacements), array_values($replacements), $text);
        $text = preg_replace('/[|_~^{}\\[\\]]/', '', $text);
        $text = preg_replace('/(?<!\d)(\d+)[\.\-\)]\s/', "\n$1. ", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\r\n|\r/', "\n", $text);

        return $text;
    }

    // ==========================================
    // PARSER PINTAR
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

        $sampah = [
            'Subtotal', 'Diskon', 'TOTAL', 'BAYAR', 'Kembali', 'Terima Kasih', 'Hormat', 
            'BCA', 'Mandiri', 'Debit', 'Transfer', 'Item', 'Qty', 'Harga', 'Satuan', 
            'Total', 'Jl-', 'Telp', 'Waktu', 'No. Transaksi', 'Tanggal', 'PPN',
            'Grand Total', 'Grandtotal', 'Payment', 'Cash', 'Card',
            'Invoice', 'Faktur', 'Receipt', 'Struk', 'Nota', 'Bill', 'Tagihan',
            'Customer', 'Pelanggan', 'Kasir', 'Cashier', 'Operator', 'Staff',
            'Harga Satuan', 'Harga Total', 'Jumlah', 'Quantity', 'Amount', 'Nominal',
            'Terbilang', 'Material Description', 'NOTA PENJUALAN', 'Nama Barang',
        ];

        $kataBahan = [
            'kulit', 'kain', 'benang', 'lem', 'cat', 'minyak', 'sabun', 
            'pewarna', 'bahan', 'tekstil', 'wol', 'kapas', 'sutra',
            'nilon', 'polyester', 'karet', 'plastik', 'logam', 'besi',
            'kayu', 'kertas', 'karton', 'gabus', 'busa', 'kapuk',
            'resleting', 'kancing', 'aksesoris', 'jahit', 'bordir',
            'sol', 'hak', 'tali', 'ritsleting', 'velcro', 'gesper',
            'pelapis', 'perekat', 'pengeras', 'pelunak', 'pewangi',
            'dompet', 'tas', 'sepatu', 'ikat pinggang', 'jaket',
            'sapi', 'kambing', 'vegetable', 'tanned', 'scrap', 'chrom', 'brown',
            'nabati', 'krom', 'lilin', 'potongan'
        ];

        $satuanList = 'Lembar|Kaki|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks';

        // TAHAP 1: EKSTRAK METADATA
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(UD\.?|TOKO|CV\.?|PT\.?)\s+(.+)/i', $line, $m)) {
                $supplier = trim($line);
                break;
            }
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/(?:No\.?|Inv)\s*[:\-]?\s*([A-Za-z0-9\-\/]+)/i', $line, $m)) {
                if (!empty($m[1]) && strlen($m[1]) > 2 && strlen($m[1]) < 25) {
                    $noNota = $m[1];
                    break;
                }
            }
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/(?:Tanggal|Tgl)\s*[:\-]?\s*(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $line, $m)) {
                $tanggal = $m[1];
                break;
            } elseif (preg_match('/(?:Tanggal|Tgl)\s*[:\-]?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/', $line, $m)) {
                $tanggal = $m[1];
                break;
            }
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(Jl\.?|Jalan|Telp|Phone|Tel)\.?\s*[:\-]?\s*(.+)/i', $line, $m)) {
                $alamat = trim($line);
                break;
            }
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/(?:TOTAL|Total|Jumlah|Bayar)\s*[:\-]?\s*Rp\s*([\d\.,]+)/i', $line, $m)) {
                $totalHarga = (int) str_replace(['.', ','], '', $m[1]);
                break;
            }
        }

        // TAHAP 2: EKSTRAK ITEM BAHAN
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strlen($line) < 3) continue;
            if (strlen($line) > 150) continue;

            $isSampah = false;
            foreach ($sampah as $word) {
                if (stripos($line, $word) !== false) { 
                    $isSampah = true; 
                    break; 
                }
            }
            if ($isSampah) continue;

            if (preg_match('/(?:TOTAL|Subtotal|Grand Total)/i', $line)) continue;
            if (preg_match('/(?:TERBILANG|Hormat|Tanda Terima)/i', $line)) continue;

            $nama = "";
            $qty = 1;
            $harga = 0;
            $satuan = 'Pcs';

            // POLA 1: Format lengkap dengan satuan
            if (preg_match('/^(\d+)[\.\-\)]\s*(.+?)\s+(\d+)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = (int) $matches[3];
                $satuan = $matches[4];
                $harga = (int) str_replace(['.', ','], '', $matches[5]);
            }
            // POLA 1B: Format tanpa titik
            elseif (preg_match('/^(\d+)\s+(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $satuan = $matches[3];
                $hargaSatuan = (int) str_replace(['.', ','], '', $matches[4]);
                $totalHargaItem = (int) str_replace(['.', ','], '', $matches[5]);
                $qty = ($hargaSatuan > 0) ? (int) ($totalHargaItem / $hargaSatuan) : 1;
                $harga = $hargaSatuan;
            }
            // POLA 2: Format dengan titik, satuan tanpa qty
            elseif (preg_match('/^(\d+)[\.\-\)]\s*(.+?)\s+(Kaki Persegi|Kaki|Lembar|Rol|Pcs|Unit|Kg|Liter|Box|Sak|Lusin|Kodi|Pack|Botol|Kaleng|Tube|Sachet|Bungkus|Buah|Batang|Helai|Potong|Set|Pasang|Biji|Lbr|Btl|Bks)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = 1;
                $satuan = $matches[3];
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
            }
            // POLA 3: Format sederhana
            elseif (preg_match('/^(\d+)\s+(.+?)\s+(\d+)\s+([\d\.,]+)/i', $line, $matches)) {
                $nama = trim($matches[2]);
                $qty = (int) $matches[3];
                $harga = (int) str_replace(['.', ','], '', $matches[4]);
                $satuan = 'Pcs';
            }
            // POLA 4: Format dengan kata bahan
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
                    if (preg_match('/^(\d+)[\.\-\)\s]\s*(.+?)\s+(\d+)\s+(.+?)\s+(\d[\d\.,]*)/', $line, $matches)) {
                        $nama = trim($matches[2]);
                        $qty = (int) $matches[3];
                        if (preg_match('/(' . $satuanList . ')/i', $matches[4], $satMatch)) {
                            $satuan = $satMatch[1];
                        }
                        $harga = (int) str_replace(['.', ','], '', $matches[5]);
                    } elseif (preg_match('/^(\d+)[\.\-\)\s]\s*(.+)/', $line, $matches)) {
                        $nama = trim($matches[2]);
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

            if (empty($nama)) continue;
            
            $nama = trim($nama, " .-");
            $nama = preg_replace('/^\d+[\.\-\)]\s*/', '', $nama);
            $nama = preg_replace('/^\d+\s*/', '', $nama);
            $nama = preg_replace('/\s+/', ' ', $nama);

            if (strlen($nama) < 3 || ctype_digit($nama)) {
                continue;
            }

            if (strlen($nama) > 2) {
                $data[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'satuan' => $satuan,
                    'harga' => $harga
                ];
            }
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

    /**
     * Show success page after scan
     */
    public function success($id)
    {
        $bahanMasuk = BahanMasuk::findOrFail($id);
        return view('operator.scan-success', compact('bahanMasuk'));
    }

    /**
     * Display detail transaksi for admin
     */
    public function show($id)
    {
        $bahanMasuk = BahanMasuk::with(['operator', 'verifiedBy'])->findOrFail($id);
        
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.scan-detail', compact('bahanMasuk'));
    }

    /**
     * Verify bahan masuk (admin only)
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bahanMasuk = BahanMasuk::findOrFail($id);

        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Update data bahan masuk dengan data terverifikasi
            $bahanMasuk->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'jumlah' => $request->jumlah,
                'harga_satuan' => $request->harga_satuan,
                'notes' => $request->notes,
            ]);

            // Cari atau buat bahan baku
            $bahanBaku = \App\Models\BahanBaku::firstOrCreate(
                ['nama' => $bahanMasuk->nama_material],
                [
                    'kategori_id' => optional(\App\Models\Kategori::where('nama', $bahanMasuk->kategori)->first())->id,
                    'satuan_id' => optional(\App\Models\Satuan::where('nama', $bahanMasuk->satuan)->first())->id,
                    'stok' => 0,
                ]
            );

            // Tambah stok
            $bahanBaku->stok += $request->jumlah;
            $bahanBaku->save();

            // Catat ke log stok
            \App\Models\LogStok::create([
                'bahan_baku_id' => $bahanBaku->id,
                'bahan_masuk_id' => $bahanMasuk->id,
                'operator_id' => $bahanMasuk->operator_id,
                'jenis_transaksi' => 'TAMBAH_STOK',
                'jumlah' => $request->jumlah,
                'keterangan' => 'Verifikasi dari nota ' . $bahanMasuk->no_transaksi,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil diverifikasi dan stok diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memverifikasi data: ' . $e->getMessage());
        }
    }

    /**
     * Reject bahan masuk (admin only)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $bahanMasuk = BahanMasuk::findOrFail($id);

        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        try {
            $bahanMasuk->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'notes' => $request->notes,
            ]);

            return redirect()->back()->with('success', 'Data ditolak');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak data: ' . $e->getMessage());
        }
    }

    /**
     * Display list of pending bahan masuk for admin
     */
    public function pendingList()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $bahanMasukList = BahanMasuk::with('operator')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.scan-pending', compact('bahanMasukList'));
    }
}