<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\CatatLogAktivitas;

use App\Models\Pengaturan;

class BahanBaku extends Model
{
    use HasFactory, Notifiable, CatatLogAktivitas;
    protected $table = 'bahan_bakus'; // Pastikan nama tabel sesuai
    protected $fillable = ['kode_material', 'nama_material', 'kategori', 'stok', 'reserved', 'satuan', 'harga_satuan', 'stok_minimum', 'status', 'supplier', 'ref_no', 'user_id', 'is_inventory', 'foto'];

    protected $casts = [
        'is_inventory' => 'boolean',
    ];

    /**
     * Get the stock layers for this material.
     */
    public function stokLayers(): HasMany
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