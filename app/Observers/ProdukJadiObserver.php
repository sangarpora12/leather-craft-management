<?php

namespace App\Observers;

use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use App\Models\Bom;

class ProdukJadiObserver
{
    /**
     * Hitung ulang harga untuk semua produk dengan tipe_harga = otomatis
     */
    public function updatedBahanBaku(BahanBaku $bahanBaku)
    {
        // Hanya recalculate jika harga_satuan berubah
        if ($bahanBaku->wasChanged('harga_satuan')) {
            $this->recalculateOtomatisProducts();
        }
    }

    /**
     * Recalculate saat pengaturan PPN/Margin berubah
     */
    public function updatedPengaturan(Pengaturan $pengaturan)
    {
        if (in_array($pengaturan->nama_pengaturan, ['ppn', 'margin_default', 'pembulatan_harga'])) {
            $this->recalculateOtomatisProducts();
        }
    }

    /**
     * Recalculate semua produk dengan harga otomatis
     */
    private function recalculateOtomatisProducts()
    {
        $products = ProdukJadi::where('tipe_harga', 'otomatis')->get();
        
        foreach ($products as $product) {
            $hargaBaru = $this->hitungHargaOtomatis($product->id);
            $product->update(['harga_jual' => $hargaBaru]);
        }
    }

    /**
     * Helper untuk hitung harga otomatis
     */
    public function hitungHargaOtomatis($produkJadiId)
    {
        // Ambil total HPP dari BOM
        $totalHpp = Bom::where('id_produk', $produkJadiId)
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->selectRaw('SUM(bom.jumlah_kebutuhan * bahan_bakus.harga_satuan) as total')
            ->value('total') ?? 0;
        
        // Ambil pengaturan keuangan
        $margin = (float) Pengaturan::where('nama_pengaturan', 'margin_default')->value('nilai') ?? 30;
        $ppn = (float) Pengaturan::where('nama_pengaturan', 'ppn')->value('nilai') ?? 11;
        $pembulatan = Pengaturan::where('nama_pengaturan', 'pembulatan_harga')->value('nilai') ?? '1000';
        
        // Hitung harga
        $keuntungan = $totalHpp * ($margin / 100);
        $hargaSebelumPajak = $totalHpp + $keuntungan;
        $nominalPpn = $hargaSebelumPajak * ($ppn / 100);
        $hargaAkhir = $hargaSebelumPajak + $nominalPpn;
        
        // Pembulatan
        if ($pembulatan !== 'none') {
            $hargaAkhir = round($hargaAkhir / (int)$pembulatan) * (int)$pembulatan;
        }
        
        return round($hargaAkhir);
    }
}