<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ScanNotaNotification extends Notification
{
    use Queueable;

    protected $scanData;
    protected $operatorName;
    protected $fotoNotaPath;

    /**
     * Create a new notification instance.
     *
     * @param array $scanData Data hasil scan (id, bahan_id, nama_bahan, items, etc.)
     * @param string $operatorName Nama operator yang scan
     * @param string|null $fotoNotaPath Path foto nota
     */
    public function __construct(array $scanData, string $operatorName, ?string $fotoNotaPath = null)
    {
        $this->scanData = $scanData;
        $this->operatorName = $operatorName;
        $this->fotoNotaPath = $fotoNotaPath;
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
     */
    public function toMail(object $notifiable): MailMessage
    {
        $base = rtrim(config('app.url'), '/');
        if (!$base || str_contains($base, 'localhost') || str_contains($base, '127.0.0.1')) {
            $ip = gethostbyname(gethostname());
            $base = 'http://' . $ip . ':8000';
        }

        $url = $base . route('admin.bahan.index', [], false);
        
        // Jika ada foto nota, generate URL yang bisa diakses
        $fotoNotaUrl = null;
        if ($this->fotoNotaPath) {
            $fotoNotaUrl = $base . '/storage/' . $this->fotoNotaPath;
        }

        $bahanId = $this->scanData['bahan_id'] ?? '-';
        $namaBahan = $this->scanData['nama_bahan'] ?? 'Bahan Baru';
        
        $mail = (new MailMessage)
            ->subject('📸 [SCAN NOTA BARU] - ' . $namaBahan)
            ->greeting('Halo, Admin!')
            ->line("Operator **{$this->operatorName}** telah berhasil melakukan scan nota untuk bahan:")
            ->line('')
            ->line('**Detail Scan:**')
            ->line("- **ID Bahan:** " . $bahanId)
            ->line("- **Nama Bahan:** " . $namaBahan)
            ->line("- **Tanggal Scan:** " . now()->format('d M Y H:i'))
            ->line('');

        // Tampilkan daftar items jika ada
        if (!empty($this->scanData['items'])) {
            $mail->line('**Daftar Barang di Nota:**');
            foreach ($this->scanData['items'] as $index => $item) {
                $harga = isset($item['harga']) ? $item['harga'] : 0;
                $mail->line(($index + 1) . ". " . $item['nama'] . " - " . $item['jumlah'] . " " . $item['satuan'] . " (Rp " . number_format($harga, 0, ',', '.') . ")");
            }
            $mail->line('');
        }

        // Total harga jika ada
        if (isset($this->scanData['total_harga'])) {
            $mail->line("**Total Harga:** Rp " . number_format($this->scanData['total_harga'], 0, ',', '.'));
            $mail->line('');
        }

        if ($fotoNotaUrl) {
            $mail->line('**Foto Nota:**');
            $mail->line("![Foto Nota]({$fotoNotaUrl})");
            $mail->line('');
        }

        $mail->action('📋 LIHAT DETAIL BAHAN', $url)
            ->line('Silakan klik tombol di atas untuk melihat detail bahan dan verifikasi hasil scan.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $base = rtrim(config('app.url'), '/');
        if (!$base || strpos($base, 'localhost') !== false || strpos($base, '127.0.0.1') !== false) {
            $ip = gethostbyname(gethostname());
            $base = 'http://' . $ip . ':8000';
        }

        $namaBahan = isset($this->scanData['nama_bahan']) ? $this->scanData['nama_bahan'] : 'Bahan Baru';

        return [
            'judul' => '📸 [SCAN NOTA BARU] ' . $namaBahan,
            'pesan' => "Operator " . $this->operatorName . " telah scan nota untuk " . $namaBahan . ".",
            'url' => route('admin.bahan.index'),
            'icon' => 'camera',
            'type' => 'scan_nota',
            'scan_data' => [
                'id' => isset($this->scanData['id']) ? $this->scanData['id'] : null,
                'bahan_id' => isset($this->scanData['bahan_id']) ? $this->scanData['bahan_id'] : null,
                'nama_bahan' => isset($this->scanData['nama_bahan']) ? $this->scanData['nama_bahan'] : null,
                'items' => isset($this->scanData['items']) ? $this->scanData['items'] : [],
                'total_harga' => isset($this->scanData['total_harga']) ? $this->scanData['total_harga'] : 0,
                'operator_name' => $this->operatorName,
                'scan_date' => now()->toDateTimeString(),
            ],
            'foto_nota_path' => $this->fotoNotaPath,
            'foto_nota_url' => $this->fotoNotaPath ? $base . '/storage/' . $this->fotoNotaPath : null,
        ];
    }
}