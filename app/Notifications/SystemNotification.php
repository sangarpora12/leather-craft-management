<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $type; // 'success', 'warning', 'info'
    public $url;
    public $target; // 'admin', 'operator', or 'all'

    public function __construct($title, $message, $type = 'info', $url = '#', $target = 'all')
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->url = $url;
        $this->target = $target;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke database
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'target' => $this->target,
        ];
    }
}