<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\LeaseContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public LeaseContract $contract;
    public string $type; // 'due_today' | 'overdue_warning' | 'contract_terminated'
    public ?Carbon $deadline;

    public function __construct(Invoice $invoice, LeaseContract $contract, string $type = 'due_today', ?Carbon $deadline = null)
    {
        $this->invoice  = $invoice;
        $this->contract = $contract;
        $this->type     = $type;
        $this->deadline = $deadline;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'due_today'           => '🔔 Tagihan Sewa Jatuh Tempo Hari Ini',
            'overdue_warning'     => '⚠️ Peringatan: Tagihan Hampir Melewati Batas Toleransi',
            'contract_terminated' => '❌ Kontrak Sewa Anda Telah Dihentikan',
            default               => 'Notifikasi Tagihan Sewa — REODA',
        };

        return new Envelope(subject: '[REODA] ' . $subject);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.payment-reminder');
    }
}
