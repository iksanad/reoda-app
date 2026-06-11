<?php

namespace App\Mail;

use App\Models\LeaseContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public LeaseContract $contract;

    public function __construct(LeaseContract $contract)
    {
        $this->contract = $contract;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[REODA] Pengajuan Kontrak Sewa Baru');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contract-requested');
    }
}
