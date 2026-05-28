<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User    $manager;
    public string  $action; // 'approved' | 'rejected'
    public ?string $notes;

    public function __construct(User $manager, string $action, ?string $notes = null)
    {
        $this->manager = $manager;
        $this->action  = $action;
        $this->notes   = $notes;
    }

    public function build()
    {
        $label = 'Ditolak ❌';
        if ($this->action === 'approved') {
            $label = 'Disetujui ✅';
        } elseif ($this->action === 'revoked') {
            $label = 'Dicabut ⚠️';
        }

        $subject = "Pendaftaran Pengelola REODA {$label}";

        return $this->subject($subject)
                    ->view('emails.manager-approved');
    }
}
