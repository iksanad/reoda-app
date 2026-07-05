<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaseContract;
use App\Models\Invoice;
use App\Models\Notification;
use Carbon\Carbon;

class TerminateExpiredKosContracts extends Command
{
    protected $signature   = 'reoda:terminate-expired-kos';
    protected $description = 'Terminate kos contracts where unpaid invoices have exceeded tolerance days';

    public function handle()
    {
        $today = Carbon::today();
        $terminated = 0;

        // Find all active kos contracts with unpaid/overdue invoices
        $contracts = LeaseContract::where('status', 'active')
            ->whereHas('unit.property', fn($q) => $q->where('type', 'kos'))
            ->with(['unit', 'tenant', 'unit.property'])
            ->get();

        foreach ($contracts as $contract) {
            $overdueInvoice = Invoice::where('lease_contract_id', $contract->id)
                ->whereIn('status', ['unpaid', 'pending'])
                ->where('due_date', '<', $today)
                ->orderBy('due_date')
                ->first();

            if (!$overdueInvoice) continue;

            $toleranceDays = $contract->tolerance_days ?? 7;
            $deadline = Carbon::parse($overdueInvoice->due_date)->addDays($toleranceDays);

            if ($today->greaterThan($deadline)) {
                // Terminate contract
                $contract->update([
                    'status'             => 'terminated',
                    'terminated_at'      => now(),
                    'termination_reason' => 'Kontrak dihentikan otomatis karena tagihan sewa tidak dibayar melewati batas toleransi (' . $toleranceDays . ' hari).',
                ]);

                // Free the unit
                $contract->unit->update(['status' => 'available']);

                // Mark overdue invoice as failed
                $overdueInvoice->update(['status' => 'failed']);

                // Notify tenant
                if ($contract->tenant) {
                    Notification::create([
                        'user_id'         => $contract->tenant_id,
                        'type'            => 'contract_terminated',
                        'title'           => '❌ Kontrak Sewa Dihentikan',
                        'message'         => 'Kontrak sewa Anda di ' . ($contract->unit->property->name ?? '') . ' unit ' .
                                             ($contract->unit->unit_code ?? '') . ' telah dihentikan otomatis karena pembayaran melewati batas toleransi ' .
                                             $toleranceDays . ' hari.',
                        'notifiable_type' => LeaseContract::class,
                        'notifiable_id'   => $contract->id,
                    ]);

                    $this->info("Terminated contract #{$contract->id} for tenant {$contract->tenant->email}");
                }

                $terminated++;
            }
        }

        $this->info("Contracts terminated today: {$terminated}");
        return self::SUCCESS;
    }
}
