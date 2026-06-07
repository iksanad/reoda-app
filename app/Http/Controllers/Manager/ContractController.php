<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaseContract;
use App\Models\Property;
use App\Models\User;
use App\Exports\ContractExport;
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
        return view('manager.contracts.create', compact('properties', 'tenants'));
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

        // Notify tenant
        \App\Models\Notification::create([
            'user_id' => $contract->tenant_id,
            'type'    => 'contract_approved',
            'title'   => 'Kontrak Disetujui! 🎉',
            'message' => 'Pengajuan kontrak Anda untuk unit ' . ($contract->unit->name ?? '') . ' di ' . ($contract->unit->property->name ?? '') . ' telah disetujui oleh pengelola.',
        ]);

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

        // Notify tenant
        \App\Models\Notification::create([
            'user_id' => $contract->tenant_id,
            'type'    => 'contract_rejected',
            'title'   => 'Pengajuan Kontrak Ditolak',
            'message' => 'Maaf, pengajuan kontrak Anda untuk unit ' . ($contract->unit->name ?? '') . ' ditolak. Alasan: ' . $request->rejection_reason,
        ]);

        return back()->with('success', 'Pengajuan kontrak ditolak.');
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
