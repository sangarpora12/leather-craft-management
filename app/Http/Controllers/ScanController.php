<?php

namespace App\Http\Controllers;

use App\Models\BahanMasuk;
use App\Models\User;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Pengadaan;
use App\Models\Bahan;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Notifications\ScanNotaNotification;
use App\Notifications\ScanNotaBulkNotification;
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
    public function index(Request $request)
    {
        $pengadaan_id = $request->query('pengadaan_id');
        $satuans = Satuan::all();
        $kategories = Kategori::where('tipe', 'bahan')->get();
        $warnas = \App\Models\Warna::all();
        return view('operator.scan-nota', compact('satuans', 'kategories', 'warnas', 'pengadaan_id'));
    }

    public function showManualInputForm($pengadaan_id)
    {
        $kategories = \App\Models\Kategori::all();
        $satuans = \App\Models\Satuan::all();
        $warnas = \App\Models\Warna::all();

        return view('operator.scan.index', [
            'pengadaan_id' => $pengadaan_id,
            'kategories' => $kategories,
            'satuans' => $satuans,
            'warnas' => $warnas
        ]);
    }

    /**
     * Process OCR and save bahan masuk
     */
    public function process(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

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
            'warna' => 'required|string',
            'harga_satuan' => 'nullable|numeric',
            'supplier' => 'nullable|string',
            'ref_no' => 'nullable|string',
            'kategori' => 'nullable|string',
            'foto' => 'nullable|image|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $noTransaksi = 'BM-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $imageName = null;
            if ($request->has('image_base64') && !empty($request->image_base64)) {
                $imageData = $request->image_base64;
                $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $imageName = $noTransaksi . '.jpg';
                $imagePath = 'bukti_nota/' . $imageName;
                Storage::disk('public')->put($imagePath, base64_decode($imageData));
            }

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

            $namaMaterial = $request->nama_material;
            $warnaInput = $request->warna ?? 'Tidak Ada Warna';
            $jumlahMasuk = (float) $request->jumlah;

            $bahan = BahanBaku::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])
                ->whereRaw('LOWER(warna) = LOWER(?)', [$warnaInput])
                ->first();

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('bahan_baku', 'public');
            } elseif ($request->has('foto') && is_string($request->foto)) {
                $fotoData = $request->foto;
                if (preg_match('/^data:image\/\w+;base64,/', $fotoData)) {
                    $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
                    $fotoData = str_replace(' ', '+', $fotoData);
                    $fotoPath = 'bahan_baku/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($fotoPath, base64_decode($fotoData));
                }
            }

            if (!$bahan) {
                $existingMaster = BahanBaku::where('nama_material', $namaMaterial)->first();

                if ($existingMaster) {
                    $kodeMaterial = $existingMaster->kode_material;
                } else {
                    $prefix = \App\Models\Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
                    $prefix = trim($prefix);
                    $maxNum = 0;
                    $allCodes = BahanBaku::lockForUpdate()->where('kode_material', 'like', $prefix . '%')->pluck('kode_material');

                    foreach ($allCodes as $code) {
                        $numericPart = preg_replace('/[^0-9]/', '', substr($code, strlen($prefix)));
                        if ($numericPart !== '' && is_numeric($numericPart)) {
                            $num = (int) $numericPart;
                            if ($num > $maxNum) {
                                $maxNum = $num;
                            }
                        }
                    }
                    $next = $maxNum + 1;

                    do {
                        $kodeMaterial = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
                        $next++;
                    } while (BahanBaku::where('kode_material', $kodeMaterial)->exists());
                }

                $bahan = BahanBaku::create([
                    'kode_material' => $kodeMaterial,
                    'nama_material' => $namaMaterial,
                    'warna'         => $warnaInput,
                    'stok'          => 0,
                    'satuan'        => $request->satuan,
                    'kategori'      => $request->kategori ?? 'Bahan Utama',
                    'harga_satuan'  => $request->harga_satuan ?? 0,
                    'is_inventory'  => true,
                    'foto'          => $fotoPath,
                ]);
            } else {
                if ($fotoPath) {
                    $bahan->foto = $fotoPath;
                    $bahan->save();
                }
            }

            $bahan->increment('stok', $jumlahMasuk);

            // FIFO: Distribusikan ke pengadaan PENDING
            $query = \Illuminate\Support\Facades\DB::table('pengadaans')
                ->join('bahan_bakus', 'bahan_bakus.id', '=', 'pengadaans.bahan_baku_id')
                ->whereRaw('LOWER(bahan_bakus.nama_material) = LOWER(?)', [$namaMaterial])
                ->where('pengadaans.status', 'PENDING')
                ->select('pengadaans.*')
                ->orderBy('pengadaans.created_at', 'asc');

            $warnaBersih = trim($warnaInput);
            $isWarnaKosong = empty($warnaBersih) || strtolower($warnaBersih) === 'tidak ada warna';
            if (!$isWarnaKosong) {
                $query->where(function ($q) use ($warnaBersih) {
                    $q->whereRaw('LOWER(pengadaans.warna) = LOWER(?)', [$warnaBersih])
                        ->orWhereNull('pengadaans.warna')
                        ->orWhere('pengadaans.warna', '')
                        ->orWhereRaw('LOWER(pengadaans.warna) = ?', ['tidak ada warna']);
                });
            }

            $antrianPengadaanRaw = $query->get();
            $antrianPengadaan = Pengadaan::whereIn('id', $antrianPengadaanRaw->pluck('id'))->get();

            $sisaDistribusi = $jumlahMasuk;
            foreach ($antrianPengadaan as $p) {
                if ($sisaDistribusi <= 0) break;
                $sisaPesanan = $p->jumlah - ($p->received ?? 0);
                $yangDiambil = min($sisaDistribusi, $sisaPesanan);
                $p->increment('received', $yangDiambil);
                $p->refresh();
                if ($p->received >= $p->jumlah) {
                    $p->update(['status' => 'DONE']);
                }
                $sisaDistribusi -= $yangDiambil;
            }

            if ($sisaDistribusi < $jumlahMasuk) {
                $jumlahTerdistribusi = $jumlahMasuk - $sisaDistribusi;
                BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->toDateString(),
                    'qty_masuk'     => $jumlahTerdistribusi,
                    'qty_sisa'      => $jumlahTerdistribusi,
                    'harga_beli'    => $request->harga_satuan ?? $bahan->harga_satuan,
                    'sumber_harga'  => 'Scan Nota',
                ]);
            }

            \App\Models\LogStok::create([
                'bahan_baku_id' => $bahan->id,
                'operator_id' => auth()->id(),
                'jenis_transaksi' => 'TAMBAH_STOK',
                'jumlah' => $jumlahMasuk,
                'keterangan' => 'Input Manual - ' . $namaMaterial . ' (' . $warnaInput . ')',
            ]);

            // Input manual: kirim 1 notifikasi per item (seperti saat ini)
            $admins = User::where('role', 'admin')->get();
            $operatorName = auth()->user()->name ?? 'Operator';
            $notif = new \App\Notifications\BahanMasukNotification($bahan, $jumlahMasuk, $operatorName, 'Input Manual');
            Notification::send($admins, $notif);

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
     * Process OCR from image using Groq API
     */
    private function processOCR(Request $request)
    {
        set_time_limit(120);

        try {
            $base64_final = $request->input('image_base64');
            if (!$base64_final) {
                return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan.'], 400);
            }

            // Simpan arsip
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
            } catch (\Exception $e) {}

            $groqApiKey = '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';

            $imageDataUrl = $base64_final;

            $prompt = 'Anda adalah asisten OCR untuk nota pembelian bahan baku kulit/leather. Ekstrak informasi dari gambar nota ini dengan format JSON PERSIS seperti ini: {"supplier": "Nama Toko","no_nota": "Nomor Nota","tanggal": "Tanggal","items": [{"nama": "Nama Barang","qty": jumlah_angka,"satuan": "satuan","harga": harga_per_satuan_angka}]} ATURAN: 1. Nama barang BAHASA INDONESIA 2. qty dan harga ANGKA 3. Satuan: Lembar, Kaki, Meter, Pcs, Kg, Roll 4. Hanya JSON valid, tidak ada teks lain';

            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $groqApiKey,
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.2-11b-vision-preview',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $prompt],
                                ['type' => 'image_url', 'image_url' => ['url' => $imageDataUrl]]
                            ]
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 2048,
                ]);

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorBody = $response->body();
                \Log::error("Groq API error: HTTP $statusCode - " . substr($errorBody, 0, 200));

                \Log::info("Mencoba Groq dengan model llama-3.2-90b-vision-preview...");
                $response2 = Http::timeout(120)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $groqApiKey,
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.2-90b-vision-preview',
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => [
                                    ['type' => 'text', 'text' => $prompt],
                                    ['type' => 'image_url', 'image_url' => ['url' => $imageDataUrl]]
                                ]
                            ]
                        ],
                        'temperature' => 0.1,
                        'max_tokens' => 2048,
                    ]);

                if ($response2->failed()) {
                    $statusCode2 = $response2->status();
                    \Log::error("Groq 90b juga gagal: HTTP $statusCode2");
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses gambar dengan AI. Coba lagi.'
                    ], 500);
                }

                $response = $response2;
            }

            $result = $response->json();
            $textResponse = $result['choices'][0]['message']['content'] ?? '';

            $textResponse = trim(preg_replace('/^```json\s*/i', '', $textResponse));
            $textResponse = trim(preg_replace('/```\s*$/', '', $textResponse));

            $parsedResult = json_decode($textResponse, true);

            if (!$parsedResult || !isset($parsedResult['items']) || count($parsedResult['items']) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membaca nota. Pastikan foto jelas.'
                ], 400);
            }

            session()->forget(['ocr_metadata', 'ocr_result']);

            session(['ocr_metadata' => [
                'no_nota' => $parsedResult['no_nota'] ?? '',
                'tanggal' => $parsedResult['tanggal'] ?? date('Y-m-d'),
                'supplier' => $parsedResult['supplier'] ?? '',
                'alamat' => '',
                'total_harga' => 0
            ]]);

            $items = [];
            foreach ($parsedResult['items'] as $item) {
                $items[] = [
                    'nama' => $item['nama'] ?? '',
                    'qty' => (int)($item['qty'] ?? 1),
                    'satuan' => $item['satuan'] ?? 'Pcs',
                    'harga' => (int)($item['harga'] ?? 0)
                ];
            }

            session(['ocr_result' => [
                'supplier' => $parsedResult['supplier'] ?? 'Supplier Tidak Terdeteksi',
                'metadata' => [
                    'no_nota' => $parsedResult['no_nota'] ?? '',
                    'tanggal' => $parsedResult['tanggal'] ?? date('Y-m-d'),
                    'alamat_kontak' => '',
                    'total_bayar' => 0
                ],
                'items' => $items,
                'image_url' => $publicPath ? asset('storage/' . $publicPath) : null
            ]]);

            return response()->json([
                'success' => true,
                'message' => 'Scan Berhasil!',
                'redirect_url' => route('operator.scan.result')
            ]);

        } catch (\Throwable $e) {
            \Log::error("Gemini System Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // CLEAN OCR TEXT
    // ==========================================
    private function cleanOcrText($text)
    {
        $replacements = [
            'Sa i' => 'Sapi', 'Sap i' => 'Sapi', 'Sap' => 'Sapi',
            'Kult' => 'Kulit', 'Kuit' => 'Kulit', 'Kuli' => 'Kulit',
            'Benan' => 'Benang', 'Benng' => 'Benang',
            'Iem' => 'Lem', 'Lm' => 'Lem',
            'Kambin' => 'Kambing', 'Kambng' => 'Kambing',
            'Ve efable' => 'Vegetable', 'Vegetble' => 'Vegetable',
            'Taned' => 'Tanned', 'Scra s' => 'Scrap', 'POI' => 'Polyester',
        ];

        $text = str_ireplace(array_keys($replacements), array_values($replacements), $text);
        $text = preg_replace('/[|_~^{}\\[\\]]/', '', $text);
        $text = preg_replace('/(?<!\d)(\d+)[\.\-\)]\s/', "\n$1. ", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\r\n|\r/', "\n", $text);

        return $text;
    }

    /**
     * Show OCR result page with parsed items
     */
    public function result()
    {
        $warnas = \App\Models\Warna::all();
        return view('operator.scan-result', compact('warnas'));
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
     * Simpan hasil OCR ke Database - 1 notifikasi bulk untuk semua item
     */
    public function save(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.nama' => 'required|string',
            'items.*.qty' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $operatorName = auth()->user()->name ?? 'Operator';
            $savedItems = [];

            // Kumpulkan semua item untuk notifikasi bulk
            $notifItems = [];

            foreach ($request->input('items') as $item) {
                $namaInput = trim($item['nama']);
                $qtyTotal  = (float) $item['qty'];
                $hargaBeli = (int) ($item['harga'] ?? 0);
                $satuan    = $item['satuan'] ?? 'Pcs';

                // Ambil data warna (array multidimensi dari form pecah warna)
                $warnaData = $item['warna'] ?? [];

                if (empty($warnaData) || !is_array($warnaData)) {
                    $warnaData = [['nama' => 'Tidak Ada Warna', 'qty' => $qtyTotal]];
                }

                $warnaItems = [];
                foreach ($warnaData as $wItem) {
                    $warnaNama = trim($wItem['nama'] ?? '');
                    $warnaQty  = (float) ($wItem['qty'] ?? 0);
                    if (empty($warnaNama) || $warnaQty <= 0) continue;
                    $warnaItems[] = ['nama' => $warnaNama, 'qty' => $warnaQty];
                }

                if (empty($warnaItems)) {
                    $warnaItems[] = ['nama' => 'Tidak Ada Warna', 'qty' => $qtyTotal];
                }

                foreach ($warnaItems as $warnaItem) {
                    $warnaNama = $warnaItem['nama'];
                    $warnaQty  = $warnaItem['qty'];

                    // Cari berdasarkan NAMA + WARNA (case-insensitive)
                    $barang = BahanBaku::whereRaw('LOWER(TRIM(nama_material)) = LOWER(TRIM(?))', [$namaInput])
                        ->whereRaw('LOWER(TRIM(warna)) = LOWER(TRIM(?))', [$warnaNama])
                        ->first();

                    if (!$barang) {
                        $existingMaster = BahanBaku::where('nama_material', $namaInput)->first();

                        if ($existingMaster) {
                            $kodeMaterial = $existingMaster->kode_material;
                        } else {
                            $prefix = \App\Models\Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
                            $prefix = trim($prefix);
                            $maxNum = 0;
                            $allCodes = BahanBaku::lockForUpdate()->where('kode_material', 'like', $prefix . '%')->pluck('kode_material');

                            foreach ($allCodes as $code) {
                                $numericPart = preg_replace('/[^0-9]/', '', substr($code, strlen($prefix)));
                                if ($numericPart !== '' && is_numeric($numericPart)) {
                                    $num = (int) $numericPart;
                                    if ($num > $maxNum) $maxNum = $num;
                                }
                            }
                            $next = $maxNum + 1;
                            do {
                                $kodeMaterial = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
                                $next++;
                            } while (BahanBaku::where('kode_material', $kodeMaterial)->exists());
                        }

                        $barang = BahanBaku::create([
                            'kode_material' => $kodeMaterial,
                            'nama_material' => $namaInput,
                            'warna'         => $warnaNama,
                            'stok'          => 0,
                            'satuan'        => $satuan,
                            'kategori'      => 'Bahan Utama',
                            'harga_satuan'  => $hargaBeli,
                            'is_inventory'  => true,
                        ]);
                    }

                    // Tambah stok per warna
                    $barang->increment('stok', $warnaQty);
                    if ($hargaBeli > 0) {
                        $barang->harga_satuan = $hargaBeli;
                        $barang->save();
                    }

                    // Catat log stok
                    \App\Models\LogStok::create([
                        'bahan_baku_id' => $barang->id,
                        'operator_id' => auth()->id(),
                        'jenis_transaksi' => 'TAMBAH_STOK',
                        'jumlah' => $warnaQty,
                        'keterangan' => 'Scan Nota - ' . $namaInput . ' (' . $warnaNama . ')',
                    ]);

                    // Buat stok layer
                    BahanStokLayer::create([
                        'bahan_baku_id' => $barang->id,
                        'tanggal_masuk' => now()->toDateString(),
                        'qty_masuk'     => $warnaQty,
                        'qty_sisa'      => $warnaQty,
                        'harga_beli'    => $hargaBeli,
                        'sumber_harga'  => 'Scan Nota',
                    ]);

                    // Kumpulkan untuk notifikasi bulk (bukan kirim per item)
                    $notifItems[] = [
                        'nama' => $namaInput,
                        'warna' => $warnaNama,
                        'qty' => $warnaQty,
                        'satuan' => $satuan,
                        'harga' => $hargaBeli,
                    ];

                    $savedItems[] = "$namaInput ($warnaNama: $warnaQty)";
                }
            }

            // Kirim 1 notifikasi bulk untuk semua item
            if (!empty($notifItems)) {
                $ocrResult = session('ocr_result', []);
                $supplier = $ocrResult['supplier'] ?? 'Supplier Tidak Terdeteksi';
                $noNota = $ocrResult['metadata']['no_nota'] ?? '';
                $fotoNotaUrl = $ocrResult['image_url'] ?? null;

                $admins = User::where('role', 'admin')->get();
                $bulkNotif = new ScanNotaBulkNotification(
                    $operatorName,
                    $supplier,
                    $noNota,
                    $notifItems,
                    0,
                    $fotoNotaUrl
                );
                Notification::send($admins, $bulkNotif);
            }

            session()->forget(['ocr_metadata', 'ocr_result']);
            DB::commit();

            return redirect()->route('operator.bahan.index')->with('success', 'Data tersimpan: ' . implode(', ', $savedItems));

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Scan Save Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display detail transaksi for admin
     */
    public function show($id)
    {
        $bahanMasuk = BahanMasuk::with(['operator', 'verifiedBy'])->findOrFail($id);
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
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $bahanMasuk->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'jumlah' => $request->jumlah,
                'harga_satuan' => $request->harga_satuan,
                'notes' => $request->notes,
            ]);

            $bahanBaku = \App\Models\BahanBaku::firstOrCreate(
                ['nama' => $bahanMasuk->nama_material],
                [
                    'kategori_id' => optional(\App\Models\Kategori::where('nama', $bahanMasuk->kategori)->first())->id,
                    'satuan_id' => optional(\App\Models\Satuan::where('nama', $bahanMasuk->satuan)->first())->id,
                    'stok' => 0,
                ]
            );

            $bahanBaku->stok += $request->jumlah;
            $bahanBaku->save();

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
        $request->validate(['notes' => 'required|string']);
        $bahanMasuk = BahanMasuk::findOrFail($id);
        if (auth()->user()->role !== 'admin') abort(403);

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
        if (auth()->user()->role !== 'admin') abort(403);
        $bahanMasukList = BahanMasuk::with('operator')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.scan-pending', compact('bahanMasukList'));
    }
}