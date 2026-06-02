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
    /**
     * Aturan pembuatan invoice per kontrak:
     *  - Semua bulan dari start_date hingga bulan LALU  → PAID (lunas + ada payment)
     *  - Bulan berjalan (bulan ini)                     → UNPAID (belum jatuh tempo / baru terbit)
     *  - Khusus kontrak Tono (expired) semua invoice sudah paid
     *  - Khusus kontrak Putri (penyewa7, Kos Yogya)  bulan berjalan → OVERDUE (telat bayar)
     */
    public function run(): void
    {
        $contracts = LeaseContract::with(['tenant', 'unit', 'manager'])->get();

        if ($contracts->isEmpty()) {
            $this->command->warn('Tidak ada kontrak. Skip InvoiceSeeder.');
            return;
        }

        foreach ($contracts as $contract) {
            $start = Carbon::parse($contract->start_date)->startOfMonth();
            $now   = now();

            // Tentukan batas akhir pembuatan invoice
            // Untuk kontrak expired, generate hingga end_date
            $limit = $contract->status === 'expired'
                ? Carbon::parse($contract->end_date)->startOfMonth()
                : $now->copy()->startOfMonth();

            $current = $start->copy();

            // Apakah ini kontrak yang sengaja dibuat overdue (Putri - penyewa7 di PMD-04)
            $isOverdueContract = $contract->tenant->email === 'penyewa7@reoda.com'
                && $contract->unit->unit_code === 'PMD-04';

            while ($current->lte($limit)) {
                $billingMonth = $current->month;
                $billingYear  = $current->year;
                $dueDate      = $current->copy()->addDays(10); // jatuh tempo tanggal 10

                $isBulanIni = $current->isSameMonth($now);
                $isExpired  = $contract->status === 'expired';

                // Tentukan status invoice
                if ($isExpired || $current->lt($now->copy()->startOfMonth())) {
                    // Semua bulan sebelum bulan ini → paid
                    $status = 'paid';
                } elseif ($isBulanIni && $isOverdueContract) {
                    // Kontrak khusus overdue untuk testing
                    $status = 'unpaid';
                    $dueDate = $current->copy()->subDays(5); // due date sudah lewat
                } else {
                    // Bulan berjalan → unpaid (invoice baru terbit)
                    $status = 'unpaid';
                }

                // Buat invoice sewa
                $invoice = Invoice::updateOrCreate(
                    [
                        'lease_contract_id' => $contract->id,
                        'billing_month'     => $billingMonth,
                        'billing_year'      => $billingYear,
                        'type'              => 'rent',
                    ],
                    [
                        'invoice_number' => 'INV-RENT-' . $billingYear . str_pad($billingMonth, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(5)),
                        'tenant_id'      => $contract->tenant_id,
                        'manager_id'     => $contract->manager_id,
                        'amount'         => $contract->rent_amount,
                        'due_date'       => $dueDate,
                        'status'         => $status,
                    ]
                );

                // Buat payment untuk invoice yang paid
                if ($status === 'paid') {
                    $methods = ['transfer', 'transfer', 'transfer', 'cash'];
                    $method  = $methods[array_rand($methods)];
                    $paidAt  = $dueDate->copy()->subDays(rand(1, 7)); // bayar 1-7 hari sebelum jatuh tempo

                    $manager = $contract->manager;

                    Payment::updateOrCreate(
                        ['invoice_id' => $invoice->id, 'tenant_id' => $contract->tenant_id],
                        [
                            'payment_code'   => 'PAY-' . strtoupper(Str::random(10)),
                            'manager_id'     => $contract->manager_id,
                            'amount'         => $contract->rent_amount,
                            'payment_method' => $method,
                            'bank_name'      => $method === 'transfer' ? ($manager->bank_name ?? 'BCA') : null,
                            'bank_account'   => $method === 'transfer' ? ($manager->bank_account_number ?? '1234567890') : null,
                            'status'         => 'approved',
                            'paid_at'        => $paidAt,
                            'verified_at'    => $paidAt->copy()->addDays(rand(1, 2)),
                            'verified_by'    => $contract->manager_id,
                        ]
                    );

                    // Tagihan listrik (50% peluang, hanya untuk unit occupied aktif)
                    if (rand(0, 1) === 1 && $contract->status === 'active') {
                        $meterStart = rand(100, 800);
                        $meterEnd   = $meterStart + rand(30, 120);
                        $kwh        = $meterEnd - $meterStart;
                        $ratePerKwh = 1444; // tarif dasar listrik
                        $amount     = $kwh * $ratePerKwh;

                        Invoice::updateOrCreate(
                            [
                                'lease_contract_id' => $contract->id,
                                'billing_month'     => $billingMonth,
                                'billing_year'      => $billingYear,
                                'type'              => 'electricity',
                            ],
                            [
                                'invoice_number' => 'INV-PLN-' . $billingYear . str_pad($billingMonth, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(5)),
                                'tenant_id'      => $contract->tenant_id,
                                'manager_id'     => $contract->manager_id,
                                'meter_start'    => $meterStart,
                                'meter_end'      => $meterEnd,
                                'price_per_unit' => $ratePerKwh,
                                'amount'         => $amount,
                                'due_date'       => $dueDate,
                                'status'         => 'paid',
                            ]
                        );
                    }
                }

                $current->addMonth();
            }
        }

        $invoiceCount = Invoice::count();
        $paymentCount = Payment::count();
        $this->command->info("InvoiceSeeder: {$invoiceCount} invoice dan {$paymentCount} payment dibuat.");
    }
}
