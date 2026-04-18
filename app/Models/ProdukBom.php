<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukBom extends Model
{
    protected $table = 'product_boms';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'kategori_id',
        'warna',
        'jenis_motif',
        'satuan_id',
        'deskripsi',
        'foto',
        'harga_jual',
        'harga_bahan',
        'biaya_produksi',
        'biaya_lain',
        'margin',
        'dimensi_panjang',
        'dimensi_lebar',
        'dimensi_tebal',
        'satuan_dimensi',
        'is_active'
    ];

    /**
     * Relasi ke BOM (Bill of Materials)
     */
    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class, 'id_produk_bom');
    }

    /**
     * Relasi ke Kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi ke Satuan
     */
    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }
}