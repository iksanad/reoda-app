<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Models\Notification;
use Midtrans\Config;
use Midtrans\Notification as MidtransNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notif = new MidtransNotification();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error parsing notification'], 400);
        }

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        // Order ID format: INV-RENT-YYYYMM-XXXXX-1234567890
        // We need to extract the invoice_number by removing the last hyphen and timestamp
        $invoiceNumber = substr($orderId, 0, strrpos($orderId, '-'));
        
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        if (!$invoice) {
            Log::error('Midtrans Webhook Error: Invoice not found for order_id ' . $orderId);
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Avoid double processing
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice already paid'], 200);
        }

        DB::beginTransaction();
        try {
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'challenge') {
                    // TODO: Handle challenge by manual review
                } else {
                    // Payment successful
                    $invoice->update(['status' => 'paid']);

                    // Calculate Split: The manager gets exactly $invoice->amount (the rent price).
                    // The 14000 admin+gateway fee was added ON TOP of the invoice amount during checkout.
                    $managerId = $invoice->manager_id;
                    $manager = User::find($managerId);

                    if ($manager) {
                        // 1. Catat transaksi dompet (Wallet Transaction)
                        WalletTransaction::create([
                            'user_id' => $managerId,
                            'type' => 'credit',
                            'amount' => $invoice->amount,
                            'reference_id' => $orderId,
                            'description' => 'Pembayaran lunas: ' . $invoice->invoice_number,
                        ]);

                        // 2. Tambah saldo pengelola
                        $manager->increment('balance', $invoice->amount);

                        // 3. Notifikasi ke pengelola
                        Notification::create([
                            'user_id' => $managerId,
                            'type' => 'wallet_credit',
                            'title' => 'Saldo Masuk: Rp ' . number_format($invoice->amount, 0, ',', '.'),
                            'message' => 'Penyewa telah melunasi tagihan ' . $invoice->invoice_number . '. Saldo ini sekarang tersedia di dompet Anda.',
                        ]);
                    }

                    // Referral & Discount Logic
                    $tenant = User::find($invoice->tenant_id);
                    if ($tenant) {
                        // 1. Kurangi kuota diskon penyewa jika dia memilikinya (asumsinya dipakai di transaksi ini)
                        if ($tenant->discount_quota > 0) {
                            $tenant->decrement('discount_quota');
                        }

                        // 2. Cek apakah ini pembayaran sukses pertamanya
                        if (!$tenant->has_made_first_payment) {
                            $tenant->update(['has_made_first_payment' => true]);

                            // 3. Beri hadiah ke pengundang (jika ada)
                            if ($tenant->referred_by) {
                                $referrer = User::find($tenant->referred_by);
                                if ($referrer) {
                                    $referrer->increment('discount_quota');

                                    Notification::create([
                                        'user_id' => $referrer->id,
                                        'type' => 'referral_bonus',
                                        'title' => 'Bonus Voucher Referral!',
                                        'message' => 'Teman yang Anda undang (' . $tenant->name . ') telah melakukan pembayaran pertamanya. Anda mendapatkan 1 Voucher Diskon Rp 50.000!',
                                    ]);
                                }
                            }
                        }
                    }

                    // Notifikasi ke penyewa
                    Notification::create([
                        'user_id' => $invoice->tenant_id,
                        'type' => 'payment_success',
                        'title' => 'Pembayaran Berhasil',
                        'message' => 'Terima kasih, tagihan ' . $invoice->invoice_number . ' telah berhasil dilunasi melalui Midtrans.',
                    ]);
                }
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Payment failed or expired
                // No need to change invoice status to unpaid if it is already unpaid, just notify or log
            } else if ($transactionStatus == 'pending') {
                // Waiting for customer to pay
                // Can optionally change status to pending or leave as unpaid
            }

            DB::commit();
            return response()->json(['message' => 'Webhook handled successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Webhook DB Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
