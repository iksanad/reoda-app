<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return ['Kode Pembayaran', 'No. Invoice', 'Penyewa', 'Properti', 'Unit', 'Nominal', 'Metode', 'Tanggal Bayar', 'Status'];
    }

    public function map($payment): array
    {
        $tenantName = $payment->tenant->name ?? ($payment->invoice->leaseContract->tenant->name ?? '-');
        $propertyName = $payment->invoice->leaseContract->unit->property->name ?? '-';
        $unitCode = $payment->invoice->leaseContract->unit->unit_code ?? '-';

        return [
            $payment->payment_code,
            $payment->invoice->invoice_number ?? '-',
            $tenantName,
            $propertyName,
            $unitCode,
            $payment->amount,
            ucfirst($payment->payment_method),
            $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '',
            ucfirst($payment->status)
        ];
    }
}
