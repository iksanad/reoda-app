<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaseContract;
use App\Exports\TenantExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        // Get tenants who have an active or past lease contract on properties owned by this manager
        $query = User::where('role', 'tenant')
            ->whereHas('leaseContracts', function ($q) {
                $q->whereHas('unit.property', function ($p) {
                    $p->where('manager_id', Auth::id());
                });
            })
            ->with(['leaseContracts' => function ($q) {
                $q->whereHas('unit.property', function ($p) {
                    $p->where('manager_id', Auth::id());
                })->with('unit.property')->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('leaseContracts', function ($q) use ($status) {
                $q->where('status', $status)
                  ->whereHas('unit.property', function ($p) {
                      $p->where('manager_id', Auth::id());
                  });
            });
        }

        $tenants = $query->paginate(15)->appends(request()->query());

        // Stat cards
        $baseContractQuery = fn($q) => $q->whereHas('unit.property', fn($p) => $p->where('manager_id', Auth::id()));
        $stats = [
            'total'    => User::where('role', 'tenant')->whereHas('leaseContracts', $baseContractQuery)->count(),
            'active'   => User::where('role', 'tenant')->whereHas('leaseContracts', fn($q) => $baseContractQuery($q)->where('status', 'active'))->count(),
            'expiring' => User::where('role', 'tenant')->whereHas('leaseContracts', fn($q) => $baseContractQuery($q)->where('status', 'active')->where('end_date', '<=', now()->addDays(30)))->count(),
            'expired'  => User::where('role', 'tenant')->whereHas('leaseContracts', fn($q) => $baseContractQuery($q)->where('status', 'expired'))->count(),
        ];

        return view('manager.tenants.index', compact('tenants', 'stats'));
    }

    public function show(User $tenant)
    {
        // Ensure this tenant has a contract on one of the manager's properties
        $hasContract = $tenant->leaseContracts()
            ->whereHas('unit.property', function ($q) {
                $q->where('manager_id', Auth::id());
            })->exists();

        if (!$hasContract) {
            abort(403);
        }

        $contracts = $tenant->leaseContracts()
            ->whereHas('unit.property', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with('unit.property')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manager.tenants.show', compact('tenant', 'contracts'));
    }

    public function export(Request $request)
    {
        $query = User::where('role', 'tenant')
            ->whereHas('leaseContracts', function ($q) {
                $q->whereHas('unit.property', function ($p) {
                    $p->where('manager_id', Auth::id());
                });
            })
            ->with(['leaseContracts' => function ($q) {
                $q->whereHas('unit.property', function ($p) {
                    $p->where('manager_id', Auth::id());
                })->with('unit.property')->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('leaseContracts', function ($q) use ($status) {
                $q->where('status', $status)
                  ->whereHas('unit.property', function ($p) {
                      $p->where('manager_id', Auth::id());
                  });
            });
        }

        $tenants = $query->get();

        return Excel::download(new TenantExport($tenants), 'data_penyewa.xlsx');
    }
}
