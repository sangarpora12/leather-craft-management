<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\BahanBaku;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BahanMasukOperatorNotification extends Notification
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
        return ['database'];
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
            'url'   => route('operator.bahan.index'),
            'icon'  => 'fa-box'
        ];
    }
}