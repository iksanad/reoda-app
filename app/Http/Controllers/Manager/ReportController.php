<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Unit;
use App\Models\LeaseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year       = $request->get('year', now()->year);
        $month      = $request->get('month');
        $propertyId = $request->get('property_id');

        $baseQuery = fn($q) => $q->where('manager_id', Auth::id())
            ->when($propertyId, fn($qq) => $qq->where('id', $propertyId));

        // Monthly revenue for the selected year (for chart)
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[$m] = Payment::whereHas('invoice.leaseContract.unit.property', $baseQuery)
                ->where('status', 'approved')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $m)
                ->sum('amount');
        }

        // Per-property summary
        $query = Payment::whereHas('invoice.leaseContract.unit.property', $baseQuery)
            ->where('status', 'approved')
            ->whereYear('paid_at', $year);
        if ($month) $query->whereMonth('paid_at', $month);

        $totalRevenue = (clone $query)->sum('amount');
        $totalPaid    = (clone $query)->count();

        // Payment list for the period
        $payments = Payment::whereHas('invoice.leaseContract.unit.property', $baseQuery)
            ->where('status', 'approved')
            ->whereYear('paid_at', $year)
            ->when($month, fn($q) => $q->whereMonth('paid_at', $month))
            ->with(['invoice.leaseContract.unit.property', 'invoice.leaseContract.tenant'])
            ->latest('paid_at')
            ->paginate(20)
            ->appends(request()->query());

        $years = range(now()->year, 2023);

        return view('manager.reports.index', compact(
            'monthlyData', 'totalRevenue', 'totalPaid',
            'payments', 'year', 'month', 'years'
        ));
    }

    public function export(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month');

        $payments = Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', Auth::id()))
            ->where('status', 'approved')
            ->whereYear('paid_at', $year)
            ->when($month, fn($q) => $q->whereMonth('paid_at', $month))
            ->with(['invoice.leaseContract.unit.property', 'invoice.leaseContract.tenant'])
            ->latest('paid_at')
            ->get();

        $filename = 'laporan-pembayaran-' . $year . ($month ? '-' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.xlsx';

        // Build XLSX using maatwebsite/excel if available, fallback to CSV
        if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
            $export = new \App\Exports\ReportExport($payments, $year, $month);
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        // Fallback: simple CSV
        $csvFilename = str_replace('.xlsx', '.csv', $filename);
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$csvFilename}\"",
        ];
        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Bayar', 'Penyewa', 'Properti', 'Unit', 'Jenis', 'Periode', 'Nominal', 'Metode', 'Tanggal']);
            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->payment_code,
                    $p->invoice->leaseContract->tenant->name ?? '-',
                    $p->invoice->leaseContract->unit->property->name ?? '-',
                    $p->invoice->leaseContract->unit->unit_code ?? '-',
                    $p->invoice->type,
                    ($p->invoice->billing_month ?? '-') . '/' . ($p->invoice->billing_year ?? '-'),
                    $p->amount,
                    $p->payment_method,
                    $p->paid_at?->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
