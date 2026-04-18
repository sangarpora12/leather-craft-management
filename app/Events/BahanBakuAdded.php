<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\BahanBaku;
use Illuminate\Broadcasting\PrivateChannel;

class BahanBakuAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bahanBaku;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\BahanBaku $bahanBaku
     * @return void
     */
    public function __construct(BahanBaku $bahanBaku)
    {
        $this->bahanBaku = $bahanBaku;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-channel'), 
            new PrivateChannel('operator.' . $this->bahanBaku->user_id),
        ];
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'kode_material' => $this->bahanBaku->kode_material,
            'nama_material' => $this->bahanBaku->nama_material,
            'user_id' => $this->bahanBaku->user_id, 
            'total_transaksi' => $this->bahanBaku->stok * $this->bahanBaku->harga_satuan, 
        ];
    }
}