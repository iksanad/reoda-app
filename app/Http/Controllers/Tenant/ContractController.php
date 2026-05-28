<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaseContract;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $contracts = LeaseContract::where('tenant_id', $user->id)
            ->with(['unit.property.manager'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('tenant.contract.index', compact('contracts'));
    }

    public function show($id = null)
    {
        $user = Auth::user();
        
        $query = LeaseContract::where('tenant_id', $user->id)
            ->with(['unit.property.manager', 'unit.facilities']);
            
        if ($id) {
            $contract = $query->find($id);
        } else {
            // Priority: pending, then active
            $contract = $query->whereIn('status', ['pending', 'active'])
                ->orderByRaw("FIELD(status, 'pending', 'active')")
                ->first();
        }

        if (!$contract) {
            return redirect()->route('tenant.dashboard')->with('error', 'Anda belum memiliki kontrak sewa yang aktif atau menunggu persetujuan.');
        }

        return view('tenant.contract.show', compact('contract'));
    }
    
    public function approve(LeaseContract $contract)
    {
        if ($contract->tenant_id !== Auth::id() || $contract->status !== 'pending') {
            abort(403);
        }
        
        $contract->update(['status' => 'active']);
        $contract->unit->update(['status' => 'rented']);
        
        return back()->with('success', 'Anda telah menyetujui kontrak sewa ini. Status sekarang Aktif.');
    }
    
    public function reject(LeaseContract $contract)
    {
        if ($contract->tenant_id !== Auth::id() || $contract->status !== 'pending') {
            abort(403);
        }
        
        $contract->update([
            'status' => 'terminated',
            'termination_reason' => 'Ditolak oleh penyewa'
        ]);
        
        return back()->with('success', 'Anda telah menolak kontrak sewa ini.');
    }
}
