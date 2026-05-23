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
        'foto_nota_terakhir',
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

    /**
     * Scope: Cari master material berdasarkan nama (case-insensitive).
     */
    public function scopeMasterByName($query, $nama)
    {
        return $query->whereRaw('LOWER(nama_material) = LOWER(?)', [$nama]);
    }

    /**
     * Scope: Ambil semua varian dari material yang sama (berdasarkan kode_material).
     */
    public function scopeVarians($query, $kodeMaterial)
    {
        return $query->where('kode_material', $kodeMaterial)->orderBy('warna');
    }

    /**
     * Cari atau buat kode_material untuk suatu nama_material.
     * Jika sudah ada varian lain, pakai kode_material yang sama.
     * Jika belum ada sama sekali, generate baru.
     */
    public static function resolveKodeMaterial(string $namaMaterial): string
    {
        $existing = self::whereRaw('LOWER(nama_material) = LOWER(?)', [$namaMaterial])->first();
        return $existing ? $existing->kode_material : self::generateKodeMaterial();
    }
    // Membuang .00 di belakang stok
    public function getStokAttribute($value)
    {
        return (float) $value;
    }

    /**
     * Relasi ke tabel 'warnas' untuk mendapatkan kode hex.
     */
    public function color()
    {
        return $this->belongsTo(Warna::class, 'warna', 'nama_warna');
    }
}