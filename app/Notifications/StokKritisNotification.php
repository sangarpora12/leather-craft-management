<?php

namespace App\Notifications;

use App\Models\BahanBaku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StokKritisNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bahanBaku;
    protected $batasStok;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\BahanBaku $bahanBaku
     * @param int $batasStok
     * @return void
     */
    public function __construct(BahanBaku $bahanBaku, int $batasStok)
    {
        $this->bahanBaku = $bahanBaku;
        $this->batasStok = $batasStok;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Kirim ke database dan email
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Peringatan Stok Kritis: ' . $this->bahanBaku->nama_material)
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Stok untuk bahan baku berikut telah mencapai batas kritis.')
                    ->line('Nama Material: **' . $this->bahanBaku->nama_material . '**')
                    ->line('Sisa Stok: **' . $this->bahanBaku->stok . ' ' . $this->bahanBaku->satuan . '**')
                    ->line('Batas Stok Kritis: **' . $this->batasStok . ' ' . $this->bahanBaku->satuan . '**')
                    ->action('Lihat Detail Bahan', route('admin.bahan.index', ['search' => $this->bahanBaku->kode_material]))
                    ->line('Harap segera lakukan pengadaan untuk menghindari kehabisan stok.');
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
            'title' => 'Stok Kritis: ' . $this->bahanBaku->nama_material,
            'message' => 'Stok tersisa ' . $this->bahanBaku->stok . ' ' . $this->bahanBaku->satuan . '. Segera lakukan pengadaan.',
            'url' => route('admin.bahan.index', ['search' => $this->bahanBaku->kode_material]),
            'target' => 'admin', // Menandakan notifikasi ini untuk admin
        ];
    }
}