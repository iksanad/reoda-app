<?php

namespace App\Mail;

use App\Models\LeaseContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public LeaseContract $contract;
    public bool $isApproved;

    public function __construct(LeaseContract $contract, bool $isApproved = true)
    {
        $this->contract   = $contract;
        $this->isApproved = $isApproved;
    }

    public function envelope(): Envelope
    {
        $subject = $this->isApproved
            ? '🎉 Kontrak Sewa Anda Telah Disetujui'
            : 'Pengajuan Kontrak Sewa Ditolak';

        return new Envelope(subject: '[REODA] ' . $subject);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contract-approved');
    }
}
