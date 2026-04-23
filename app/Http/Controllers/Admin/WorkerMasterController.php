<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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

    public function index()
    {
        $operators = User::where('role', 'operator')
            ->withCount(['bahanBakus as total_input_bahan'])
            ->withSum(['produksis as total_produk_dibuat' => function($q) {
                $q->where('status', 'SELESAI');
            }], 'jumlah_produksi')
            ->latest()
            ->paginate(12);

        // Load statistik detail per operator
        foreach ($operators as $op) {
            $bahanTerbanyak = \DB::table('bahan_bakus')
                ->where('user_id', $op->id)
                ->select('nama_material', \DB::raw('COUNT(*) as total'))
                ->groupBy('nama_material')
                ->orderByDesc('total')
                ->first();
            $op->bahan_terbanyak = $bahanTerbanyak ? (object)[
                'nama' => $bahanTerbanyak->nama_material,
                'total' => $bahanTerbanyak->total,
                'satuan' => 'Lembar'
            ] : null;

            $produkTerbanyak = \DB::table('produksis')
                ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
                ->where('produksis.operator_id', $op->id)
                ->where('produksis.status', 'SELESAI')
                ->select('produk_jadis.nama_produk as nama', \DB::raw('SUM(produksis.jumlah_produksi) as total'))
                ->groupBy('produk_jadis.nama_produk')
                ->orderByDesc('total')
                ->first();
            $op->produk_terbanyak = $produkTerbanyak ? (object)[
                'nama' => $produkTerbanyak->nama,
                'total' => $produkTerbanyak->total,
                'satuan' => 'Pcs'
            ] : null;
        }

        return view('admin.pekerja-master.index', compact('operators'));
    }

    public function store(Request $request)
    {
        // PERBAIKAN: Gunakan 'name' (sesuai input di form)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Simpan operator (status awal: belum diverifikasi)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'operator',
            'sub_role' => $request->sub_role ?? 'pekerja',
            'jabatan' => $request->jabatan ?? 'Staff Produksi',
            'email_verified_at' => null,
        ]);

        // Buat link verifikasi - gunakan base URL operator (ngrok) agar bisa dibuka dari HP
        $link = $this->operatorBaseUrl() . '/operator/verify/' . $user->id;

        // Kirim Email
        try {
            $details = ['name' => $user->name, 'link' => $link];
            Mail::send('emails.invite_operator', $details, function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Verifikasi Akun Operator - Berliano Leather');
            });
            $msg = 'Akun berhasil dibuat! Silakan cek email untuk verifikasi.';
        } catch (\Exception $e) {
            $msg = 'Akun dibuat, tapi email GAGAL dikirim. Cek sandi aplikasi Google Anda.';
        }

        return redirect()->back()->with('success', $msg);
    }

    // FUNGSI VERIFIKASI (Saat tombol di email diklik)
    public function verifyOperator($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        // Tidak ada route khusus /operator/login di aplikasi ini.
        // Login menggunakan route bawaan (/login), lalu middleware role akan mengarahkan ke dashboard operator.
        return redirect('/login')->with('success', 'Verifikasi berhasil! Akun Anda sekarang aktif dan bisa menerima perintah produksi. Silakan login.');
    }

    // FUNGSI HAPUS (INI YANG TADI HILANG)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Mencegah penghapusan admin melalui route ini
        if($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menghapus Admin!');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun operator berhasil dihapus.');
    }
}