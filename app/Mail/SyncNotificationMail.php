<?php

namespace App\Mail;

use App\Models\AppNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SyncNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AppNotification $notification;

    public function __construct(AppNotification $notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        return $this->subject('Pemberitahuan REODA: ' . $this->notification->title)
                    ->view('emails.sync-notification');
    }
}
