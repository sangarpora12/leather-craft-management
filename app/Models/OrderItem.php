<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'produk_jadi_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function produkJadi(): BelongsTo
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }
}