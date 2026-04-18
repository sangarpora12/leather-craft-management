<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenguranganStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pengurangan_stok';

    protected $fillable = [
        'produk_jadi_id',
        'kuantitas_dikurangi',
        'alasan',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    /**
     * Relasi ke produk jadi yang stoknya dikurangi.
     */
    public function produkJadi(): BelongsTo
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }
}