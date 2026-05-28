<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyExport implements FromCollection, WithHeadings, WithMapping
{
    protected $properties;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function collection()
    {
        return $this->properties;
    }

    public function headings(): array
    {
        return ['Nama Lokasi', 'Tipe', 'Alamat', 'Kota', 'Total Unit', 'Unit Disewa', 'Status'];
    }

    public function map($prop): array
    {
        return [
            $prop->name,
            ucfirst($prop->type),
            $prop->address,
            $prop->city,
            $prop->units_count ?? 0,
            $prop->rented_units_count ?? 0,
            ucfirst($prop->status)
        ];
    }
}
