<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaseContract;
use App\Models\Invoice;

class ProcessExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses kontrak yang telah kedaluwarsa dan tagihan tertunggak';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to process expired contracts...');

        // 1. Find active contracts whose end_date is past today
        $expiredContracts = LeaseContract::where('status', 'active')
            ->where('end_date', '<', now()->toDateString())
            ->get();

        $countContracts = 0;
        foreach ($expiredContracts as $contract) {
            $contract->update(['status' => 'expired']);

            // Set unit to available
            if ($contract->unit) {
                $contract->unit->update(['status' => 'available']);
            }
            $countContracts++;
        }

        $this->info("Processed {$countContracts} expired contracts.");

        // 2. Find unpaid invoices whose due_date is past today
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->get();

        $countInvoices = 0;
        foreach ($overdueInvoices as $invoice) {
            $invoice->update(['status' => 'overdue']);
            $countInvoices++;
        }

        $this->info("Processed {$countInvoices} overdue invoices.");

        return Command::SUCCESS;
    }
}
