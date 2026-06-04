<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaseContract;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeContract = LeaseContract::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with('unit.property.manager')
            ->first();

        $awaitingContract = !$activeContract
            ? LeaseContract::where('tenant_id', $user->id)
                ->where('status', 'awaiting_approval')
                ->with('unit.property')
                ->first()
            : null;

        $pendingInvoice = null;
        if ($activeContract) {
            $pendingInvoice = Invoice::where('lease_contract_id', $activeContract->id)
                ->whereIn('status', ['unpaid', 'pending'])
                ->first();
        }

        return view('tenant.dashboard', compact('activeContract', 'awaitingContract', 'pendingInvoice'));
    }
}
