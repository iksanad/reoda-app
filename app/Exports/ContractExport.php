<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractExport implements FromCollection, WithHeadings, WithMapping
{
    protected $contracts;

    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    public function collection()
    {
        return $this->contracts;
    }

    public function headings(): array
    {
        return ['No. Kontrak', 'Penyewa', 'Properti', 'Unit', 'Jenis', 'Mulai', 'Akhir', 'Harga Sewa', 'Status'];
    }

    public function map($contract): array
    {
        return [
            $contract->contract_number,
            $contract->tenant->name ?? '-',
            $contract->unit->property->name ?? '-',
            $contract->unit->unit_code ?? '-',
            ucfirst($contract->rental_type),
            $contract->start_date ? $contract->start_date->format('Y-m-d') : '-',
            $contract->end_date ? $contract->end_date->format('Y-m-d') : '-',
            $contract->rent_amount,
            ucfirst($contract->status)
        ];
    }
}
