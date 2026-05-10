<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\CatatLogAktivitas;
use App\Models\ProdukKeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdukJadi extends Model
{
    use HasFactory, CatatLogAktivitas;

    // (Opsional) Jika nama tabel Anda di database bukan 'produk_jadis', uncomment dan ubah baris di bawah:
    // protected $table = 'produk_jadi';

    // Daftarkan kolom-kolom yang ada di tabel Produk Jadi agar bisa disimpan (Mass Assignment)
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'kategori',
        'warna',
        'jenis_motif',
        'harga_jual',
        'tipe_harga',
        'deskripsi',
        'keterangan',
        'stok',
        'stok_minimum',
        'foto',
        'panjang',
        'lebar',
        'tinggi',
        'detail_pola',
        'sketsa',
    ];

    /**
     * Cast JSON columns to array automatically
     */
    protected $casts = [
        'kategori'    => 'array',
        'warna'       => 'array',
        'jenis_motif' => 'array',
        'sketsa'      => 'array',
    ];

    /**
     * Relasi ke model Bom (Bill of Materials)
     * 1 Produk Jadi memiliki banyak BOM (One-to-Many)
     */
    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class, 'id_produk');
    }

    /**
     * Relasi ke ProdukKeluar (riwayat pengurangan stok)
     */
    public function produkKeluars(): HasMany
    {
        return $this->hasMany(ProdukKeluar::class, 'produk_jadi_id', 'id');
    }

    /**
     * Relasi ke RiwayatPenguranganStok
     */
    public function riwayatPenguranganStok(): HasMany
    {
        return $this->hasMany(RiwayatPenguranganStok::class, 'produk_jadi_id', 'id');
    }

    /**
     * Relasi ke model Produksi.
     * Satu produk jadi bisa memiliki banyak riwayat produksi.
     */
    public function produksi(): HasMany
    {
        return $this->hasMany(Produksi::class, 'produk_jadi_id');
    }

    /**
     * Mengurangi stok produk jadi dan mencatat riwayatnya.
     *
     * @param int $kuantitas Jumlah yang akan dikurangi.
     * @param string $alasan Alasan pengurangan stok.
     * @return bool True jika berhasil, false jika stok tidak mencukupi.
     */
    public function kurangiStok(int $kuantitas, string $alasan): bool
    {
        if ($this->stok < $kuantitas) {
            return false;
        }

        // Menggunakan transaksi database untuk memastikan integritas data
        try {
            DB::transaction(function () use ($kuantitas, $alasan) {
                // 1. Kurangi stok
                $this->decrement('stok', $kuantitas);

                // 2. Catat ke tabel riwayat yang benar (produk_keluars)
                ProdukKeluar::create([
                    'produk_jadi_id' => $this->id,
                    'jumlah_keluar' => $kuantitas,
                    'alasan' => $alasan,
                    'tanggal_keluar' => now(),
                    'user_id' => Auth::id(), // Catat user yang melakukan aksi
                ]);
            });
        } catch (\Throwable $e) {
            // Jika terjadi error, transaksi akan di-rollback secara otomatis.
            // Log::error('Gagal mengurangi stok untuk produk ID ' . $this->id . ': ' . $e->getMessage());
            return false;
        }

        return true;
    }

    // ====================================================================
    // RUMUS PENGECEKAN KESIAPAN BAHAN BAKU UNTUK SPK (VERSI ANTI NGELES)
    // ====================================================================

    /**
     * Menghitung maksimal produk yang bisa dibuat berdasarkan stok bahan baku saat ini.
     * (Mencari bahan baku mana yang paling membatasi / bottleneck)
     */// ====================================================================
    // RUMUS PENGECEKAN KESIAPAN BAHAN BAKU UNTUK SPK (VERSI ANTI NGELES)
    // ====================================================================

    /**
     * Menghitung maksimal produk yang bisa dibuat berdasarkan stok bahan baku saat ini.
     * (Mencari bahan baku mana yang paling membatasi / bottleneck)
     */
    public function getMaxProductionAttribute()
    {
        // Kalau resepnya belum dibikin, berarti max produksinya 0
        if ($this->boms->isEmpty()) {
            return 0;
        }

        $maxQty = null;

        foreach ($this->boms as $bom) {
            // Cek apakah bahan baku sudah dihapus dari database atau butuhnya 0
            if (!$bom->bahanBaku || $bom->jumlah_kebutuhan <= 0) {
                return 0; 
            }

            // ✅ BYPASS: Abaikan jika kategorinya Bahan Pelengkap atau is_inventory = 0
            if (($bom->bahanBaku->is_inventory == 0) || strtolower($bom->bahanBaku->kategori) === 'bahan pelengkap') {
                continue;
            }

            // Rumus: Stok di Gudang dibagi Kebutuhan per Resep (HASIL INTEGER TANPA DESIMAL)
            $possible = (int) floor($bom->bahanBaku->stok / $bom->jumlah_kebutuhan);
            
            // Cari angka yang paling kecil (bottleneck)
            if ($maxQty === null || $possible < $maxQty) {
                $maxQty = $possible;
            }
        }

        // Jika semua bahan ternyata pelengkap (kemungkinan kecil), anggap bisa produksi tak terbatas (999)
        return $maxQty ?? 999;
    }

    /**
     * Mengecek apakah produk siap dibikinin SPK (Bisa buat minimal 1 buah)
     */
    public function getIsReadyAttribute()
    {
        return $this->max_production > 0;
    }

    /**
     * Ngasih tau alasan kenapa digembok (Buat ditampilin di pop-up layar admin)
     */
    public function getReasonAttribute()
    {
        if ($this->boms->isEmpty()) {
            return "Resep BOM belum dibuat.";
        }

        foreach ($this->boms as $bom) {
            // 1. Cek kasus bahan gaib
            if (!$bom->bahanBaku) {
                return "ERROR: Ada bahan baku di resep BOM ini yang sudah DIHAPUS dari sistem gudang.";
            }

            // 2. Cek kasus butuh 0
            if ($bom->jumlah_kebutuhan <= 0) {
                return "ERROR: Jumlah kebutuhan bahan di BOM adalah 0. Silakan edit resep BOM.";
            }

            // ✅ BYPASS: Abaikan error stok kalau kategorinya Bahan Pelengkap
            if (($bom->bahanBaku->is_inventory == 0) || strtolower($bom->bahanBaku->kategori) === 'bahan pelengkap') {
                continue;
            }

            // 3. Cek kasus stok murni kurang
            if ($bom->bahanBaku->stok < $bom->jumlah_kebutuhan) {
                return "Stok '{$bom->bahanBaku->nama_material}' kurang. Butuh: {$bom->jumlah_kebutuhan}, Sisa: {$bom->bahanBaku->stok}.";
            }
        }

        return "Siap diproduksi.";
    }
    /**
     * ✅ DYNAMIC PRICING: Hitung Harga Jual Otomatis
     * Harga Jual = HPP (Harga Pokok Produksi) + (HPP * Margin / 100)
     */
    public function getHargaJualOtomatisAttribute()
    {
        // Hitung HPP terlebih dahulu dari BOM
        $hpp = 0;
        foreach ($this->boms as $bom) {
            if ($bom->bahanBaku) {
                $hpp += $bom->jumlah_kebutuhan * $bom->bahanBaku->harga;
            }
        }

        // Ambil Margin dari Pengaturan
        $pengaturan = \App\Models\Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->first();
        $margin = $pengaturan && $pengaturan->nilai ? (float) $pengaturan->nilai : 50;
        
        // Hitung Harga Jual
        $hargaJual = $hpp + ($hpp * $margin / 100);

        // Pembulatan harga (jika diatur)
        $pembulatanRaw = \App\Models\Pengaturan::where('nama_pengaturan', 'pembulatan_harga')->first();
        $pembulatan = $pembulatanRaw && $pembulatanRaw->nilai ? $pembulatanRaw->nilai : 'none';
        if ($pembulatan != 'none' && is_numeric($pembulatan)) {
            $hargaJual = ceil($hargaJual / $pembulatan) * $pembulatan;
        }

        return (int) $hargaJual;
    }

    /**
     * ✅ GLOBAL PRICE SYNC: Update harga jual otomatis berdasarkan HPP BOM + Margin
     * Hanya dipanggil untuk produk dengan tipe_harga = 'otomatis'
     * 
     * @param float|null $margin Margin yang akan digunakan. Jika null, ambil dari Pengaturan.
     * @return bool True jika berhasil diupdate, false jika produk mode manual atau gagal.
     */
    public function updateAutomaticPrice(?float $margin = null): bool
    {
        // Skip jika produk ini mode manual
        if ($this->tipe_harga !== 'otomatis') {
            return false;
        }

        // Load BOM dengan material
        $this->load('boms.material');

        // Hitung HPP dari BOM
        $totalHpp = 0;
        foreach ($this->boms as $bom) {
            if ($bom->material) {
                $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                $totalHpp += $hargaSatuan * $jumlahKebutuhan;
            }
        }

        // Ambil margin dari parameter atau dari Pengaturan database
        if ($margin === null) {
            $margin = (float) (\App\Models\Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 50);
        }

        // Hitung harga jual: HPP + (HPP * margin / 100), dibulatkan ke atas
        $hargaJual = ceil($totalHpp + ($totalHpp * ($margin / 100)));

        // Simpan ke database
        $this->update(['harga_jual' => $hargaJual]);

        return true;
    }

    /**
     * ✅ Aksesor Override Harga Jual: Otomatis JIKA BELUM ADA NILAI MANUAL
     * 
     * Aturan Baru:
     * ✅ Jika harga_jual NULL / 0 / belum diisi: TAMPILKAN OTOMATIS
     * ✅ Jika sudah diisi manual: TAMPILKAN NILAI MANUAL (Prioritas tertinggi)
     */
    public function getHargaJualAttribute($value)
    {
        // ✅ ✅ UPDATE: Jika 0 atau NULL dianggap mode OTOMATIS
        if ($value > 0) {
            // ✅ JIKA ADA NILAI > 0: SELALU TAMPILKAN MANUAL
            return $value;
        }

        // ✅ JIKA 0 ATAU NULL: TAMPILKAN HARGA OTOMATIS
        return $this->harga_jual_otomatis;
    }
}