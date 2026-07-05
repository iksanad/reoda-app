<?php
use App\Models\Invoice;
use Carbon\Carbon;

$today = Carbon::today();
echo "Today: " . $today->toDateString() . "\n\n";

// Check upcoming invoices (H-3 and H-1)
foreach ([3, 1] as $days) {
    $targetDate = $today->copy()->addDays($days);
    $count = Invoice::whereIn('status', ['unpaid', 'pending'])
        ->whereDate('due_date', $targetDate)
        ->count();
    echo "H-{$days} (due {$targetDate->toDateString()}): {$count} invoices\n";
}

// Check due today
$dueToday = Invoice::whereIn('status', ['unpaid', 'pending'])
    ->whereDate('due_date', $today)
    ->count();
echo "\nDue today: {$dueToday} invoices\n";

// Check overdue
$overdue = Invoice::whereIn('status', ['unpaid', 'pending'])
    ->where('due_date', '<', $today)
    ->with(['leaseContract'])
    ->get();
echo "Overdue total: " . $overdue->count() . " invoices\n\n";

// Check which ones would get $daysLeft === 1
$warningCount = 0;
foreach ($overdue as $invoice) {
    $contract = $invoice->leaseContract;
    if (!$contract) continue;
    $toleranceDays = $contract->tolerance_days ?? 7;
    $deadline = Carbon::parse($invoice->due_date)->addDays($toleranceDays);
    $daysLeft = $today->diffInDays($deadline, false);
    if ($daysLeft === 1) {
        $warningCount++;
        echo "  → Invoice #{$invoice->id} due {$invoice->due_date}, deadline {$deadline->toDateString()}, daysLeft={$daysLeft}\n";
    }
}
echo "Overdue warnings to send: {$warningCount}\n\n";

// Check Windows Task Scheduler / CRON running
echo "NOTE: Laravel Scheduler needs 'php artisan schedule:run' to be triggered by Windows Task Scheduler or CRON every minute.\n";
