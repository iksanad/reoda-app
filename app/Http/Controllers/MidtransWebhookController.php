<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\WalletTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Setup Midtrans
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans webhook parse error: ' . $e->getMessage());
            return response()->json(['message' => 'Bad signature'], 403);
        }

        $orderId           = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus       = $notification->fraud_status ?? null;

        Log::info("Midtrans Webhook: order_id={$orderId}, status={$transactionStatus}, fraud={$fraudStatus}");

        // Find payment by order_id
        $payment = Payment::where('midtrans_order_id', $orderId)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $invoice = $payment->invoice;

        DB::beginTransaction();
        try {
            $isSettled = in_array($transactionStatus, ['settlement', 'capture'])
                && ($fraudStatus === null || $fraudStatus === 'accept');

            $isFailed = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure']);

            if ($isSettled && $invoice->status !== 'paid') {
                // Mark payment approved
                $payment->update([
                    'status'                 => 'approved',
                    'midtrans_transaction_id' => $notification->transaction_id ?? null,
                    'verified_at'            => now(),
                    'paid_at'                => now(),
                ]);

                // Mark invoice paid
                $invoice->update(['status' => 'paid']);

                // Credit manager wallet (amount net of platform fee)
                $netAmount = $payment->amount - ($payment->platform_fee ?? 0);
                $manager   = $invoice->manager;
                if ($manager) {
                    $balanceBefore = $manager->balance ?? 0;
                    $manager->increment('balance', $netAmount);

                    WalletTransaction::create([
                        'user_id'       => $manager->id,
                        'type'          => 'credit',
                        'amount'        => $netAmount,
                        'reference_id'  => $payment->payment_code,
                        'description'   => 'Pembayaran invoice ' . $invoice->invoice_number . ' dari penyewa ' . ($invoice->tenant->name ?? ''),
                        'balance_after' => $balanceBefore + $netAmount,
                    ]);
                }

                // Notify tenant
                \App\Models\Notification::create([
                    'user_id'         => $invoice->tenant_id,
                    'notifiable_type' => \App\Models\User::class,
                    'notifiable_id'   => $invoice->tenant_id,
                    'type'            => 'payment_received',
                    'title'           => 'Pembayaran Berhasil ✅',
                    'message'         => 'Pembayaran untuk invoice ' . $invoice->invoice_number . ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') . ' telah berhasil dikonfirmasi.',
                ]);

                // Send email confirmation
                try {
                    if ($invoice->tenant && $invoice->tenant->email) {
                        \Illuminate\Support\Facades\Mail::to($invoice->tenant->email)
                            ->send(new \App\Mail\PaymentApprovedMail($payment, 'approved'));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
                }

            } elseif ($isFailed && !in_array($invoice->status, ['paid'])) {
                $payment->update(['status' => 'rejected']);
                $invoice->update(['status' => 'unpaid']);
            }

            DB::commit();
            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Midtrans webhook processing error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
