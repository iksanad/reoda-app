<?php
use App\Models\Payment;

$total = Payment::whereIn('status', ['verified', 'approved'])->sum('platform_fee');
$count = Payment::whereIn('status', ['verified', 'approved'])->count();

echo "Approved/verified payments count: " . $count . "\n";
echo "Total platform_fee sum: Rp " . number_format($total, 0, ',', '.') . "\n\n";

$samples = Payment::whereIn('status', ['verified', 'approved'])->take(5)->get(['id', 'status', 'amount', 'platform_fee', 'gateway_fee', 'payment_method']);
foreach ($samples as $p) {
    echo "ID={$p->id} status={$p->status} amount={$p->amount} platform_fee={$p->platform_fee} gateway_fee={$p->gateway_fee} method={$p->payment_method}\n";
}

// Also check all payments
echo "\n--- ALL PAYMENTS ---\n";
$all = Payment::all(['id', 'status', 'amount', 'platform_fee']);
foreach ($all as $p) {
    echo "ID={$p->id} status={$p->status} platform_fee={$p->platform_fee}\n";
}
