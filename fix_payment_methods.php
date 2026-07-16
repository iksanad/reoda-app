<?php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

Config::$serverKey    = config('services.midtrans.server_key');
Config::$isProduction = config('services.midtrans.is_production');

$payments = Payment::where(function($q) {
    $q->where('payment_method', 'midtrans')
      ->orWhere('payment_method', 'Midtrans')
      ->orWhereNull('payment_method');
})->whereNotNull('midtrans_order_id')->get();

$count = 0;
foreach ($payments as $payment) {
    try {
        $status = Transaction::status($payment->midtrans_order_id);
        
        $paymentType = $status->payment_type ?? null;
        if (!$paymentType) continue;

        $methodName = $paymentType;
        if ($methodName === 'bank_transfer' && isset($status->va_numbers[0]->bank)) {
            $methodName = 'VA ' . strtoupper($status->va_numbers[0]->bank);
        } elseif ($methodName === 'echannel') {
            $methodName = 'VA Mandiri';
        } elseif ($methodName === 'cstore' && isset($status->store)) {
            $methodName = ucfirst($status->store);
        } else {
            $methodName = ucwords(str_replace('_', ' ', $methodName));
        }

        $payment->payment_method = $methodName;
        $payment->save();
        $count++;
        echo "Updated payment {$payment->id} to {$methodName}\n";
    } catch (\Exception $e) {
        echo "Error on payment {$payment->id}: " . $e->getMessage() . "\n";
    }
}
echo "Total updated: $count\n";
