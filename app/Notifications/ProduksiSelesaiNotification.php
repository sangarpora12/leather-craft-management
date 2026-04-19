<?php

namespace App\Notifications;

use App\Models\Produksi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProduksiSelesaiNotification extends Notification
{
    use Queueable;

    /**
     * @param Produksi $produksi Object Produksi yang sudah selesai
     * @param  'admin'|'operator'  $audience
     */
    public function __construct(public Produksi $produksi, public string $audience = 'admin')
    {
        // Pastikan relasi terload sebelum disimpan ke notifikasi
        $produksi->loadMissing(['product', 'operator']);
        $this->produksi = $produksi;
    }

public function via(object $notifiable): array
{
    // Simpan ke database (untuk lonceng notifikasi)
    return ['database'];
}

    public function toDatabase(object $notifiable): array
    {
        $namaProduk = $this->produksi->product?->nama_produk ?? 'Produk';
        $kodeSpk = $this->produksi->kode_spk ?? '-';
        $qty = (float) ($this->produksi->jumlah_produksi ?? 0);
        $operator = $this->produksi->operator?->name ?? 'Operator';
        $catatan = trim((string) ($this->produksi->catatan ?? ''));
        $fotoBukti = $this->produksi->foto_bukti ?? null;

        $catatanText = $catatan !== '' && $catatan !== '-' ? "Catatan: {$catatan}" : 'Catatan: -';

        $judul = $this->audience === 'operator' ? 'Tugas Selesai' : 'Produksi Selesai';
        $pesan = $this->audience === 'operator'
            ? "Tugas kamu untuk {$namaProduk} ({$kodeSpk}) sudah ditandai SELESAI. Hasil produksi: {$qty} pcs. {$catatanText}"
            : "Produksi {$namaProduk} ({$kodeSpk}) telah SELESAI oleh {$operator}. Stok bertambah {$qty} pcs. {$catatanText}";

        return [
            'judul' => $judul,
            'pesan' => $pesan,
            // URL diputuskan dari audience, bukan dari role notifiable
            'url' => $this->audience === 'operator'
                ? route('operator.tugas.index')
                : route('admin.produksi.review', $this->produksi->id) . '?from=notification',
            'produksi_id' => $this->produksi->id,
            'foto_bukti' => $fotoBukti,
        ];
    }

}