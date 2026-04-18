<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanMasuk extends Model
{
    use HasFactory;

    protected $table = 'bahan_masuk';

    protected $fillable = [
        'no_transaksi',
        'operator_id',
        'nama_material',
        'kode_material',
        'jumlah',
        'satuan',
        'harga_satuan',
        'supplier',
        'ref_no',
        'kategori',
        'foto_nota',
        'lokasi_gudang',
        'status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFotoNotaUrlAttribute(): ?string
    {
        if ($this->foto_nota) {
            return asset('storage/bukti_nota/' . $this->foto_nota);
        }
        return null;
    }

    public function getFormattedJumlahAttribute(): string
    {
        return number_format($this->jumlah, 2, ',', '.');
    }

    public function getFormattedHargaSatuanAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->setTimezone('Asia/Jakarta')->format('H:i');
    }

    public function getFormattedCreatedAtWibAttribute(): string
    {
        return $this->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';
    }

    public function getTotalHargaAttribute(): float
    {
        return $this->jumlah * $this->harga_satuan;
    }

    public function getFormattedTotalHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->getTotalHargaAttribute(), 0, ',', '.');
    }
}