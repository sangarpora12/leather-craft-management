<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_jadi_id',
        'user_id',
        'nama_pelanggan',
        'ulasan',
        'rating',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the product that owns the testimonial.
     */
    public function produkJadi(): BelongsTo
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }

    /**
     * Get the user that owns the testimonial.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}