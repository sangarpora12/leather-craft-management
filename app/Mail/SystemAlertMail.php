<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $title;
    public $message;
    public $data;
    public $buttonText;
    public $buttonUrl;
    public $alertType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $title, string $message, array $data = [], string $buttonText = null, string $buttonUrl = null, string $alertType = 'info')
    {
        $this->subject = $subject;
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->buttonText = $buttonText;
        $this->buttonUrl = $buttonUrl;
        $this->alertType = $alertType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->markdown('emails.system.alert', [
                        'title' => $this->title,
                        'message' => $this->message,
                        'data' => $this->data,
                        'buttonText' => $this->buttonText,
                        'buttonUrl' => $this->buttonUrl,
                        'alertType' => $this->alertType,
                    ]);
    }
}