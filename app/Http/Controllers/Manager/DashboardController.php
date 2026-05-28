<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $managerId = auth()->id();

        $totalUnits = \App\Models\Unit::whereHas('property', fn($q) => $q->where('manager_id', $managerId))->count();
        $rentedUnits = \App\Models\Unit::whereHas('property', fn($q) => $q->where('manager_id', $managerId))->where('status','occupied')->count();

        $totalRevenue = \App\Models\Payment::whereHas('invoice.leaseContract.unit.property', fn($q) => $q->where('manager_id', $managerId))
            ->where('status','verified')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $properties = \App\Models\Property::where('manager_id', $managerId)
            ->withCount(['units', 'units as rented_units_count' => fn($q) => $q->where('status','occupied')])
            ->get();

        foreach($properties as $property) {
            $property->revenue = \App\Models\Payment::whereHas('invoice.leaseContract.unit', fn($q) => $q->where('property_id', $property->id))
                ->where('status','verified')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
        }

        return view('manager.dashboard', compact('totalUnits', 'rentedUnits', 'totalRevenue', 'properties'));
    }
}
