<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukKeluar;
use App\Models\ProdukJadi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ProdukKeluarController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi pengurangan stok.
     */
    public function index()
    {
        // Memuat semua transaksi Pengurangan Stok beserta detail produknya
        $transaksis = ProdukKeluar::with('produk_jadi')
                                  ->latest()
                                  ->paginate(15);

        return view('admin.produk_keluar.index', compact('transaksis'));
    }

    /**
     * Menampilkan formulir untuk mencatat Pengurangan Stok Produk Jadi.
     */
    public function create()
    {
        // Ambil semua produk jadi
        $products = ProdukJadi::orderBy('nama_produk')->get();
        
        // Daftar alasan (sesuai Enum di migrasi)
        $alasan = ['Penjualan', 'Rusak', 'Sample', 'Lain-lain'];

        return view('admin.produk_keluar.create', compact('products', 'alasan'));
    }

    /**
     * Memproses dan menyimpan transaksi pengurangan stok.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'produk_jadi_id'    => 'required|exists:produk_jadis,id',
            'jumlah_keluar'     => 'required|integer|min:1',
            'alasan'            => 'required|in:Penjualan,Rusak,Sample,Lain-lain',
            'catatan'           => 'nullable|string|max:255',
            'tanggal_keluar'    => 'required|date',
        ]);
        
        // --- LOGIKA UTAMA: PENGURANGAN STOK (TRANSAKSI DATABASE) ---
        DB::beginTransaction();

        try {
            // 2. Ambil Data Produk dengan LOCK (Penting untuk mencegah Race Condition)
            // Kita ambil di dalam transaksi agar data stok benar-benar fresh dan terkunci
            $product = ProdukJadi::lockForUpdate()->findOrFail($validatedData['produk_jadi_id']);

            // 3. Cek Ketersediaan Stok
            if ($product->stok < $validatedData['jumlah_keluar']) {
                // Jika stok tidak cukup, batalkan transaksi dan tampilkan error
                throw ValidationException::withMessages([
                    'jumlah_keluar' => 'Stok tidak cukup! Stok tersedia: ' . $product->stok . ' Unit.'
                ]);
            }

            // 4. Update Stok di Tabel Produk Jadi
            $product->decrement('stok', $validatedData['jumlah_keluar']);
            // Catatan: Jika Anda tidak menggunakan lockForUpdate() pada query findOrFail di atas, 
            // Anda bisa menggunakan sintaks ini:
            // $product->stok -= $validatedData['jumlah_keluar'];
            // $product->save();

            // 5. Catat Transaksi di Tabel Produk Keluar
            ProdukKeluar::create($validatedData);

            DB::commit(); // Konfirmasi perubahan ke database

            // FIX: Redirect back agar admin tetap di halaman Produk Jadi setelah mengurangi stok manual
            return back()->with('success', "Berhasil mengurangi stok {$product->nama_produk} sebanyak {$validatedData['jumlah_keluar']} unit ({$validatedData['alasan']}).");

        } catch (ValidationException $e) {
            DB::rollBack(); // Batalkan semua jika ada error validasi
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error lain
            // Log::error($e->getMessage()); // Jika menggunakan facade Log
            return back()->with('error', 'Gagal memproses pengurangan stok. Silakan coba lagi.')->withInput();
        }
    }
}