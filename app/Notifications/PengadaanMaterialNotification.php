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
        // Kirim ke Database (untuk lonceng notifikasi) + Email Gmail
        return ['database', 'mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('📦 PERINTAH PENGADAAN MATERIAL BARU')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Ada perintah pengadaan bahan baru dari Admin ' . $this->admin->name)
            ->line('Nomor Order: ' . $this->pengadaan->no_referensi)
            ->line('Prioritas: ' . $this->pengadaan->prioritas)
            ->line('Deadline: ' . $this->pengadaan->tanggal_dibutuhkan)
            ->action('LIHAT DETAIL PESANAN', route('operator.belanja.index'))
            ->line('Silahkan segera proses pesanan ini.')
            ->salutation('Salam, Sistem Inventory Berliano Leather');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'pengadaan_id' => $this->pengadaan->id,
            'no_referensi' => $this->pengadaan->no_referensi,
            'message' => 'Pengadaan bahan baru dari Admin ' . $this->admin->name,
            'title' => '📦 Perintah Pengadaan Baru',
            'prioritas' => $this->pengadaan->prioritas,
            'url' => route('operator.belanja.index'),
            'admin_name' => $this->admin->name,
        ];
    }
}