<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * PERBAIKAN: Tambahkan 'nama' dan 'jabatan' ke sini
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'jabatan',
        'foto_profil',
        'email_verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string|null
     */
    public function routeNotificationForMail($notification)
    {
        // Log untuk debugging
        \Log::info('routeNotificationForMail called', [
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->toDateTimeString() ?? 'NULL',
            'notification_class' => get_class($notification)
        ]);

        // Skip email jika tidak aktif atau email tidak valid
        if (!$this->is_active || 
            empty($this->email) || 
            !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            
            \Log::warning('Email notification skipped - invalid user/email', [
                'user_id' => $this->id,
                'email' => $this->email,
                'is_active' => $this->is_active
            ]);
            
            return null; // Skip mail channel
        }

        return $this->email;
    }

    public function bahanBakus()
    {
        return $this->hasMany(BahanBaku::class);
    }

    public function productions()
    {
        return $this->hasMany(Produksi::class, 'operator_id');
    }

    /**
     * Alias untuk kompatibilitas (beberapa controller lama memakai nama relasi `produksis`).
     */
    public function produksis()
    {
        return $this->productions();
    }
}