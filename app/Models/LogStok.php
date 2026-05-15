<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStok extends Model
{
    protected $table = 'log_stok';

    protected $fillable = [
        'bahan_baku_id',
        'operator_id',
        'jenis_transaksi',
        'jumlah',
        'keterangan',
    ];
}