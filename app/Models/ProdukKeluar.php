<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKeluar extends Model
{
    use HasFactory;

    protected $table = 'produk_keluars';

    protected $fillable = [
        'produk_jadi_id',
        'jumlah_keluar',
        'alasan',
        'catatan',
        'tanggal_keluar',
        'user_id',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    public function produk_jadi()
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
