<?php

namespace Database\Seeders;

use App\Models\LeaseContract;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        $contracts = LeaseContract::where('status', 'active')->with(['tenant', 'unit', 'manager'])->get();

        if ($contracts->isEmpty()) {
            $this->command->warn('Tidak ada kontrak aktif. Skip InvoiceSeeder.');
            return;
        }

        foreach ($contracts as $contract) {
            $start = $contract->start_date;
            $now   = now();

            // Buat invoice untuk setiap bulan dari start hingga bulan ini
            $current = $start->copy()->startOfMonth();

            while ($current <= $now->copy()->startOfMonth()) {
                $dueDate     = $current->copy()->addDays(10); // jatuh tempo tanggal 10
                $isOverdue   = $dueDate->isPast();
                $isPaid      = $current < $now->copy()->subMonth(); // bulan sebelumnya sudah bayar
                $isPending   = !$isPaid && $current->month === $now->subMonth()->month;

                $status = 'unpaid';
                if ($isPaid) $status = 'paid';
                if ($isPending) $status = 'pending_verification';

                $invoice = Invoice::updateOrCreate(
                    [
                        'lease_contract_id' => $contract->id,
                        'billing_month'     => $current->month,
                        'billing_year'      => $current->year,
                        'type'              => 'rent',
                    ],
                    [
                        'invoice_number' => 'INV-' . $current->format('Ym') . '-' . strtoupper(Str::random(6)),
                        'tenant_id'      => $contract->tenant_id,
                        'manager_id'     => $contract->manager_id,
                        'amount'         => $contract->rent_amount,
                        'due_date'       => $dueDate,
                        'status'         => $status,
                    ]
                );

                // Buat payment untuk invoice yang paid
                if ($status === 'paid') {
                    Payment::updateOrCreate(
                        ['invoice_id' => $invoice->id, 'tenant_id' => $contract->tenant_id],
                        [
                            'payment_code'     => 'PAY-' . strtoupper(Str::random(10)),
                            'manager_id'       => $contract->manager_id,
                            'amount'           => $contract->rent_amount,
                            'payment_method'   => collect(['transfer', 'transfer', 'cash'])->random(),
                            'bank_name'        => 'BCA',
                            'bank_account'     => '1234567890',
                            'proof_of_payment' => null,
                            'status'           => 'approved',
                            'paid_at'          => $dueDate->copy()->subDays(rand(0, 5)),
                            'verified_at'      => $dueDate->copy()->subDays(rand(0, 3)),
                            'verified_by'      => $contract->manager_id,
                        ]
                    );
                }

                // Buat PLN invoice juga untuk bulan yang sudah paid
                if ($isPaid && rand(0, 1) === 1) {
                    $plnInvoice = Invoice::updateOrCreate(
                        [
                            'lease_contract_id' => $contract->id,
                            'billing_month'     => $current->month,
                            'billing_year'      => $current->year,
                            'type'              => 'electricity',
                        ],
                        [
                            'invoice_number'  => 'INV-PLN-' . $current->format('Ym') . '-' . strtoupper(Str::random(5)),
                            'tenant_id'       => $contract->tenant_id,
                            'manager_id'      => $contract->manager_id,
                            'meter_start'      => rand(100, 500),
                            'meter_end'        => rand(501, 600),
                            'price_per_unit'   => 1500,
                            'amount'           => rand(50000, 200000),
                            'due_date'         => $dueDate,
                            'status'           => 'paid',
                        ]
                    );
                }

                $current->addMonth();
            }
        }

        $invoiceCount  = Invoice::count();
        $paymentCount  = Payment::count();
        $this->command->info("InvoiceSeeder: {$invoiceCount} invoice dan {$paymentCount} payment dibuat.");
    }
}
