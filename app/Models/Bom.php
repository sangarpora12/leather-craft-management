<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BahanBaku;

class Bom extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * Pada project ini migrasi membuat tabel bernama `bom` (bukan `boms`).
     */
    protected $table = 'bom';

    /**
     * Kolom yang boleh diisi (Mass Assignment).
     * Sesuaikan dengan nama kolom di database Anda.
     */
    protected $fillable = [
        // mengikuti struktur tabel `bom`
        'id_produk',
        'id_bahan',
        'jumlah_kebutuhan',
        'satuan',
    ];

    /**
     * Relasi Kebalikan: Setiap item BOM milik satu Produk Bom (Master Produk).
     */
    public function produkBom(): BelongsTo
    {
        return $this->belongsTo(ProdukBom::class, 'id_produk_bom');
    }

    /**
     * Alias untuk relasi produkBom() agar bisa dipanggil dengan snake_case.
     * Ini untuk mengatasi error RelationNotFoundException.
     */
    public function produk_bom(): BelongsTo
    {
        return $this->produkBom();
    }

    /**
     * Legacy relasi untuk kompatibilitas (akan dihapus nanti)
     */
    public function produkJadi(): BelongsTo
    {
        return $this->belongsTo(ProdukJadi::class, 'id_produk');
    }

    /**
     * Legacy alias untuk kompatibilitas (akan dihapus nanti)
     */
    public function produk_jadi(): BelongsTo
    {
        return $this->produkJadi();
    }

    /**
     * Relasi ke Bahan Baku.
     * Asumsi: Anda punya model 'BahanBaku' (App\Models\BahanBaku).
     * Jika belum punya model BahanBaku, Anda bisa menghapus atau mengomentari fungsi ini dulu.
     */
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }

    // Tambahkan relasi material jika diperlukan
    public function material(): BelongsTo
    {
        // Di tabel `bom` relasi bahan baku menggunakan kolom `id_bahan`
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
    // Membuang .00 di belakang jumlah kebutuhan bahan
    public function getJumlahKebutuhanAttribute($value)
    {
        return (float) $value;
    }
}