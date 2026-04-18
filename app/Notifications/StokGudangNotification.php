<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StokGudangNotification extends Notification
{
    use Queueable;

    protected $kode_spk;
    protected $pesan_khusus;

    // Kita bisa mengirim kode SPK, pesan kustom, dan ID bahan baku untuk URL
    public function __construct(string $kode_spk, ?string $pesan_khusus = null, ?int $bahanBakuId = null)
    {
        $this->kode_spk = $kode_spk;
        $this->pesan_khusus = $pesan_khusus;
        $this->bahanBakuId = $bahanBakuId; // Tambahkan properti ini
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'judul' => 'Info Stok Gudang',
            'pesan' => $this->pesan_khusus ?? "Stok bahan baku telah dipotong otomatis untuk produksi dengan kode SPK {$this->kode_spk}.",
            'url'   => $this->bahanBakuId ? route('admin.bahan.show', $this->bahanBakuId) : route('admin.bahan.index') // Arahkan Admin ke halaman bahan baku atau detail jika ada ID
        ];
    }
}