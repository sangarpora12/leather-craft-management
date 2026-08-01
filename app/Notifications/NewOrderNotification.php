<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'judul' => 'Pesanan Baru Masuk',
            'pesan' => 'Pesanan ' . $this->order->order_number . ' sebesar Rp ' . number_format($this->order->total_amount, 0, ',', '.') . ' dari ' . $this->order->shipping_name,
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'url' => route('admin.penjualan.show', $this->order->id),
        ];
    }
}