<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\User;

class PengadaanMaterialNotification extends Notification
{
    use Queueable;

    protected $pengadaans; // Mengubah nama menjadi jamak
    protected $admin;

    /**
     * Create a new notification instance.
     *
     * @param \Illuminate\Support\Collection $pengadaans Koleksi objek Pengadaan
     * @param \App\Models\User $admin User admin yang membuat
     */
    public function __construct(Collection $pengadaans, User $admin)
    {
        $this->pengadaans = $pengadaans;
        $this->admin = $admin;
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
        $url = route('operator.pengadaan.list');
        $refGroup = $this->pengadaans->first()->no_referensi ?? 'MULTI-ITEM';
        $adminName = $this->admin->name;

        $mail = (new MailMessage)
            ->subject('📦 [PERINTAH PENGADAAN] ' . $refGroup)
            ->greeting('Halo, Tim Operator!')
            ->line("Admin **{$adminName}** telah menerbitkan perintah pengadaan baru untuk beberapa material sekaligus.")
            ->line('');

        // Membuat tabel dari semua item pengadaan
        $mail->line('**DAFTAR MATERIAL YANG HARUS DIADAKAN:**');
        $mail->line('');
        $mail->line('| Nama Bahan | Jumlah | Prioritas | Tgl. Butuh |');
        $mail->line('|:---|:---|:---|:---|');
        foreach ($this->pengadaans as $pengadaan) {
            $namaBahan = $pengadaan->bahan->nama_material ?? 'N/A';
            $jumlah = (float)$pengadaan->jumlah . ' ' . ($pengadaan->bahan->satuan ?? 'pcs');
            $prioritas = $pengadaan->prioritas == 'URGENT' ? '🔴 URGENT' : '🟢 BIASA';
            $tanggal = \Carbon\Carbon::parse($pengadaan->tanggal_dibutuhkan)->format('d M Y');
            $mail->line("| {$namaBahan} | {$jumlah} | {$prioritas} | {$tanggal} |");
        }
        $mail->line('');

        // Catatan dari admin (ambil dari item pertama, diasumsikan sama)
        $catatan = $this->pengadaans->first()->catatan;
        if ($catatan) {
            $mail->line('**📋 Catatan dari Admin:**');
            $mail->line("> {$catatan}");
            $mail->line('');
        }

        $mail->line('Silakan segera lakukan pembelian material sesuai dengan daftar di atas.');
        $mail->line('');
        $mail->action('📦 LIHAT DAFTAR PENGADAAN', $url)
            ->line('Jika belum login, Anda akan diarahkan ke halaman login operator terlebih dahulu.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $refGroup = $this->pengadaans->first()->no_referensi ?? 'MULTI-ITEM';
        $itemCount = $this->pengadaans->count();

        return [
            'judul' => '📦 [PENGADAAN] ' . $refGroup,
            'pesan' => "Admin {$this->admin->name} memerintahkan pengadaan untuk {$itemCount} material.",
            'url' => route('operator.pengadaan.list'),
            'icon' => 'shopping-cart',
            'type' => 'pengadaan_material_multi',
            'pengadaan_group' => $refGroup,
            'admin_name' => $this->admin->name,
        ];
    }
}