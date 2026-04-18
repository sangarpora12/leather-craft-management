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

            // Rumus: Stok di Gudang dibagi Kebutuhan per Resep (HASIL INTEGER TANPA DESIMAL)
            $possible = (int) floor($bom->bahanBaku->stok / $bom->jumlah_kebutuhan);
            
            // Cari angka yang paling kecil (bottleneck)
            if ($maxQty === null || $possible < $maxQty) {
                $maxQty = $possible;
            }
        }

        return $maxQty ?? 0;
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

            // 3. Cek kasus stok murni kurang
            if ($bom->bahanBaku->stok < $bom->jumlah_kebutuhan) {
                return "Stok '{$bom->bahanBaku->nama_material}' kurang. Butuh: {$bom->jumlah_kebutuhan}, Sisa: {$bom->bahanBaku->stok}.";
            }
        }

        return "Siap diproduksi.";
    }
}