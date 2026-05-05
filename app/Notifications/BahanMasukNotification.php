<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\BahanBaku;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BahanMasukNotification extends Notification
{
    use Queueable;

    protected $bahan;
    protected $qtyMasuk;

    /**
     * Create a new notification instance.
     */
    public function __construct(BahanBaku $bahan, $qtyMasuk)
    {
        $this->bahan = $bahan;
        $this->qtyMasuk = $qtyMasuk;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Gunakan URL yang bisa diakses dari mana saja (bukan localhost)
        // Route yang benar adalah /admin/bahan (bukan /admin/bahan-baku)
        $url = route('admin.bahan.index');
        
        return (new MailMessage)
        ->subject('📦 Notifikasi Bahan Baku Masuk')
        ->greeting('Halo ' . $notifiable->name . '!')
        ->line("Bahan baku **{$this->bahan->nama_material}** baru saja masuk.")
        ->line("Jumlah masuk: **{$this->qtyMasuk} {$this->bahan->satuan}**")
        ->line("Stok saat ini: **{$this->bahan->stok} {$this->bahan->satuan}**")
        ->action('Lihat Stok Bahan Baku', $url)
        ->line('Terima kasih telah menggunakan sistem Berliano Leather!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => 'Bahan Baku Masuk',
            'pesan' => "Bahan {$this->bahan->nama_material} baru saja masuk/ditambah sebanyak {$this->qtyMasuk} {$this->bahan->satuan}. Stok saat ini: {$this->bahan->stok} {$this->bahan->satuan}.",
            'url'   => route('admin.bahan.index'),
            'icon'  => 'fa-box'
        ];
    }
}
