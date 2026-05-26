<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScanNotaBulkNotification extends Notification
{
    use Queueable;

    protected $operatorName;
    protected $supplier;
    protected $noNota;
    protected $items;
    protected $totalHarga;
    protected $fotoNotaUrl;
    protected $tanggal;

    /**
     * Create a new notification instance.
     *
     * @param string $operatorName
     * @param string $supplier
     * @param string $noNota
     * @param array $items [{nama, warna, qty, satuan, harga}]
     * @param float $totalHarga
     * @param string|null $fotoNotaUrl
     */
    public function __construct(
        string $operatorName,
        string $supplier,
        string $noNota,
        array $items,
        float $totalHarga = 0,
        ?string $fotoNotaUrl = null
    ) {
        $this->operatorName = $operatorName;
        $this->supplier = $supplier;
        $this->noNota = $noNota;
        $this->items = $items;
        $this->totalHarga = $totalHarga;
        $this->fotoNotaUrl = $fotoNotaUrl;
        $this->tanggal = now()->isoFormat('dddd, D MMMM YYYY [Pukul] HH:mm');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('📸 Scan Nota - ' . $this->supplier)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line("Operator **{$this->operatorName}** telah melakukan scan nota dari **{$this->supplier}**.")
            ->line('');

        // Info nota
        $mail->line('**📋 Informasi Nota:**');
        $mail->line("- Toko: **{$this->supplier}**");
        $mail->line("- No. Nota: **" . ($this->noNota ?: '-') . "**");
        $mail->line("- Tanggal Scan: **{$this->tanggal}**");
        $mail->line('');

        // Daftar item dengan harga
        $mail->line('**📦 Detail Barang Masuk:**');
        $no = 1;
        $grandTotal = 0;
        foreach ($this->items as $item) {
            $warna = $item['warna'] ?? 'Tidak Ada Warna';
            $qty = $item['qty'] ?? 0;
            $satuan = $item['satuan'] ?? 'Pcs';
            $hargaSatuan = $item['harga'] ?? 0;
            $subtotal = $hargaSatuan * $qty;
            $grandTotal += $subtotal;
            $nama = $item['nama'] ?? '-';
            $mail->line("{$no}. **{$nama}**");
            $mail->line("   🎨 Warna: {$warna}");
            if ($hargaSatuan > 0) {
                $mail->line("   💰 Rp " . number_format($hargaSatuan, 0, ',', '.') . " x {$qty} {$satuan} = **Rp " . number_format($subtotal, 0, ',', '.') . "**");
            } else {
                $mail->line("   📦 {$qty} {$satuan}");
            }
            $no++;
        }
        $mail->line('');

        // Grand total
        if ($grandTotal > 0) {
            $mail->line('**💰 Total Keseluruhan: Rp ' . number_format($grandTotal, 0, ',', '.') . '**');
            $mail->line('');
        }

        // Foto nota
        if ($this->fotoNotaUrl) {
            $mail->line('**📸 Foto Nota:**');
            $mail->line("![Foto Nota]({$this->fotoNotaUrl})");
            $mail->line('');
        }

        $mail->action('📋 Lihat Stok Bahan', route('admin.bahan.index'))
            ->line('Data bahan sudah tersimpan di sistem. Silakan cek stok untuk verifikasi.');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $itemList = [];
        $grandTotal = 0;
        foreach ($this->items as $item) {
            $harga = $item['harga'] ?? 0;
            $qty = $item['qty'] ?? 0;
            $subtotal = $harga * $qty;
            $grandTotal += $subtotal;
            $itemList[] = [
                'nama' => $item['nama'] ?? '-',
                'warna' => $item['warna'] ?? 'Tidak Ada Warna',
                'qty' => $qty,
                'satuan' => $item['satuan'] ?? 'Pcs',
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        $total = $this->totalHarga > 0 ? $this->totalHarga : $grandTotal;

        // Ringkasan pendek: "2 item - Total: Rp 150.000"
        $ringkasan = count($this->items) . " item";
        if ($total > 0) {
            $ringkasan .= " - Total: Rp " . number_format($total, 0, ',', '.');
        }

        return [
            'judul' => '📸 Scan Nota - ' . $this->supplier,
            'pesan' => "{$this->operatorName} scan nota dari {$this->supplier}. {$ringkasan}",
            'url' => route('admin.bahan.index'),
            'icon' => 'fa-camera',
            'detail' => [
                'operator' => $this->operatorName,
                'supplier' => $this->supplier,
                'no_nota' => $this->noNota,
                'total_harga' => $total,
                'tanggal' => $this->tanggal,
                'items' => $itemList,
            ],
            'foto_nota_url' => $this->fotoNotaUrl,
        ];
    }
}