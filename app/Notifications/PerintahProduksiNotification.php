<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Produksi;
use Illuminate\Support\Facades\Storage;

class PerintahProduksiNotification extends Notification
{
    use Queueable;

    public array $createdSpks;
    public string $adminName;
    public bool $isAssigned; // <-- Tambahkan properti ini

    /**
     * Create a new notification instance.
     * @param array $createdSpks Array of Produksi models
     * @param string $adminName
     * @param bool $isAssigned Menandakan apakah tugas ini untuk operator spesifik
     */
    public function __construct(array $createdSpks, string $adminName = 'Admin', bool $isAssigned = false)
    {
        $this->adminName = $adminName;
        $this->isAssigned = $isAssigned;

        // Eager load relasi yang dibutuhkan untuk email
        $spkIds = array_map(fn($spk) => $spk->id, $createdSpks);
        $this->createdSpks = Produksi::with(['product.boms.bahanBaku'])->whereIn('id', $spkIds)->get()->all();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $firstSpk = $this->createdSpks[0] ?? null;
        $subject = 'Info Tugas Produksi Baru';
        if ($this->isAssigned) {
            $subject = 'Anda Mendapat Tugas Produksi Baru';
        }
        if (count($this->createdSpks) === 1 && $firstSpk) {
            $subject = ($this->isAssigned ? 'Tugas Baru: ' : 'Info Produksi: ') . $firstSpk->kode_spk;
        }

        // Buat array URL foto untuk semua produk
        $fotoProduk = [];
        foreach ($this->createdSpks as $spk) {
            if ($spk->product && $spk->product->foto) {
                $fotoProduk[$spk->id] = Storage::disk('public')->url($spk->product->foto);
            }
        }

        // URL Signed Route untuk Auto Login
        $url = null;
        if ($this->isAssigned) {
            $url = \Illuminate\Support\Facades\URL::signedRoute('operator.auto_login', [
                'user' => $notifiable->id,
                'redirect' => 'operator.tugas.index'
            ], now()->addDays(1)); // URL valid 7 hari
        }

        // Cek apakah operator adalah Kepala Gudang (bisa login) atau Pekerja (tidak bisa login)
        $isKepalaGudang = $notifiable->sub_role === 'kepala_gudang';

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.produksi.perintah_baru', [
                'spks' => $this->createdSpks,
                'operator' => $notifiable,
                'adminName' => $this->adminName,
                'url' => $url,
                'isAssigned' => $this->isAssigned,
                'fotoProduk' => $fotoProduk,
                'isKepalaGudang' => $isKepalaGudang,
            ]);
    }

    /**
     * Get the array representation of the notification for the database.
     */
    public function toDatabase(object $notifiable): array
    {
        $firstSpk = $this->createdSpks[0] ?? null;
        $totalSpk = count($this->createdSpks);

        if (!$firstSpk) {
            return [];
        }

        $namaProduk = $firstSpk->product ? $firstSpk->product->nama_produk : 'Produk';
        $pesan = "Anda mendapatkan {$totalSpk} tugas produksi baru dari {$this->adminName}.";
        if ($totalSpk === 1) {
            $pesan = "Tugas produksi baru untuk {$namaProduk} ({$firstSpk->kode_spk}) telah ditugaskan kepada Anda oleh {$this->adminName}.";
        }

        return [
            'judul' => "{$totalSpk} Tugas Produksi Baru",
            'pesan' => $pesan,
            'url' => route('operator.tugas.index'),
            'icon' => 'fa-industry',
            'type' => 'perintah_produksi_massal',
            'ids' => collect($this->createdSpks)->pluck('id')->toArray(),
        ];
    }
}