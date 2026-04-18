<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Pengadaan;
use App\Models\BahanBaku;

class PengadaanBahanNotification extends Notification
{
    use Queueable;

    protected $pengadaan;

    public function __construct(Pengadaan $pengadaan)
    {
        $this->pengadaan = $pengadaan;
    }

    public function via($notifiable)
    {
        if (!is_object($notifiable)) {
            return ['mail'];
        }

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

        $bahan = $this->pengadaan->bahan;
        $namaBahan = $bahan?->nama_material ?? 'Bahan';
        $jumlah = (float) ($this->pengadaan->jumlah ?? 0);
        $satuan = $bahan?->satuan ?? 'pcs';
        $noReferensi = $this->pengadaan->no_referensi ?? '-';

        // Arahkan ke halaman penerimaan material operator
        $url = $base . route('operator.pengadaan.list', [], false);

        $mail = (new MailMessage)
            ->subject('📦 [PENGADAAN BARU] ' . $namaBahan . ' - ' . $noReferensi)
            ->greeting('Halo, Tim Operator!')
            ->line("Ada pengadaan bahan baru yang perlu Anda terima:")
            ->line('')
            ->line('**Detail Pengadaan:**')
            ->line("- **Nama Bahan:** {$namaBahan}")
            ->line("- **Jumlah:** {$jumlah} {$satuan}")
            ->line("- **No. Referensi:** {$noReferensi}")
            ->line("- **Tanggal Dibutuhkan:** " . ($this->pengadaan->tanggal_dibutuhkan ? \Carbon\Carbon::parse($this->pengadaan->tanggal_dibutuhkan)->format('d M Y') : '-'))
            ->line('')
            ->line('Silakan cek daftar pengadaan dan lakukan penerimaan material sesuai jumlah yang dipesan.')
            ->action('📦 LIHAT DAFTAR PENGADAAN', $url)
            ->line('Jika belum login, Anda akan diarahkan ke halaman login operator terlebih dahulu.');

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $bahan = $this->pengadaan->bahan;
        $namaBahan = $bahan?->nama_material ?? 'Bahan';
        $jumlah = (float) ($this->pengadaan->jumlah ?? 0);
        $satuan = $bahan?->satuan ?? 'pcs';
        $noReferensi = $this->pengadaan->no_referensi ?? '-';

        return [
            'judul' => '📦 [PENGADAAN BARU] ' . $namaBahan,
            'pesan' => "Ada pengadaan bahan baru: {$namaBahan} ({$noReferensi}) - {$jumlah} {$satuan}. Silakan cek dan terima material.",
            'url'   => route('operator.pengadaan.list'),
            'pengadaan_id' => $this->pengadaan->id,
            'bahan_id' => $this->pengadaan->bahan_baku_id,
        ];
    }
}