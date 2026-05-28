<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tenants;

    public function __construct($tenants)
    {
        $this->tenants = $tenants;
    }

    public function collection()
    {
        return $this->tenants;
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'Telepon', 'Jumlah Kontrak', 'Bergabung Sejak'];
    }

    public function map($tenant): array
    {
        return [
            $tenant->name,
            $tenant->email,
            $tenant->phone ?? '-',
            $tenant->leaseContracts ? $tenant->leaseContracts->count() : 0,
            $tenant->created_at->format('d M Y')
        ];
    }
}
