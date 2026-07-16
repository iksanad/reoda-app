<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$today = \Carbon\Carbon::today();
$contracts = \App\Models\LeaseContract::with('unit.property')->where('status', 'active')->get();
echo "Memeriksa " . $contracts->count() . " kontrak aktif:\n--------------------------\n";

foreach ($contracts as $c) {
    $dueDay = min(\Carbon\Carbon::parse($c->start_date)->day, $today->daysInMonth);
    $nextDueDate = $today->copy()->day($dueDay);
    if ($nextDueDate->isPast()) {
        $nextDueDate->addMonth();
        $dueDay = min(\Carbon\Carbon::parse($c->start_date)->day, $nextDueDate->daysInMonth);
        $nextDueDate->day($dueDay);
    }
    
    // Apakah ada invoice terlewat (bulan ini)
    $thisMonthDueDate = $today->copy()->day(min(\Carbon\Carbon::parse($c->start_date)->day, $today->daysInMonth));
    $generateDate = $thisMonthDueDate->copy()->subDays(5);
    
    $hasInvoiceThisMonth = \App\Models\Invoice::where('lease_contract_id', $c->id)
                            ->where('billing_month', $thisMonthDueDate->month)
                            ->where('billing_year', $thisMonthDueDate->year)
                            ->exists();
    
    echo "Kontrak ID: {$c->id} (Tgl Mulai: {$c->start_date->format('d M Y')})\n";
    echo "  - Jatuh Tempo Bulan Ini: {$thisMonthDueDate->format('d M Y')} (Harusnya digenerate tgl {$generateDate->format('d M Y')})\n";
    
    if ($today->startOfDay()->gte($generateDate->startOfDay())) {
        if ($hasInvoiceThisMonth) {
            echo "  - Status: OK (Tagihan sudah ada)\n";
        } else {
            echo "  - Status: TERLEWAT! (Harusnya tagihan sudah digenerate)\n";
        }
    } else {
        echo "  - Status: AMAN (Belum waktunya H-5)\n";
    }
    echo "--------------------------\n";
}
