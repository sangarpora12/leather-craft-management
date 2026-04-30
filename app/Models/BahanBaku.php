<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_bakus';

    protected $fillable = [
        'kode_material',
        'nama_material',
        'kategori',
        'satuan',
        'stok',
        'stok_minimum',
        'harga_satuan',
        'supplier',
        'is_inventory',
        'foto',
        'warna',
        'detail',
        'deskripsi_bahan',
        'ref_no',
    ];

    /**
     * Get the stock layers for this material.
     */
    public function stockLayers(): HasMany
    {
        return $this->hasMany(BahanStokLayer::class, 'bahan_baku_id');
    }

    /**
     * Generate a unique material code based on the global prefix from settings.
     */
    public static function generateKodeMaterial(string $kategori = null): string
    {
        // Ambil prefix dari tabel pengaturan, dengan fallback 'BHN-'
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';

        do {
            $number = rand(1000, 9999);
            $kode = $prefix . $number;
        } while (self::where('kode_material', $kode)->exists());

        return $kode;
    }
}