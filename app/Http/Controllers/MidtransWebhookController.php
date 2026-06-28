<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\WalletTransaction;
use App\Models\Setting;
use App\Services\NotificationService;
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

        // Midtrans URL test sends an empty POST — respond 200 so the test passes
        $body = $request->getContent();
        if (empty($body) || $body === '{}' || !$request->has('order_id')) {
            Log::info('Midtrans webhook: received ping/test request, responding OK.');
            return response()->json(['message' => 'OK'], 200);
        }

        // Midtrans dashboard "Test URL" sends a dummy notification with order_id
        // starting with "payment_notif_test_" — skip processing, just return 200
        $orderId = $request->input('order_id', '');
        if (str_starts_with($orderId, 'payment_notif_test_')) {
            Log::info('Midtrans webhook: received dashboard test notification, responding OK.');
            return response()->json(['message' => 'OK'], 200);
        }

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

        // Fallback: parse invoice_id from order_id format "INV-{id}-{timestamp}"
        if (!$payment && preg_match('/^INV-(\d+)-\d+$/', $orderId, $matches)) {
            $payment = Payment::where('invoice_id', $matches[1])
                ->where('status', 'pending')
                ->whereNull('midtrans_transaction_id')
                ->latest()
                ->first();
            // Update the payment record with the correct order_id
            if ($payment) {
                $payment->update(['midtrans_order_id' => $orderId]);
            }
        }

        if (!$payment) {
            Log::warning("Midtrans webhook: payment not found for order_id={$orderId}");
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

                // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
                // noRetry=true: webhook must respond quickly, email will be retried via Superadmin if failed
                if ($invoice->tenant) {
                    app(NotificationService::class)->send(
                        $invoice->tenant,
                        'Pembayaran Berhasil ✅',
                        'Pembayaran untuk invoice ' . $invoice->invoice_number . ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') . ' telah berhasil dikonfirmasi.',
                        'payment_received',
                        route('tenant.invoices.index'),
                        $payment,
                        true  // noRetry
                    );
                }

                // Notify manager via NotificationService
                // noRetry=true: webhook must respond quickly
                if ($manager) {
                    app(NotificationService::class)->send(
                        $manager,
                        'Pembayaran Diterima dari ' . ($invoice->tenant->name ?? 'Penyewa'),
                        'Pembayaran invoice ' . $invoice->invoice_number . ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') . ' dari ' . ($invoice->tenant->name ?? '') . ' telah diterima.',
                        'payment_manager_received',
                        route('manager.payments.index'),
                        $payment,
                        true  // noRetry
                    );
                }

            } elseif ($transactionStatus === 'pending' && !in_array($invoice->status, ['paid'])) {
                // User has chosen a payment method (e.g. bank transfer/VA) but hasn't paid yet
                $invoice->update(['status' => 'pending']);

            } elseif ($isFailed && !in_array($invoice->status, ['paid'])) {
                // Payment cancelled/expired/denied — reset invoice so tenant can retry
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
