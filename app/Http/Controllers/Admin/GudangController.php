<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProdukJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::all()->pluck('value', 'key');
        return view('admin.laporan.pengaturan.gudang', compact('pengaturan'));
    }

    public function store(Request $request)
    {
        $pengaturanGudang = $request->except('_token');
        $marginLama = Pengaturan::where('key', 'margin_keuntungan')->first()->value ?? 0;
        $marginBaru = $request->margin_keuntungan;

        DB::beginTransaction();
        try {
            foreach ($pengaturanGudang as $key => $value) {
                Pengaturan::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Jika margin keuntungan berubah, update semua harga jual produk jadi
            if ($marginLama != $marginBaru) {
                $produks = ProdukJadi::all();
                foreach ($produks as $produk) {
                    // Asumsi: ada properti harga_produksi pada model ProdukJadi
                    $hargaProduksi = $produk->harga_produksi;
                    $hargaJualBaru = $hargaProduksi + ($hargaProduksi * ($marginBaru / 100));
                    $produk->update(['harga_jual' => $hargaJualBaru]);
                }
            }

            DB::commit();
            return back()->with('success', 'Pengaturan gudang berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}