<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'modul',
        'aktivitas',
        'ip_address',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan modul
     */
    public function scopeModul($query, $modul)
    {
        if ($modul) {
            return $query->where('modul', $modul);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeUser($query, $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        if ($tanggal) {
            return $query->whereDate('created_at', $tanggal);
        }
        return $query;
    }

    /**
     * Hapus log yang sudah lebih dari 30 hari
     */
    public static function hapusLogLama()
    {
        return static::where('created_at', '<', now()->subDays(30))->delete();
    }
}