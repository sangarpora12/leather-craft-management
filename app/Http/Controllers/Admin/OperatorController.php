<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class OperatorController extends Controller
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

        // Fallback LAN
        $ip = gethostbyname(gethostname());
        return 'http://' . $ip . ':8000';
    }
    /**
     * Menampilkan daftar operator.
     */
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
            // Bahan terbanyak yang diinput operator
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

            // Produk terbanyak yang dikerjakan operator (status SELESAI)
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

        return view('admin.operator.index', compact('operators'));
    }

    /**
     * Menampilkan form tambah operator.
     */
    public function create()
    {
        return view('admin.operator.create');
    }

    /**
     * Menyimpan data operator baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'jabatan' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'jabatan' => $validated['jabatan'],
            'password' => Hash::make($validated['password']),
            'role' => 'operator',
            'foto' => $request->hasFile('foto') ? $request->file('foto')->store('users', 'public') : null,
        ]);

        // Buat link verifikasi rahasia
        $link = URL::signedRoute('operator.verify', ['id' => $user->id], null, false);
        $link = $this->operatorBaseUrl() . $link;

        // Kirim Email
        try {
            $details = ['name' => $user->name, 'link' => $link];
            Mail::send('emails.invite_operator', $details, function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Verifikasi Akun Operator - Berliano Leather');
            });
            $msg = 'Akun Operator berhasil dibuat! Silakan cek email untuk verifikasi.';
        } catch (\Exception $e) {
            $msg = 'Akun dibuat, tapi email GAGAL dikirim. Cek konfigurasi email.';
        }

        return redirect()->route('admin.operator.index')->with('success', $msg);
    }

    /**
     * Menampilkan form edit operator.
     */
    public function edit($id)
    {
        $operator = User::where('role', 'operator')->findOrFail($id);
        return view('admin.operator.edit', compact('operator'));
    }

    /**
     * Memperbarui data operator.
     */
    public function update(Request $request, $id)
    {
        $operator = User::where('role', 'operator')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'sub_role' => 'required|in:kepala_gudang,pekerja',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $operator->name = $validated['name'];
        $operator->email = $validated['email'];
        $operator->sub_role = $validated['sub_role'];

        if ($request->filled('password')) {
            $operator->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($operator->foto) {
                Storage::disk('public')->delete($operator->foto);
            }
            $operator->foto = $request->file('foto')->store('users', 'public');
        }

        $operator->save();

        return redirect()->route('admin.operator.index')->with('success', 'Data Operator berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $operator = User::where('role', 'operator')->findOrFail($id);
        $operator->delete();
        return redirect()->route('admin.operator.index')->with('success', 'Akun Operator berhasil dihapus.');
    }

    /**
     * Mengaktifkan atau menonaktifkan akun operator.
     */
    public function toggleStatus($id)
    {
        $operator = User::where('role', 'operator')->findOrFail($id);

        // Jika request meminta toggle sub_role
        if (request()->has('toggle_sub_role')) {
            $operator->sub_role = $operator->sub_role === 'kepala_gudang' ? 'pekerja' : 'kepala_gudang';
            $operator->save();
            $label = $operator->sub_role === 'kepala_gudang' ? 'Kepala Gudang' : 'Pekerja';
            return redirect()->back()->with('success', "Role {$operator->name} berhasil diubah menjadi {$label}.");
        }

        // Toggle status aktif/nonaktif
        $operator->is_active = !$operator->is_active;
        $operator->save();

        $status = $operator->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun operator berhasil $status.");
    }

    /**
     * Kirim ulang email verifikasi.
     */
    public function resendVerification($id)
    {
        $operator = User::where('role', 'operator')->findOrFail($id);

        if ($operator->email_verified_at) {
            return redirect()->back()->with('error', 'Akun sudah diverifikasi.');
        }

        // Buat link verifikasi rahasia
        $link = URL::signedRoute('operator.verify', ['id' => $operator->id], null, false);
        $link = $this->operatorBaseUrl() . $link;

        // Kirim Email
        try {
            $details = ['name' => $operator->name, 'link' => $link];
            Mail::send('emails.invite_operator', $details, function($message) use ($operator) {
                $message->to($operator->email)
                        ->subject('Verifikasi Akun Operator - Berliano Leather');
            });
            $msg = 'Email verifikasi berhasil dikirim ulang.';
        } catch (\Exception $e) {
            $msg = 'Gagal mengirim email. Cek konfigurasi email.';
        }

        return redirect()->back()->with('success', $msg);
    }
}