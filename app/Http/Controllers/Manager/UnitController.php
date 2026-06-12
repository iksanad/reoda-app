<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    // Normally accessed via nested route properties/{property}/units/create
    public function create(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        return view('manager.units.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'unit_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'rent_price' => 'required|numeric|min:0',
            'area_sqm' => 'nullable|numeric|min:0',
            'floor' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'pln_customer_id' => 'nullable|string|max:50',
            'pdam_customer_id' => 'nullable|string|max:50',
        ]);

        $validated['property_id'] = $property->id;
        $validated['status'] = 'available';

        $property->units()->create($validated);

        return redirect()->route('manager.properties.show', $property)->with('success', 'Unit/kamar berhasil ditambahkan.');
    }

    public function show(Unit $unit)
    {
        if ($unit->property->manager_id !== Auth::id()) {
            abort(403);
        }

        $unit->load([
            'property',
            'activeContract.tenant',
            'leaseContracts' => fn($q) => $q->with('tenant')->orderBy('created_at', 'desc'),
        ]);

        return view('manager.units.show', compact('unit'));
    }

    // Edit is standalone route: units/{unit}/edit
    public function edit(Unit $unit)
    {
        if ($unit->property->manager_id !== Auth::id()) {
            abort(403);
        }

        return view('manager.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        if ($unit->property->manager_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'unit_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'rent_price' => 'required|numeric|min:0',
            'area_sqm' => 'nullable|numeric|min:0',
            'floor' => 'nullable|string|max:50',
            'status' => 'required|in:available,rented,maintenance',
            'description' => 'nullable|string',
            'pln_customer_id' => 'nullable|string|max:50',
            'pdam_customer_id' => 'nullable|string|max:50',
        ]);

        $unit->update($validated);

        return redirect()->route('manager.properties.show', $unit->property_id)->with('success', 'Unit/kamar berhasil diupdate.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->property->manager_id !== Auth::id()) {
            abort(403);
        }

        $propertyId = $unit->property_id;
        $unit->delete();

        return redirect()->route('manager.properties.show', $propertyId)->with('success', 'Unit/kamar berhasil dihapus.');
    }
}
