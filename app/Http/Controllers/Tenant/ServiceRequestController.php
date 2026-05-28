<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\FacilityRequest;
use App\Models\EmergencyReport;
use App\Models\ContractRequest;
use App\Models\LeaseContract;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Main service hub page.
     */
    public function index()
    {
        $user     = Auth::user();
        $contract = LeaseContract::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['unit.property.manager', 'unit.facilities'])
            ->latest()
            ->first();

        $facilityRequests  = FacilityRequest::where('tenant_id', $user->id)->latest()->take(5)->get();
        $emergencyReports  = EmergencyReport::where('tenant_id', $user->id)->latest()->take(5)->get();
        $contractRequests  = ContractRequest::where('tenant_id', $user->id)->latest()->take(5)->get();

        return view('tenant.services.index', compact(
            'contract', 'facilityRequests', 'emergencyReports', 'contractRequests'
        ));
    }

    /**
     * Submit a facility request.
     */
    public function storeFacility(Request $request)
    {
        $user = Auth::user();
        $contract = LeaseContract::where('tenant_id', $user->id)->where('status', 'active')->firstOrFail();

        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
        ]);

        $facilityRequest = FacilityRequest::create([
            'tenant_id'   => $user->id,
            'unit_id'     => $contract->unit_id,
            'manager_id'  => $contract->manager_id,
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        // Send notification to manager
        $this->notificationService->send(
            $contract->manager,
            'Permohonan Fasilitas Baru',
            "Penyewa {$user->name} mengajukan permohonan fasilitas: {$request->title}",
            'facility_request',
            '/manager/properties/' . $contract->unit->property_id . '#services', // Adjust link later if needed
            $facilityRequest
        );

        return redirect()->route('tenant.services.index')
            ->with('success', 'Permintaan fasilitas berhasil dikirim ke pengelola.');
    }

    /**
     * Submit an emergency report.
     */
    public function storeEmergency(Request $request)
    {
        $user = Auth::user();
        $contract = LeaseContract::where('tenant_id', $user->id)->where('status', 'active')->firstOrFail();

        $request->validate([
            'category'    => 'required|in:electricity,water,structural,security,other',
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
            'priority'    => 'required|in:low,medium,high,critical',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('emergency/photos', 'public');
        }

        $emergencyReport = EmergencyReport::create([
            'tenant_id'   => $user->id,
            'unit_id'     => $contract->unit_id,
            'manager_id'  => $contract->manager_id,
            'category'    => $request->category,
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'photo'       => $photoPath,
            'status'      => 'open',
        ]);

        // Send notification to manager
        $this->notificationService->send(
            $contract->manager,
            'Laporan Darurat Baru',
            "Penyewa {$user->name} melaporkan keadaan darurat [{$request->priority}]: {$request->title}",
            'emergency_report',
            '/manager/properties/' . $contract->unit->property_id . '#services',
            $emergencyReport
        );

        return redirect()->route('tenant.services.index')
            ->with('success', 'Laporan darurat berhasil dikirim! Pengelola akan segera merespons.');
    }

    /**
     * Submit a contract renewal/termination request.
     */
    public function storeContractRequest(Request $request)
    {
        $user = Auth::user();
        $contract = LeaseContract::where('tenant_id', $user->id)->where('status', 'active')->firstOrFail();

        $request->validate([
            'type'           => 'required|in:renewal,termination',
            'reason'         => 'required|string|max:2000',
            'requested_date' => 'nullable|date|after:today',
        ]);

        // Check no pending request of same type
        $existing = ContractRequest::where('tenant_id', $user->id)
            ->where('lease_contract_id', $contract->id)
            ->where('type', $request->type)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki permintaan ' . ($request->type === 'renewal' ? 'perpanjangan' : 'pembatalan') . ' yang sedang diproses.');
        }

        ContractRequest::create([
            'lease_contract_id' => $contract->id,
            'tenant_id'         => $user->id,
            'manager_id'        => $contract->manager_id,
            'type'              => $request->type,
            'reason'            => $request->reason,
            'requested_date'    => $request->requested_date,
            'status'            => 'pending',
        ]);

        $label = $request->type === 'renewal' ? 'perpanjangan' : 'pembatalan';
        return redirect()->route('tenant.services.index')
            ->with('success', "Permintaan {$label} kontrak berhasil dikirim.");
    }
}
