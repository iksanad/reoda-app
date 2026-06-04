<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $payments;
    protected $year;
    protected $month;

    public function __construct($payments, $year, $month = null)
    {
        $this->payments = $payments;
        $this->year     = $year;
        $this->month    = $month;
    }

    public function collection(): Collection
    {
        $rows = collect();
        $no   = 1;
        foreach ($this->payments as $p) {
            $rows->push([
                $no++,
                $p->payment_code,
                $p->invoice->leaseContract->tenant->name ?? '-',
                $p->invoice->leaseContract->unit->property->name ?? '-',
                $p->invoice->leaseContract->unit->unit_code ?? '-',
                ucfirst($p->invoice->type),
                ($p->invoice->billing_month ?? '-') . '/' . ($p->invoice->billing_year ?? '-'),
                number_format($p->amount, 0, ',', '.'),
                ucfirst($p->payment_method),
                $p->paid_at?->format('d/m/Y H:i') ?? '-',
            ]);
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Kode Bayar', 'Nama Penyewa', 'Properti', 'Kode Unit', 'Jenis Tagihan', 'Periode', 'Nominal (Rp)', 'Metode Bayar', 'Tanggal Bayar'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF3B82F6']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->year . ($this->month ? '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) : '');
    }
}
