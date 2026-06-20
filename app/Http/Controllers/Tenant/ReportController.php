<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\LeaseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the active (or most recent) contract for the tenant
        $contract = LeaseContract::where('tenant_id', $user->id)
            ->with('unit.property')
            ->latest()
            ->first();

        // All paid invoices for this tenant
        $paidInvoices = Invoice::where('tenant_id', $user->id)
            ->where('status', 'paid')
            ->get();

        // Yearly summary grouped by year, then month
        $yearlyData = [];
        $availableYears = $paidInvoices->pluck('billing_year')->unique()->sortDesc()->values()->toArray();

        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }

        foreach ($availableYears as $year) {
            $yearInvoices = $paidInvoices->where('billing_year', $year);

            $monthlyBreakdown = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthInvoices = $yearInvoices->where('billing_month', $m);
                if ($monthInvoices->isEmpty()) continue;

                $monthlyBreakdown[$m] = [
                    'month'       => $m,
                    'rent'        => $monthInvoices->where('type', 'rent')->sum('amount'),
                    'electricity' => $monthInvoices->where('type', 'electricity')->sum('amount'),
                    'water'       => $monthInvoices->where('type', 'water')->sum('amount'),
                    'ipl'         => $monthInvoices->where('type', 'ipl')->sum('amount'),
                    'total'       => $monthInvoices->sum('amount'),
                ];
            }

            $yearlyData[$year] = [
                'year'          => $year,
                'monthly'       => $monthlyBreakdown,
                'total_rent'    => $yearInvoices->where('type', 'rent')->sum('amount'),
                'total_elec'    => $yearInvoices->where('type', 'electricity')->sum('amount'),
                'total_water'   => $yearInvoices->where('type', 'water')->sum('amount'),
                'total_ipl'     => $yearInvoices->where('type', 'ipl')->sum('amount'),
                'grand_total'   => $yearInvoices->sum('amount'),
            ];
        }

        // Current year stats for summary cards
        $currentYear = Carbon::now()->year;
        $currentYearData = $yearlyData[$currentYear] ?? [
            'total_rent'  => 0,
            'total_elec'  => 0,
            'total_water' => 0,
            'grand_total' => 0,
        ];

        // Chart data for current year (monthly totals)
        $chartMonths = [];
        $chartTotals = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonths[] = Carbon::create(null, $m)->translatedFormat('M');
            $chartTotals[] = isset($yearlyData[$currentYear]['monthly'][$m])
                ? $yearlyData[$currentYear]['monthly'][$m]['total']
                : 0;
        }

        return view('tenant.reports.index', compact(
            'contract', 'yearlyData', 'availableYears',
            'currentYearData', 'currentYear', 'chartMonths', 'chartTotals'
        ));
    }
}
