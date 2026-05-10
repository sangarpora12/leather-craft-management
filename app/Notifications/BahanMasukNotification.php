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
    protected $operatorName;
    protected $sumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(BahanBaku $bahan, $qtyMasuk, $operatorName = 'Operator', $sumber = 'Input Manual')
    {
        $this->bahan = $bahan;
        $this->qtyMasuk = $qtyMasuk;
        $this->operatorName = $operatorName;
        $this->sumber = $sumber;
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
        $url = route('admin.bahan.index');
        $tanggal = now()->isoFormat('dddd, D MMMM YYYY [Pukul] HH:mm');
        
        return (new MailMessage)
        ->subject('📦 Bahan Baku Masuk - ' . $this->bahan->nama_material)
        ->greeting('Halo ' . $notifiable->name . '!')
        ->line("**{$this->operatorName}** telah menambahkan bahan baku masuk melalui **{$this->sumber}**.")
        ->line("")
        ->line("📋 **Detail Bahan Masuk:**")
        ->line("• Nama Bahan: **{$this->bahan->nama_material}**")
        ->line("• Warna: **{$this->bahan->warna}**")
        ->line("• Jumlah Masuk: **{$this->qtyMasuk} {$this->bahan->satuan}**")
        ->line("• Stok Saat Ini: **{$this->bahan->stok} {$this->bahan->satuan}**")
        ->line("• Diinput oleh: **{$this->operatorName}**")
        ->line("• Tanggal: **{$tanggal}**")
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
        $tanggal = now()->isoFormat('dddd, D MMMM YYYY');
        
        return [
            'judul' => 'Bahan Baku Masuk',
            'pesan' => "{$this->operatorName} menambahkan {$this->bahan->nama_material} ({$this->bahan->warna}) sebanyak {$this->qtyMasuk} {$this->bahan->satuan} melalui {$this->sumber}. Stok saat ini: {$this->bahan->stok}.",
            'url'   => route('admin.bahan.index'),
            'icon'  => 'fa-box',
            'detail' => [
                'nama_bahan' => $this->bahan->nama_material,
                'warna' => $this->bahan->warna,
                'jumlah' => $this->qtyMasuk,
                'satuan' => $this->bahan->satuan,
                'operator' => $this->operatorName,
                'sumber' => $this->sumber,
                'tanggal' => $tanggal,
            ]
        ];
    }
}
