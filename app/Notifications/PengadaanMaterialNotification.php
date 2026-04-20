<?php

namespace App\Notifications;

use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PengadaanMaterialNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengadaanGroup;
    public $admin;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Database\Eloquent\Collection $pengadaanGroup
     * @param \App\Models\User $admin
     */
    public function __construct($pengadaanGroup, User $admin)
    {
        $this->pengadaanGroup = $pengadaanGroup;
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
        // PREPARE DATA FOTO BAHAN (PERSIS SEPERTI NOTIFIKASI PRODUKSI)
        $daftarFoto = [];
        foreach($this->pengadaanGroup as $item) {
            if ($item->bahan && $item->bahan->foto) {
                $daftarFoto[$item->id] = \Illuminate\Support\Facades\Storage::disk('public')->url($item->bahan->foto);
            }
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('📦 PERINTAH PENGADAAN MATERIAL BARU')
            ->markdown('emails.pengadaan.perintah_baru', [
                'operatorName' => $notifiable->name,
                'adminName' => $this->admin->name,
                'prioritas' => $this->pengadaanGroup->first()->prioritas,
                'deadline' => date('d F Y', strtotime($this->pengadaanGroup->first()->tanggal_dibutuhkan)),
                'pengadaanGroup' => $this->pengadaanGroup,
                'daftarFoto' => $daftarFoto,
                'actionUrl' => route('operator.belanja.index')
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $daftarBahan = $this->pengadaanGroup->map(function($item) {
            return intval($item->jumlah) . ' ' . $item->bahan->satuan . ' - ' . $item->bahan->nama_material;
        })->implode(', ');

        return [
            'pengadaan_group' => $this->pengadaanGroup->pluck('id'),
            'no_referensi' => $this->pengadaanGroup->first()->no_referensi,
            'message' => $this->pengadaanGroup->count() . ' bahan baru dari Admin ' . $this->admin->name,
            'title' => '📦 Perintah Pengadaan Baru',
            'prioritas' => $this->pengadaanGroup->first()->prioritas,
            'daftar_bahan' => $daftarBahan,
            'jumlah_item' => $this->pengadaanGroup->count(),
            'url' => route('operator.belanja.index'),
            'admin_name' => $this->admin->name,
        ];
    }
}