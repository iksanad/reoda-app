<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\LeaseContract;
use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractRequestedMail;

class ContractRequestController extends Controller
{
    /**
     * Show the contract request form for a given property.
     */
    public function show(string $propertyCode)
    {
        $property = Property::where('property_code', $propertyCode)
            ->where('status', 'active')
            ->with(['units' => function ($q) {
                $q->where('status', 'available')->orderBy('unit_code');
            }, 'facilities', 'manager'])
            ->firstOrFail();

        // Check if tenant already has an active/pending contract
        $existingContract = LeaseContract::where('tenant_id', Auth::id())
            ->whereIn('status', ['active', 'awaiting_approval'])
            ->whereHas('unit', fn($q) => $q->where('property_id', $property->id))
            ->first();

        if ($existingContract) {
            return redirect()->route('tenant.contract.show')
                ->with('info', 'Anda sudah memiliki kontrak aktif atau sedang diproses untuk hunian ini.');
        }

        return view('tenant.contract-request', compact('property'));
    }

    /**
     * Store the contract request.
     */
    public function store(Request $request, string $propertyCode)
    {
        $property = Property::where('property_code', $propertyCode)
            ->where('status', 'active')
            ->with(['manager'])
            ->firstOrFail();

        $request->validate([
            'unit_id'          => 'required|exists:units,id',
            'payment_cycle'    => 'required|in:monthly,yearly',
            'contract_duration'=> 'nullable|integer|min:1|max:60',
            'tolerance_days'   => 'nullable|integer|min:1|max:30',
        ]);

        $unit = Unit::where('id', $request->unit_id)
            ->where('property_id', $property->id)
            ->where('status', 'available')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $isKos = $property->type === 'kos';

            // Calculate rent amount
            $rentAmount = $unit->rent_price;
            $duration   = $isKos ? null : ($request->contract_duration ?? 1);
            $cycle      = $request->payment_cycle;

            if (!$isKos && $cycle === 'yearly' && $property->yearly_discount_percent > 0) {
                $discount   = $property->yearly_discount_percent / 100;
                $rentAmount = $unit->rent_price * (1 - $discount);
            }

            // Determine start/end dates
            $startDate = now()->startOfMonth()->addMonth();
            $endDate   = $isKos ? null : (
                $cycle === 'yearly'
                    ? $startDate->copy()->addYears($duration)
                    : $startDate->copy()->addMonths($duration)
            );

            $contractNumber = 'REODA-CTR-' . now()->format('Y') . '-' . strtoupper(Str::random(6));

            $contract = LeaseContract::create([
                'contract_number'   => $contractNumber,
                'tenant_id'         => Auth::id(),
                'unit_id'           => $unit->id,
                'manager_id'        => $property->manager_id,
                'start_date'        => $startDate->toDateString(),
                'end_date'          => $endDate?->toDateString(),
                'rental_type'       => 'monthly',
                'payment_cycle'     => $cycle,
                'contract_duration' => $duration,
                'tolerance_days'    => $isKos ? ($request->tolerance_days ?? 7) : 7,
                'rent_amount'       => $rentAmount,
                'deposit_amount'    => 0,
                'status'            => 'awaiting_approval',
                'tenant_sign_at'    => now(),
                'notes'             => $property->property_terms ?? '',
            ]);

            // Mark unit as occupied (optimistic lock — manager can revert)
            $unit->update(['status' => 'occupied']);

            DB::commit();

            // Notify manager via NotificationService (handles DB notification, email, and logging safely)
            if ($property->manager) {
                app(\App\Services\NotificationService::class)->send(
                    $property->manager,
                    'Pengajuan Kontrak Baru',
                    Auth::user()->name . ' mengajukan kontrak untuk unit ' . $unit->name . ' di ' . $property->name . '. Silakan tinjau dan setujui.',
                    'contract_requested',
                    route('manager.contracts.show', $contract->id),
                    $contract
                );
            }

            return redirect()->route('tenant.contract.show')
                ->with('success', 'Pengajuan kontrak berhasil dikirim! Tunggu persetujuan dari pengelola.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Contract Request Error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi: ' . $e->getMessage())->withInput();
        }
    }
}
