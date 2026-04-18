<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProduksiBaruNotification extends Notification
{
    use Queueable;

    protected $produksi;

    public function __construct($produksi)
    {
        // Menyimpan data produksi yang baru dibuat
        $this->produksi = $produksi; 
    }

    // Menggunakan database untuk menyimpan notifikasi
    public function via($notifiable)
    {
        return ['database', 'mail']; 
    }

    public function toMail($notifiable): MailMessage
    {
        // Base URL operator agar bisa dibuka dari HP satu WiFi (fallback)
        $base = rtrim(config('app.operator_url') ?: config('app.url'), '/');
        if (!$base || str_contains($base, 'localhost') || str_contains($base, '127.0.0.1')) {
            $ip = gethostbyname(gethostname());
            $base = 'http://' . $ip . ':8000';
        }

        $namaProduk = $this->produksi->product?->nama_produk ?? 'Produk';
        $kodeSpk = $this->produksi->kode_spk ?? '-';
        $qty = (float) ($this->produksi->jumlah_produksi ?? 0);
        $keterangan = $this->produksi->keterangan ?? $this->produksi->catatan ?? null;

        // Arahkan ke detail tugas operator (lihat BOM + HPP dulu)
        $url = $base . route('operator.tugas.detail', ['id' => $this->produksi->id], false);

        $mail = (new MailMessage)
            ->subject('🚨 [TUGAS BARU] Perintah Produksi: ' . $namaProduk . ' - ' . $kodeSpk)
            ->greeting('Halo, Tim Produksi!')
            ->line("Ada tugas baru untuk mengerjakan **{$namaProduk}** sebanyak **{$qty} Pcs**.");

        // Tambahkan catatan admin jika ada
        if ($keterangan) {
            $mail->line('');
            $mail->line('📋 **Catatan dari Admin:**');
            $mail->line("> {$keterangan}");
        }

        $mail->line('')
            ->line('Silakan cek detail bahan (BOM) dan kalkulasi modal (HPP) melalui tautan di bawah ini sebelum memulai pengerjaan.')
            ->action('🛠️ LIHAT DETAIL TUGAS', $url)
            ->line('Jika belum login, Anda akan diarahkan ke halaman login operator terlebih dahulu.');

        return $mail;
    }

    // Format data yang akan disimpan ke tabel notifications
    public function toDatabase($notifiable)
    {
        $namaProduk = $this->produksi->product?->nama_produk ?? 'Produk';
        $kodeSpk = $this->produksi->kode_spk ?? '-';
        $qty = (float) ($this->produksi->jumlah_produksi ?? 0);

        return [
            'judul' => '🚨 [TUGAS BARU] Perintah Produksi',
            'pesan' => "Ada tugas baru: {$namaProduk} ({$kodeSpk}) - {$qty} Pcs. Cek BOM & HPP sebelum mulai.",
            // Arahkan ke detail tugas (selaras dengan email)
            'url'   => route('operator.tugas.detail', ['id' => $this->produksi->id]),
            'produksi_id' => $this->produksi->id,
        ];
    }
}