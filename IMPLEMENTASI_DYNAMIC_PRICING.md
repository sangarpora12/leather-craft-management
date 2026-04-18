# Implementasi Dynamic Pricing - Berliano Leather

## ✅ Yang Sudah Selesai:

1. **Migration kolom `tipe_harga`** - Sudah ditambahkan ke tabel `produk_jadis`
   - Nilai default: 'manual'
   - Pilihan: 'manual' atau 'otomatis'

## 📋 Langkah Selanjutnya (Manual Implementation):

### 1. Update Model ProdukJadi
Tambahkan `tipe_harga` ke fillable di `app/Models/ProdukJadi.php`:
```php
protected $fillable = [
    // ... field yang sudah ada
    'tipe_harga',
];
```

### 2. Update Form Produk (Create/Edit)
Edit file `resources/views/admin/produksi/create.blade.php` dan `edit.blade.php`:

Tambahkan toggle switch sebelum input harga_jual:
```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Harga</label>
    <div class="flex gap-4">
        <label class="flex items-center">
            <input type="radio" name="tipe_harga" value="otomatis" class="mr-2" onchange="toggleHargaField()">
            <span>🔘 Harga Otomatis (Markup dari BOM & Pengaturan)</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="tipe_harga" value="manual" class="mr-2" checked onchange="toggleHargaField()">
            <span>🔘 Harga Manual (Custom)</span>
        </label>
    </div>
    <p id="bantuan-harga" class="text-xs text-gray-500 mt-2 hidden">
        "Harga akan dihitung otomatis berdasarkan modal bahan baku + Margin + PPN saat produk disimpan."
    </p>
</div>

<div id="harga-jual-container">
    <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
    <input type="number" name="harga_jual" id="harga_jual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
</div>

<script>
function toggleHargaField() {
    const tipeOtomatis = document.querySelector('input[value="otomatis"]').checked;
    const hargaInput = document.getElementById('harga_jual');
    const bantuanText = document.getElementById('bantuan-harga');
    
    if (tipeOtomatis) {
        hargaInput.disabled = true;
        hargaInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        bantuanText.classList.remove('hidden');
    } else {
        hargaInput.disabled = false;
        hargaInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        bantuanText.classList.add('hidden');
    }
}
</script>
```

### 3. Update Controller ProdukJadi
Edit `app/Http/Controllers/ProdukJadiController.php`:

Tambahkan method helper untuk hitung harga otomatis:
```php
private function hitungHargaOtomatis($produkJadiId)
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
```

Update method `store` dan `update`:
```php
public function store(Request $request)
{
    // ... validasi dan logic yang sudah ada
    
    $data = $request->all();
    
    if ($request->tipe_harga === 'otomatis') {
        // Harga akan dihitung setelah produk dibuat
        $produk = ProdukJadi::create($data);
        $data['harga_jual'] = $this->hitungHargaOtomatis($produk->id);
        $produk->update(['harga_jual' => $data['harga_jual']]);
    } else {
        $produk = ProdukJadi::create($data);
    }
    
    return redirect()->route('admin.produk-jadi.index')->with('success', 'Produk berhasil disimpan!');
}

public function update(Request $request, $id)
{
    // ... logic yang sudah ada
    
    $data = $request->all();
    
    if ($request->tipe_harga === 'otomatis') {
        $data['harga_jual'] = $this->hitungHargaOtomatis($id);
    }
    
    $produk->update($data);
    
    return redirect()->route('admin.produk-jadi.index')->with('success', 'Produk berhasil diupdate!');
}
```

### 4. Buat Observer untuk Auto Recalculate
Buat file `app/Observers/ProdukJadiObserver.php`:
```php
<?php

namespace App\Observers;

use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Pengaturan;

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
    private function hitungHargaOtomatis($produkJadiId)
    {
        // Copy logic dari controller
        $totalHpp = \App\Models\Bom::where('id_produk', $produkJadiId)
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->selectRaw('SUM(bom.jumlah_kebutuhan * bahan_bakus.harga_satuan) as total')
            ->value('total') ?? 0;
        
        $margin = (float) Pengaturan::where('nama_pengaturan', 'margin_default')->value('nilai') ?? 30;
        $ppn = (float) Pengaturan::where('nama_pengaturan', 'ppn')->value('nilai') ?? 11;
        $pembulatan = Pengaturan::where('nama_pengaturan', 'pembulatan_harga')->value('nilai') ?? '1000';
        
        $keuntungan = $totalHpp * ($margin / 100);
        $hargaSebelumPajak = $totalHpp + $keuntungan;
        $nominalPpn = $hargaSebelumPajak * ($ppn / 100);
        $hargaAkhir = $hargaSebelumPajak + $nominalPpn;
        
        if ($pembulatan !== 'none') {
            $hargaAkhir = round($hargaAkhir / (int)$pembulatan) * (int)$pembulatan;
        }
        
        return round($hargaAkhir);
    }
}
```

### 5. Register Observer
Edit `app/Providers/AppServiceProvider.php`:
```php
public function boot(): void
{
    \App\Models\ProdukJadi::observe(\App\Observers\ProdukJadiObserver::class);
    \App\Models\BahanBaku::observe(\App\Observers\ProdukJadiObserver::class);
    \App\Models\Pengaturan::observe(\App\Observers\ProdukJadiObserver::class);
}
```

## 🎯 Cara Testing:

1. **Test Harga Manual**:
   - Buat produk baru
   - Pilih "Harga Manual"
   - Input harga bebas
   - Simpan → harga tersimpan sesuai input

2. **Test Harga Otomatis**:
   - Buat produk baru
   - Tambahkan BOM (bahan-bahan)
   - Pilih "Harga Otomatis"
   - Simpan → harga dihitung otomatis
   - Cek harga = HPP + Margin + PPN (dibulatkan)

3. **Test Auto Recalculate**:
   - Buat produk dengan "Harga Otomatis"
   - Ubah harga salah satu bahan baku di BOM
   - Refresh halaman produk → harga otomatis berubah
   - Atau ubah PPN/Margin di pengaturan keuangan
   - Harga produk otomatis ikut berubah

## 📊 Formula Perhitungan:

```
HPP (Harga Pokok Produksi) = Σ (Jumlah Kebutuhan × Harga Satuan Bahan)
Keuntungan = HPP × (Margin% / 100)
Harga Sebelum Pajak = HPP + Keuntungan
PPN = Harga Sebelum Pajak × (PPN% / 100)
Harga Jual Akhir = Harga Sebelum Pajak + PPN
Pembulatan = round(Harga / Pembulatan) × Pembulatan
```

## ✅ Checklist Implementasi:

- [x] Migration kolom tipe_harga
- [ ] Update model ProdukJadi (fillable)
- [ ] Update form create/edit produk (toggle switch)
- [ ] Update controller (store/update logic)
- [ ] Buat observer untuk auto recalculate
- [ ] Register observer di AppServiceProvider
- [ ] Test semua skenario
- [ ] Deploy ke production

## 🚀 Ready to Implement!

Semua kode sudah siap di-copy paste. Tinggal ikuti langkah-langkah di atas secara berurutan.