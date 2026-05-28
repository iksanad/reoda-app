<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Payment;
use App\Models\LeaseContract;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_managers'       => User::where('role', 'manager')->count(),
            'pending_managers'     => User::where('role', 'manager')->where('manager_status', 'pending')->count(),
            'total_tenants'        => User::where('role', 'tenant')->count(),
            'total_properties'     => Property::count(),
            'active_contracts'     => LeaseContract::where('status', 'active')->count(),
            'total_revenue'        => Payment::where('status', 'verified')->sum('amount'),
            'revenue_this_month'   => Payment::where('status', 'verified')
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('amount'),
        ];

        $pendingManagers = User::where('role', 'manager')
            ->where('manager_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentManagers = User::where('role', 'manager')
            ->latest()
            ->take(8)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'pendingManagers', 'recentManagers'));
    }
}
