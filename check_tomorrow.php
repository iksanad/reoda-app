<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

$tomorrow = Carbon::tomorrow(); // 2026-07-08

echo "=== PREDIKSI EKSEKUSI BESOK TANGGAL: " . $tomorrow->format('Y-m-d') . " ===\n\n";

// 1. CEK GENERATE INVOICES (H-5)
echo "[1] TAGIHAN YANG AKAN DIGENERATE BESOK (JAM 00:00)\n";
$targetDueDate = $tomorrow->copy()->addDays(5); // 2026-07-13
$contracts = \App\Models\LeaseContract::with('unit.property', 'tenant')->where('status', 'active')->get();
$generateCount = 0;

foreach ($contracts as $c) {
    $dueDay = min(Carbon::parse($c->start_date)->day, $targetDueDate->daysInMonth);
    
    if ($targetDueDate->day === $dueDay) {
        echo "- Kontrak ID: {$c->id} (Penyewa: {$c->tenant->name}, Unit: {$c->unit->unit_code})\n";
        echo "  Akan dibuatkan tagihan untuk jatuh tempo {$targetDueDate->format('d M Y')}\n";
        $generateCount++;
    }
}
if ($generateCount === 0) {
    echo "- TIDAK ADA tagihan yang akan digenerate besok.\n";
}

echo "\n-------------------------------------------------\n\n";

// 2. CEK EMAIL PENGINGAT (JAM 08:00)
echo "[2] EMAIL PENGINGAT YANG AKAN DIKIRIM BESOK (JAM 08:00)\n";

// H-3
$targetH3 = $tomorrow->copy()->addDays(3); // 2026-07-11
$invoicesH3 = \App\Models\Invoice::with('leaseContract.tenant', 'leaseContract.unit')->whereIn('status', ['unpaid', 'pending'])->whereDate('due_date', $targetH3)->get();
foreach ($invoicesH3 as $inv) {
    echo "- [H-3] Kontrak ID: {$inv->lease_contract_id} (Penyewa: {$inv->leaseContract->tenant->name})\n";
    echo "  Email akan dikirim (Jatuh tempo {$targetH3->format('d M Y')})\n";
}

// H-1
$targetH1 = $tomorrow->copy()->addDays(1); // 2026-07-09
$invoicesH1 = \App\Models\Invoice::with('leaseContract.tenant', 'leaseContract.unit')->whereIn('status', ['unpaid', 'pending'])->whereDate('due_date', $targetH1)->get();
foreach ($invoicesH1 as $inv) {
    echo "- [H-1] Kontrak ID: {$inv->lease_contract_id} (Penyewa: {$inv->leaseContract->tenant->name})\n";
    echo "  Email akan dikirim (Jatuh tempo {$targetH1->format('d M Y')})\n";
}

// Hari H
$invoicesH = \App\Models\Invoice::with('leaseContract.tenant', 'leaseContract.unit')->whereIn('status', ['unpaid', 'pending'])->whereDate('due_date', $tomorrow)->get();
foreach ($invoicesH as $inv) {
    echo "- [HARI H] Kontrak ID: {$inv->lease_contract_id} (Penyewa: {$inv->leaseContract->tenant->name})\n";
    echo "  Email akan dikirim (Jatuh tempo HARI H: {$tomorrow->format('d M Y')})\n";
}

// Overdue Warning
$overdueInvoices = \App\Models\Invoice::with('leaseContract.tenant')->whereIn('status', ['unpaid', 'pending'])->where('due_date', '<', $tomorrow)->get();
$overdueCount = 0;
foreach ($overdueInvoices as $inv) {
    $tolerance = $inv->leaseContract->tolerance_days ?? 7;
    $deadline = Carbon::parse($inv->due_date)->addDays($tolerance);
    if ($tomorrow->diffInDays($deadline, false) === 1) {
        echo "- [OVERDUE H-1] Kontrak ID: {$inv->lease_contract_id} (Penyewa: {$inv->leaseContract->tenant->name})\n";
        echo "  Email Peringatan Putus Kontrak (Toleransi habis lusa: {$deadline->format('d M Y')})\n";
        $overdueCount++;
    }
}

if ($invoicesH3->count() == 0 && $invoicesH1->count() == 0 && $invoicesH->count() == 0 && $overdueCount == 0) {
    echo "- TIDAK ADA email pengingat yang akan dikirim besok.\n";
}
echo "\n";
