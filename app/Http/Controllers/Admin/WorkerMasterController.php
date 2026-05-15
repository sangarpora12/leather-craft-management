<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // ✅ Tambahan facade Schema
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkerMasterController extends Controller
{
    private function operatorBaseUrl(): string
    {
        // Base URL untuk operator.
        // Prioritas:
        // 1) OPERATOR_URL (bila diset)
        // 2) APP_URL (bila sudah bukan localhost)
        // 3) fallback: IP LAN + port artisan serve (biar bisa dibuka dari HP satu WiFi)
        $base = config('app.operator_url') ?: config('app.url');
        $base = rtrim((string) $base, '/');

        if ($base && !str_contains($base, 'localhost') && !str_contains($base, '127.0.0.1')) {
            return $base;
        }

        $ip = gethostbyname(gethostname());
        return 'http://' . $ip . ':8000';
    }

    /**
     * Menampilkan daftar operator dengan statistik detail.
     */
    public function index()
    {
        try {
            $operators = User::where('role', 'operator')
                ->withCount(['bahanBakus as total_input_bahan'])
                ->withSum(['produksis as total_produk_dibuat' => function($q) {
                    $q->where('status', 'SELESAI');
                }], 'jumlah_produksi')
                ->latest()
                ->paginate(12);

            // Load statistik detail per operator
            foreach ($operators as $op) {
                // ✅ Query bahan_terbanyak dengan filter transaksi masuk
// Menampilkan bahan yang paling banyak di-input
$bahanTerbanyak = DB::table('log_stok')
->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
->where('log_stok.operator_id', $op->id) // <--- TAMBAHKAN BARIS INI
->where(function($q) {
    // Biar lebih aman, kita deteksi semua jenis transaksi masuk
    $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%');
})
->select('bahan_bakus.nama_material', 'bahan_bakus.satuan', DB::raw('SUM(log_stok.jumlah) as total'))
->groupBy('bahan_bakus.nama_material', 'bahan_bakus.satuan')
->orderByDesc('total')
->first();

$op->bahan_terbanyak = $bahanTerbanyak ? (object)[
'nama' => $bahanTerbanyak->nama_material,
'total' => (int)$bahanTerbanyak->total,
'satuan' => $bahanTerbanyak->satuan ?? 'Lembar' // Mengambil satuan asli dari database jika ada
] : null;

                // ✅ Query produk_terbanyak - berdasarkan operator_id dan status SELESAI
                $produkTerbanyak = DB::table('produksis')
                    ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
                    ->where('produksis.operator_id', $op->id)
                    ->where('produksis.status', 'SELESAI')
                    ->select('produk_jadis.nama_produk as nama', DB::raw('SUM(produksis.jumlah_produksi) as total'))
                    ->groupBy('produk_jadis.nama_produk')
                    ->orderByDesc('total')
                    ->first();

                $op->produk_terbanyak = $produkTerbanyak ? (object)[
                    'nama' => $produkTerbanyak->nama,
                    'total' => (int)$produkTerbanyak->total,
                    'satuan' => 'Pcs'
                ] : null;
            }

            return view('admin.pekerja-master.index', compact('operators'));
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    /**
     * Menampilkan detail kinerja operator.
     */
    /**
     * Menampilkan detail kinerja operator.
     */
    public function kinerja(Request $request, $id)
    {
        try {
            $operator = User::where('role', 'operator')->findOrFail($id);

            // Ambil tanggal dari request
            $tanggal = $request->input('tanggal');

            // 1. Statistik produk yang diselesaikan BULAN INI oleh operator INI
            $produksiBulanIni = DB::table('produksis')
                ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
                ->where('produksis.operator_id', $id) // Filter Operator
                ->where(DB::raw('TRIM(produksis.status)'), 'Selesai')
                ->whereMonth('produksis.tanggal_selesai', now()->month)
                ->whereYear('produksis.tanggal_selesai', now()->year)
                ->select('produk_jadis.nama_produk', DB::raw('SUM(produksis.jumlah_selesai) as total'))
                ->groupBy('produk_jadis.nama_produk')
                ->orderByDesc('total')
                ->get();
            
            // Total produk selesai bulan ini
            $totalProdukBulanIni = $produksiBulanIni->sum('total');
            
            // 2. Statistik KESELURUHAN (Semua waktu) untuk operator INI
            $totalSemuaProduk = DB::table('produksis')
                ->where('operator_id', $id) // Filter Operator
                ->where(DB::raw('TRIM(status)'), 'Selesai')
                ->sum('jumlah_selesai');
            
            // 3. Total Bahan Diinput BULAN INI oleh operator INI
            $totalBahanDiinput = DB::table('log_stok')
                ->where('operator_id', $id) // Filter Operator KHUSUS INI
                ->whereMonth('created_at', now()->month) // Filter hanya bulan ini
                ->whereYear('created_at', now()->year)
                ->where(function($q) {
                    $q->where('jenis_transaksi', 'like', '%MASUK%')
                      ->orWhere('jenis_transaksi', 'like', '%TAMBAH%')
                      ->orWhere('jenis_transaksi', 'like', '%PENGADAAN%')
                      ->orWhere('jenis_transaksi', 'like', '%RESTOCK%')
                      ->orWhere('jenis_transaksi', 'like', '%INPUT%');
                })
                ->sum('jumlah');

            // --- TAMBAHAN UNTUK 10 PRODUK & BAHAN TERBARU --- //
            
            // 1. Ambil Produk Terbaru dengan Paginasi (Atau list) untuk operator INI
            $queryProduk = DB::table('produksis')
                ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
                ->where('produksis.operator_id', $id) // Filter Operator
                ->where(DB::raw('TRIM(produksis.status)'), 'Selesai');

            // Terapkan filter tanggal jika ada
            if ($tanggal) {
                $queryProduk->whereDate('produksis.tanggal_selesai', $tanggal);
            }

            $produkSelesaiTerbaru = $queryProduk
            ->select(
                'produk_jadis.nama_produk', 
                'produk_jadis.kode_produk', 
                DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'), 
                DB::raw('DATE(produksis.tanggal_selesai) as created_at')
            )
            ->groupBy('produk_jadis.kode_produk', 'produk_jadis.nama_produk', DB::raw('DATE(produksis.tanggal_selesai)'))
            ->orderByDesc(DB::raw('DATE(produksis.tanggal_selesai)'))
            ->get();
            // 2. Ambil Bahan Masuk Terbaru oleh operator INI
            $queryBahan = DB::table('log_stok')
                ->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
                ->where('log_stok.operator_id', $id) // Filter Operator KHUSUS INI
                ->where(function($q) {
                    $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
                        ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
                        ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
                        ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
                        ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%');
                });

            // Terapkan filter tanggal jika ada
            if ($tanggal) {
                $queryBahan->whereDate('log_stok.created_at', $tanggal);
            }

            $bahanTerbaru = $queryBahan
            ->select(
                'bahan_bakus.nama_material', 
                'bahan_bakus.kode_material as kode_bahan', 
                DB::raw('SUM(log_stok.jumlah) as jumlah'), 
                DB::raw('DATE(log_stok.created_at) as created_at')
            )
            ->groupBy('bahan_bakus.kode_material', 'bahan_bakus.nama_material', DB::raw('DATE(log_stok.created_at)'))
            ->orderByDesc(DB::raw('DATE(log_stok.created_at)'))
            ->get();
            return view('admin.pekerja-master.kinerja', compact(
                'operator',
                'produksiBulanIni',
                'totalProdukBulanIni',
                'totalSemuaProduk',
                'totalBahanDiinput',
                'produkSelesaiTerbaru',
                'bahanTerbaru'
            ));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Data Operator tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@kinerja: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat kinerja: ' . $e->getMessage());
        }
    }
    /**
     * Menyimpan data operator baru.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'sub_role' => 'nullable|in:pekerja,kepala_gudang',
                'jabatan' => 'nullable|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Simpan operator (status awal: belum diverifikasi)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'operator',
                'sub_role' => $validated['sub_role'] ?? 'pekerja',
                'jabatan' => $validated['jabatan'] ?? 'Staff Produksi',
                'email_verified_at' => null,
                'is_active' => false, // ✅ Aktifkan setelah verifikasi email
                'foto_profil' => $request->hasFile('foto') ? $request->file('foto')->store('users', 'public') : null,
            ]);

            // Buat link verifikasi - langsung update database via PHP murni (paling simpel)
            $link = $this->operatorBaseUrl() . '/verify-operator.php?id=' . $user->id;

            // Kirim Email
            try {
                $details = ['name' => $user->name, 'link' => $link];
                Mail::send('emails.invite_operator', $details, function($message) use ($validated) {
                    $message->to($validated['email'])
                            ->subject('Verifikasi Akun Operator - Berliano Leather');
                });
                
                if (count(Mail::failures()) > 0) {
                    $msg = 'Akun dibuat, tapi email ke ' . $validated['email'] . ' GAGAL dikirim. Email mungkin tidak aktif atau alamat salah.';
                    Log::error('Gagal mengirim email verifikasi ke: ' . $validated['email'] . ' - Mail failures: ' . json_encode(Mail::failures()));
                } else {
                    $msg = 'Akun berhasil dibuat! Silakan cek email ' . $validated['email'] . ' untuk verifikasi.';
                    Log::info('Email verifikasi berhasil dikirim ke: ' . $validated['email']);
                }
            } catch (\Exception $e) {
                $msg = 'Akun dibuat, tapi verifikasi GAGAL dikirim ke ' . $validated['email'] . '. Error: ' . $e->getMessage();
                Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Gagal membuat akun. ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit operator.
     */
    public function edit($id)
    {
        try {
            $operator = User::where('role', 'operator')->findOrFail($id);
            return view('admin.operator.edit', compact('operator'));
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Data operator tidak ditemukan.');
        }
    }

    /**
     * Memperbarui data operator.
     */
    public function update(Request $request, $id)
    {
        try {
            $operator = User::where('role', 'operator')->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'jabatan' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:6|confirmed',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'sub_role' => 'nullable|in:pekerja,kepala_gudang',
            ]);

            // Update data dasar
            $operator->name = $validated['name'];
            $operator->email = $validated['email'];
            $operator->jabatan = $validated['jabatan'];
            $operator->sub_role = $validated['sub_role'] ?? $operator->sub_role;

            // Update password jika diisi
            if ($request->filled('password')) {
                $operator->password = Hash::make($validated['password']);
            }

            // Update foto jika ada
            if ($request->hasFile('foto')) {
                // ✅ Hapus foto lama jika ada
                if ($operator->foto_profil && Storage::disk('public')->exists($operator->foto_profil)) {
                    Storage::disk('public')->delete($operator->foto_profil);
                }
                $operator->foto_profil = $request->file('foto')->store('users', 'public');
            }

            $operator->save();
            Log::info('Data operator berhasil diperbarui: ' . $operator->id);

            return redirect()->route('admin.pekerja-master.index')
                ->with('success', 'Data Operator berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Gagal memperbarui data. ' . $e->getMessage());
        }
    }

    /**
     * Menghapus operator.
     */
    public function destroy($id)
    {
        try {
            $operator = User::findOrFail($id);
            
            // Mencegah penghapusan admin melalui route ini
            if ($operator->role === 'admin') {
                return back()->with('error', 'Tidak bisa menghapus Admin!');
            }

            // ✅ Hapus foto jika ada
            if ($operator->foto_profil && Storage::disk('public')->exists($operator->foto_profil)) {
                Storage::disk('public')->delete($operator->foto_profil);
            }

            $operator->delete();
            Log::info('Operator berhasil dihapus: ' . $operator->id);

            return redirect()->back()->with('success', 'Akun operator berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus operator.');
        }
    }

    /**
     * Mengaktifkan atau menonaktifkan akun operator / mengubah sub_role.
     */
    public function toggleStatus($id)
    {
        try {
            $operator = User::where('role', 'operator')->findOrFail($id);

            // Jika request meminta toggle sub_role
            if (request()->has('toggle_sub_role')) {
                $operator->sub_role = $operator->sub_role === 'kepala_gudang' ? 'pekerja' : 'kepala_gudang';
                $operator->save();
                $label = $operator->sub_role === 'kepala_gudang' ? 'Kepala Gudang' : 'Pekerja';
                Log::info("Role {$operator->name} berhasil diubah menjadi {$label}");
                return redirect()->back()->with('success', "Role {$operator->name} berhasil diubah menjadi {$label}.");
            }

            // Toggle status aktif/nonaktif
            $operator->is_active = !$operator->is_active;
            $operator->save();

            $status = $operator->is_active ? 'diaktifkan' : 'dinonaktifkan';
            Log::info("Akun operator {$operator->name} berhasil $status");

            return redirect()->back()->with('success', "Akun operator berhasil $status.");
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@toggleStatus: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status operator.');
        }
    }

    /**
     * Fungsi verifikasi (Saat tombol di email diklik).
     */
    public function verifyOperator($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Verifikasi email dan aktifkan akun
            $user->email_verified_at = now();
            $user->is_active = true;
            $user->save();

            Log::info("Operator {$user->name} berhasil diverifikasi");

            return redirect('/login')->with('success', 'Verifikasi berhasil! Akun Anda sekarang aktif dan bisa menerima perintah produksi. Silakan login.');
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@verifyOperator: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Verifikasi gagal. Silakan coba lagi atau hubungi admin.');
        }
    }

    /**
     * Kirim ulang email verifikasi.
     */
    public function resendVerification($id)
    {
        try {
            $operator = User::where('role', 'operator')->findOrFail($id);

            if ($operator->email_verified_at) {
                return redirect()->back()->with('error', 'Akun sudah diverifikasi.');
            }

            // Buat link verifikasi - langsung update database via PHP murni (paling simpel)
            $link = $this->operatorBaseUrl() . '/verify-operator.php?id=' . $operator->id;

            // Kirim Email
            try {
                $details = ['name' => $operator->name, 'link' => $link];
                Mail::send('emails.invite_operator', $details, function($message) use ($operator) {
                    $message->to($operator->email)
                            ->subject('Verifikasi Akun Operator - Berliano Leather');
                });
                $msg = 'Email verifikasi berhasil dikirim ulang.';
                Log::info('Email verifikasi dikirim ulang ke: ' . $operator->email);
            } catch (\Exception $e) {
                $msg = 'Gagal mengirim email. Cek konfigurasi email.';
                Log::error('Gagal mengirim ulang email verifikasi: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('Error di WorkerMasterController@resendVerification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Operator tidak ditemukan.');
        }
    }
}