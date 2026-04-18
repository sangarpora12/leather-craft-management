<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksis'; // Sesuaikan nama tabel Anda

    // Samakan dengan kolom yang dipakai di controller (SPK)
    protected $fillable = [
        'kode_spk',
        'produk_jadi_id',
        'jumlah_produksi',
        'operator_id',
        'tanggal_mulai',
        'tanggal_target',
        'tanggal_selesai',
        'tanggal_mulai_proses',
        'status',
        'total_biaya_hpp',
        'catatan',
        'keterangan',
        'foto_bukti',
        'jumlah_selesai',
    ];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_target' => 'date',
        'tanggal_selesai' => 'datetime',
        'tanggal_mulai_proses' => 'datetime',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }

    /**
     * Alias relasi untuk kompatibilitas view lama yang memanggil `$produksi->produk_jadi`.
     */
    public function produk_jadi()
    {
        return $this->produk();
    }

    // Alias agar view/controller lama yang pakai relasi `product` tidak error
    public function product()
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }

    public function bom()
    {
        return $this->hasMany(Bom::class, 'id_produk', 'produk_jadi_id');
    }

    public function boms()
    {
        return $this->hasMany(Bom::class, 'id_produk', 'produk_jadi_id');
    }
}