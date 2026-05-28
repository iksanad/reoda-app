<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        $invoice  = $this->payment->invoice;
        $tenant   = $invoice->tenant ?? $this->payment->tenant;
        $subject  = "Pembayaran Masuk dari {$tenant->name} — Menunggu Konfirmasi";

        return $this->subject($subject)
                    ->view('emails.payment-received');
    }
}
