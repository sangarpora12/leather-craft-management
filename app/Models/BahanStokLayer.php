<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanStokLayer extends Model
{
    use HasFactory;
    protected $table = 'bahan_stok_layers';
    protected $fillable = [
        'bahan_baku_id',
        'tanggal_masuk',
        'qty_masuk',
        'qty_sisa',
        'harga_beli',
        'sumber_harga',
        'supplier',
        'ref_no',
        'foto_nota',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'qty_masuk' => 'decimal:2',
        'qty_sisa' => 'decimal:2',
        'harga_beli' => 'decimal:2',
    ];

    /**
     * Get the bahan baku that owns this stock layer.
     */
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
    // Tambahkan ini di dalam class BahanStokLayer
public function material()
{
    // Mengacu ke model BahanBaku menggunakan foreign key bahan_baku_id
    return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
}
}

