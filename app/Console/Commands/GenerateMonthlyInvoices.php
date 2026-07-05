<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaseContract;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'reoda:generate-invoices';
    protected $description = 'Automatically generate monthly rent and IPL invoices 7 days before due date';

    public function handle()
    {
        $today = Carbon::today();
        $daysBefore = 5;
        $targetDate = $today->copy()->addDays($daysBefore);

        $this->info("Running auto-invoice generation.");
        $this->info("Target Due Date (H-{$daysBefore}): " . $targetDate->format('Y-m-d'));

        // Get all active contracts
        $contracts = LeaseContract::with('unit.property')->where('status', 'active')->get();
        $generatedCount = 0;

        foreach ($contracts as $contract) {
            // Check if contract has an end_date and targetDate is beyond it
            if ($contract->end_date && $targetDate->copy()->startOfDay()->gt(Carbon::parse($contract->end_date)->startOfDay())) {
                continue; // Contract is ending, don't generate rent for dates past the end date
            }

            // Calculate expected due day for this month
            $startDay = Carbon::parse($contract->start_date)->day;
            $daysInMonth = $targetDate->daysInMonth;
            $dueDay = min($startDay, $daysInMonth);

            // Is today exactly H-7 for this contract's next due date?
            if ($targetDate->day === $dueDay) {
                // Determine billing month and year
                $billingMonth = $targetDate->month;
                $billingYear = $targetDate->year;

                // 1. Generate Rent Invoice
                $this->generateInvoice($contract, 'rent', $contract->rent_amount, $billingMonth, $billingYear, $targetDate, $generatedCount);

                // 2. Generate IPL Invoice (Only for apartemen and if ipl_amount > 0)
                $property = $contract->unit->property ?? null;
                if ($property && $property->type === 'apartemen' && $property->ipl_amount > 0) {
                    $this->generateInvoice($contract, 'ipl', $property->ipl_amount, $billingMonth, $billingYear, $targetDate, $generatedCount);
                }
            }
        }

        $this->info("Finished. Generated {$generatedCount} invoices.");
        return self::SUCCESS;
    }

    private function generateInvoice(LeaseContract $contract, string $type, float $amount, int $month, int $year, Carbon $dueDate, int &$generatedCount)
    {
        // Check if invoice already exists
        $exists = Invoice::where('lease_contract_id', $contract->id)
            ->where('type', $type)
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->exists();

        if (!$exists) {
            // Generate unique invoice number
            $invNumber = 'INV-' . strtoupper(uniqid()) . '-' . rand(100, 999);
            
            Invoice::create([
                'invoice_number' => $invNumber,
                'lease_contract_id' => $contract->id,
                'tenant_id' => $contract->tenant_id,
                'manager_id' => $contract->manager_id,
                'type' => $type,
                'billing_month' => $month,
                'billing_year' => $year,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'unpaid',
                'notes' => 'Tagihan otomatis dibuat oleh sistem.'
            ]);
            $generatedCount++;
            $this->info("Generated $type invoice for Contract #{$contract->contract_number} (Period: $month/$year)");
        }
    }
}
