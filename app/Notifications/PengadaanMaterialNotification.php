<?php

namespace App\Notifications;

use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PengadaanMaterialNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengadaan;
    public $admin;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Pengadaan $pengadaan
     * @param \App\Models\User $admin
     */
    public function __construct(Pengadaan $pengadaan, User $admin)
    {
        $this->pengadaan = $pengadaan;
        $this->admin = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'pengadaan_id' => $this->pengadaan->id,
            'message' => 'Pengadaan bahan baku baru telah dibuat oleh ' . $this->admin->name,
            'title' => 'Pengadaan Material Baru',
            'url' => route('pengadaan.show', $this->pengadaan->id),
        ];
    }
}