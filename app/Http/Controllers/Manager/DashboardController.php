<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Unit;
use App\Models\LeaseContract;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();
        $year = now()->year;

        $totalUnits  = Unit::whereHas('property', fn($q) => $q->where('manager_id', $managerId))->count();
        $rentedUnits = Unit::whereHas('property', fn($q) => $q->where('manager_id', $managerId))->where('status', 'occupied')->count();

        $pendingContracts = LeaseContract::where('manager_id', $managerId)->where('status', 'awaiting_approval')->count();

        $totalRevenue = Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', $managerId))
            ->where('status', 'approved')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', $year)
            ->sum('amount');

        $properties = Property::where('manager_id', $managerId)
            ->withCount(['units', 'units as rented_units_count' => fn($q) => $q->where('status', 'occupied')])
            ->get();

        foreach ($properties as $property) {
            $property->revenue = Payment::whereHas('invoice.leaseContract.unit', fn($q) => $q->where('property_id', $property->id))
                ->where('status', 'approved')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', $year)
                ->sum('amount');
        }

        // Monthly revenue chart data (12 bulan terakhir)
        $monthlyRevenue = [];
        $monthLabels    = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[]    = \Carbon\Carbon::createFromDate($year, $m, 1)->isoFormat('MMM');
            $monthlyRevenue[] = Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', $managerId))
                ->where('status', 'approved')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $m)
                ->sum('amount');
        }

        // Unit occupancy per property (for pie/bar)
        $occupancyData = $properties->map(fn($p) => [
            'name'     => $p->name,
            'total'    => $p->units_count,
            'rented'   => $p->rented_units_count,
            'available'=> $p->units_count - $p->rented_units_count,
        ]);

        return view('manager.dashboard', compact(
            'totalUnits', 'rentedUnits', 'totalRevenue',
            'pendingContracts', 'properties',
            'monthlyRevenue', 'monthLabels', 'occupancyData'
        ));
    }
}
