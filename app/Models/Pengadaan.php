<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    protected $table = 'pengadaans';

    protected $fillable = [
        'no_referensi',
        'bahan_baku_id',
        'warna',
        'jumlah',
        'received',
        'status',
        'prioritas',
        'tanggal_dibutuhkan',
        'catatan',
        'operator_id',
        'admin_id',
    ];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
