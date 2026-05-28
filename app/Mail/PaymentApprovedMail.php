<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;
    public string  $action; // 'approved' | 'rejected'
    public ?string $notes;

    public function __construct(Payment $payment, string $action, ?string $notes = null)
    {
        $this->payment = $payment;
        $this->action  = $action;
        $this->notes   = $notes;
    }

    public function build()
    {
        $label   = $this->action === 'approved' ? 'Dikonfirmasi ✅' : 'Ditolak ❌';
        $subject = "Pembayaran Anda {$label} — REODA";

        return $this->subject($subject)
                    ->view('emails.payment-approved');
    }
}
