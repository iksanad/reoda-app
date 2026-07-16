<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\LeaseContract;
use App\Models\Property;
use App\Models\User;
use App\Exports\ContractExport;
use App\Services\NotificationService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaseContract::where('lease_contracts.manager_id', Auth::id())
            ->join('users as tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
            ->select('lease_contracts.*')
            ->with(['tenant', 'unit.property'])
            ->orderByRaw("CASE WHEN lease_contracts.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('lease_contracts.created_at', 'desc')
            ->orderBy('tenants.name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $contracts = $query->paginate(15)->appends(request()->query());

        $counts = [
            'all'               => LeaseContract::where('manager_id', Auth::id())->count(),
            'awaiting_approval' => LeaseContract::where('manager_id', Auth::id())->where('status', 'awaiting_approval')->count(),
            'active'            => LeaseContract::where('manager_id', Auth::id())->where('status', 'active')->count(),
            'expired'           => LeaseContract::where('manager_id', Auth::id())->where('status', 'expired')->count(),
            'terminated'        => LeaseContract::where('manager_id', Auth::id())->where('status', 'terminated')->count(),
        ];

        return view('manager.contracts.index', compact('contracts', 'counts'));
    }

    public function create()
    {
        $properties = Property::where('manager_id', Auth::id())->with('units')->get();
        $tenants    = User::where('role', 'tenant')->orderBy('name')->get();
        
        $pricesArray = [];
        foreach ($properties as $property) {
            foreach ($property->units as $unit) {
                $pricesArray[$unit->id] = $unit->price_monthly;
            }
        }
        
        return view('manager.contracts.create', compact('properties', 'tenants', 'pricesArray'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_code'    => 'required|string',
            'unit_id'        => 'required|exists:units,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'rental_type'    => 'required|in:monthly,yearly',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);
        
        $tenant = User::where('user_code', $validated['tenant_code'])->where('role', 'tenant')->first();
        if (!$tenant) {
            return back()->withErrors(['tenant_code' => 'ID Penyewa tidak ditemukan atau tidak valid.'])->withInput();
        }

        $validated['tenant_id']        = $tenant->id;
        $validated['manager_id']       = Auth::id();
        $validated['contract_number']  = 'KTR-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $validated['status']           = 'pending';
        $validated['deposit_amount'] ??= 0;
        unset($validated['tenant_code']);

        $contract = LeaseContract::create($validated);

        return redirect()->route('manager.contracts.show', $contract)
            ->with('success', 'Kontrak sewa berhasil dibuat dan sedang menunggu persetujuan penyewa.');
    }

    public function show(LeaseContract $contract)
    {
        if ($contract->manager_id !== Auth::id()) abort(403);
        $contract->load(['tenant', 'unit.property', 'invoices' => fn($q) => $q->latest()]);
        return view('manager.contracts.show', compact('contract'));
    }

    public function terminate(Request $request, LeaseContract $contract)
    {
        if ($contract->manager_id !== Auth::id()) abort(403);

        $request->validate(['termination_reason' => 'required|string|max:500']);

        $contract->update([
            'status'             => 'terminated',
            'terminated_at'      => now(),
            'termination_reason' => $request->termination_reason,
        ]);

        // Free up the unit
        $contract->unit->update(['status' => 'available']);

        return redirect()->route('manager.contracts.index')
            ->with('success', 'Kontrak berhasil diakhiri. Unit telah dikembalikan ke status tersedia.');
    }

    /**
     * Approve a contract request submitted by a tenant.
     */
    public function approveRequest(Request $request, LeaseContract $contract)
    {
        if ($contract->manager_id !== Auth::id()) abort(403);
        if ($contract->status !== 'awaiting_approval') {
            return back()->with('error', 'Kontrak ini tidak dalam status menunggu persetujuan.');
        }

        $contract->update([
            'status'              => 'active',
            'manager_approved_at' => now(),
        ]);

        // Auto-generate invoices based on property type
        $property = $contract->unit->property;
        $startDate = Carbon::parse($contract->start_date);

        // Deposit invoice (if deposit_amount > 0 and property has contract)
        if ($contract->deposit_amount > 0 && in_array($property->type, ['kontrakan', 'apartemen'])) {
            Invoice::create([
                'invoice_number'    => 'INV-DEP-' . strtoupper(Str::random(6)),
                'lease_contract_id' => $contract->id,
                'tenant_id'         => $contract->tenant_id,
                'manager_id'        => $contract->manager_id,
                'type'              => 'deposit',
                'billing_month'     => $startDate->month,
                'billing_year'      => $startDate->year,
                'amount'            => $contract->deposit_amount,
                'due_date'          => now()->addDays(3),
                'status'            => 'unpaid',
                'notes'             => 'Deposit / Uang Jaminan',
            ]);
        }

        // First rent invoice
        Invoice::create([
            'invoice_number'    => 'INV-' . strtoupper(Str::random(8)),
            'lease_contract_id' => $contract->id,
            'tenant_id'         => $contract->tenant_id,
            'manager_id'        => $contract->manager_id,
            'type'              => 'rent',
            'billing_month'     => $startDate->month,
            'billing_year'      => $startDate->year,
            'amount'            => $contract->rent_amount,
            'due_date'          => now()->addDays(7),
            'status'            => 'unpaid',
            'notes'             => 'Tagihan sewa periode pertama',
        ]);

        // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
        if ($contract->tenant) {
            app(NotificationService::class)->send(
                $contract->tenant,
                'Kontrak Disetujui! 🎉',
                'Pengajuan kontrak Anda untuk unit ' . ($contract->unit->name ?? '') . ' di ' . ($contract->unit->property->name ?? '') . ' telah disetujui oleh pengelola.',
                'contract_approved',
                route('tenant.contract.show'),
                $contract
            );
        }

        return back()->with('success', 'Kontrak berhasil disetujui. Penyewa telah diberitahu.');
    }

    /**
     * Reject a contract request submitted by a tenant.
     */
    public function rejectRequest(Request $request, LeaseContract $contract)
    {
        if ($contract->manager_id !== Auth::id()) abort(403);
        if ($contract->status !== 'awaiting_approval') {
            return back()->with('error', 'Kontrak ini tidak dalam status menunggu persetujuan.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $contract->update([
            'status'             => 'terminated',
            'terminated_at'      => now(),
            'termination_reason' => $request->rejection_reason,
        ]);

        // Free the unit
        $contract->unit->update(['status' => 'available']);

        // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
        if ($contract->tenant) {
            app(NotificationService::class)->send(
                $contract->tenant,
                'Pengajuan Kontrak Ditolak',
                'Maaf, pengajuan kontrak Anda ditolak. Alasan: ' . $request->rejection_reason,
                'contract_rejected',
                route('tenant.contract.show'),
                $contract
            );
        }

        return back()->with('success', 'Pengajuan kontrak ditolak.');
    }

    public function storeInvoice(Request $request, LeaseContract $contract)
    {
        if ($contract->manager_id !== Auth::id()) abort(403);
        if ($contract->status !== 'active') {
            return back()->with('error', 'Tagihan hanya dapat dibuat untuk kontrak yang aktif.');
        }

        $request->validate([
            'type'         => 'required|in:rent,electricity,water,ipl',
            'billing_month'=> 'required|integer|min:1|max:12',
            'billing_year' => 'required|integer|min:2020|max:2100',
            'amount'       => 'required|numeric|min:1000',
            'due_date'     => 'required|date|after:yesterday',
            'meter_start'  => 'nullable|numeric|min:0',
            'meter_end'    => 'nullable|numeric|min:0|gte:meter_start',
            'price_per_unit'=> 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $typeLabels = [
            'rent'        => 'Sewa Hunian',
            'electricity' => 'Listrik',
            'water'       => 'Air',
            'ipl'         => 'IPL / Maintenance Fee',
        ];

        $invoice = Invoice::create([
            'invoice_number'    => 'INV-' . strtoupper(Str::random(8)),
            'lease_contract_id' => $contract->id,
            'tenant_id'         => $contract->tenant_id,
            'manager_id'        => Auth::id(),
            'type'              => $request->type,
            'billing_month'     => $request->billing_month,
            'billing_year'      => $request->billing_year,
            'amount'            => $request->amount,
            'meter_start'       => $request->meter_start,
            'meter_end'         => $request->meter_end,
            'price_per_unit'    => $request->price_per_unit,
            'due_date'          => $request->due_date,
            'status'            => 'unpaid',
            'notes'             => $request->notes,
        ]);

        // Notify tenant via NotificationService (logs email to Superadmin Email Logs)
        if ($contract->tenant) {
            app(NotificationService::class)->send(
                $contract->tenant,
                'Tagihan Baru: ' . ($typeLabels[$request->type] ?? ucfirst($request->type)),
                'Anda memiliki tagihan baru untuk ' . ($typeLabels[$request->type] ?? '') . ' periode ' . $request->billing_month . '/' . $request->billing_year . '. Jatuh tempo: ' . $request->due_date . '.',
                'payment_due',
                route('tenant.transactions.index'),
                $invoice
            );
        }

        return back()->with('success', 'Tagihan berhasil dibuat dan penyewa telah diberitahu.');
    }

    public function export(Request $request)
    {
        $query = LeaseContract::where('lease_contracts.manager_id', Auth::id())
            ->join('users as tenants', 'lease_contracts.tenant_id', '=', 'tenants.id')
            ->select('lease_contracts.*')
            ->with(['tenant', 'unit.property'])
            ->orderByRaw("CASE WHEN lease_contracts.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('lease_contracts.created_at', 'desc')
            ->orderBy('tenants.name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $contracts = $query->get();

        return Excel::download(new ContractExport($contracts), 'data_kontrak_sewa.xlsx');
    }
}
