<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaseContract;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeContract = LeaseContract::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['unit.property.manager', 'unit.property'])
            ->first();

        $awaitingContract = !$activeContract
            ? LeaseContract::where('tenant_id', $user->id)
                ->where('status', 'awaiting_approval')
                ->with('unit.property')
                ->first()
            : null;

        $pendingInvoice = null;
        $remainingDays = null;
        $lastPaidInvoice = null;
        $electricityInvoice = null;
        $waterInvoice = null;

        if ($activeContract) {
            $pendingInvoice = Invoice::where('lease_contract_id', $activeContract->id)
                ->whereIn('status', ['unpaid', 'pending'])
                ->first();

            // Calculate remaining days
            if ($activeContract->end_date) {
                $remainingDays = max(0, Carbon::now()->diffInDays($activeContract->end_date, false));
            }
            // null means unlimited (kos type)

            // Last paid rent invoice
            $lastPaidInvoice = Invoice::where('lease_contract_id', $activeContract->id)
                ->where('type', 'rent')
                ->where('status', 'paid')
                ->orderBy('billing_year', 'desc')
                ->orderBy('billing_month', 'desc')
                ->first();

            // Latest electricity invoice (if applicable)
            $electricityInvoice = Invoice::where('lease_contract_id', $activeContract->id)
                ->where('type', 'electricity')
                ->orderBy('billing_year', 'desc')
                ->orderBy('billing_month', 'desc')
                ->first();

            // Latest water invoice (if applicable)
            $waterInvoice = Invoice::where('lease_contract_id', $activeContract->id)
                ->where('type', 'water')
                ->orderBy('billing_year', 'desc')
                ->orderBy('billing_month', 'desc')
                ->first();
        }

        $elecConf  = $activeContract?->unit?->property?->electricity_config;
        $waterConf = $activeContract?->unit?->property?->water_config;
        $plnId     = $activeContract?->unit?->pln_customer_id;
        $pdamId    = $activeContract?->unit?->pdam_customer_id;

        return view('tenant.dashboard', compact(
            'activeContract',
            'awaitingContract',
            'pendingInvoice',
            'remainingDays',
            'lastPaidInvoice',
            'electricityInvoice',
            'waterInvoice',
            'elecConf',
            'waterConf',
            'plnId',
            'pdamId'
        ));
    }
}
