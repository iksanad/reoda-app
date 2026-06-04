<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Exports\PropertyExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::where('manager_id', Auth::id())
                          ->with('units')
                          ->withCount([
                              'units',
                              'units as rented_units_count'      => fn($q) => $q->where('status', 'occupied'),
                              'units as maintenance_units_count'  => fn($q) => $q->where('status', 'maintenance'),
                          ])
                          ->orderBy('created_at', 'desc')
                          ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $properties = $query->paginate(15)->appends(request()->query());

        // Stat cards
        $stats = [
            'total_properties'  => Property::where('manager_id', Auth::id())->count(),
            'total_units'       => \App\Models\Unit::whereHas('property', fn($q) => $q->where('manager_id', Auth::id()))->count(),
            'rented_units'      => \App\Models\Unit::whereHas('property', fn($q) => $q->where('manager_id', Auth::id()))->where('status', 'occupied')->count(),
            'maintenance_units' => \App\Models\Unit::whereHas('property', fn($q) => $q->where('manager_id', Auth::id()))->where('status', 'maintenance')->count(),
        ];

        return view('manager.properties.index', compact('properties', 'stats'));
    }

    public function create()
    {
        if (Auth::user()->manager_status === 'rejected') {
            return redirect()->route('manager.properties.index')->with('error', 'Akses dicabut: Anda tidak diizinkan menambahkan properti baru.');
        }

        return view('manager.properties.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->manager_status === 'rejected') {
            abort(403, 'Akses dicabut.');
        }
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => 'required|in:kos,kontrakan,apartemen,rumah',
            'description'             => 'nullable|string',
            'property_terms'          => 'nullable|string',
            'yearly_discount_percent' => 'nullable|numeric|min:0|max:50',
            'address'                 => 'required|string',
            'province'                => 'required|string|max:100',
            'city'                    => 'required|string|max:100',
            'district'                => 'nullable|string|max:100',
            'village'                 => 'nullable|string|max:100',
            'rt_rw'                   => 'nullable|string|max:20',
            'postal_code'             => 'nullable|string|max:10',
            'latitude'                => 'nullable|numeric',
            'longitude'               => 'nullable|numeric',
            'maps_url'                => 'nullable|string|max:500',
        ]);

        $validated['manager_id'] = Auth::id();
        $validated['status'] = 'active';
        $validated['yearly_discount_percent'] = $validated['yearly_discount_percent'] ?? 0;

        $property = Property::create($validated);

        return redirect()->route('manager.properties.index')->with('success', 'Lokasi properti berhasil ditambahkan.');
    }

    public function show(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        $property->load(['units' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        return view('manager.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        return view('manager.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => 'required|in:kos,kontrakan,apartemen,rumah',
            'description'             => 'nullable|string',
            'property_terms'          => 'nullable|string',
            'yearly_discount_percent' => 'nullable|numeric|min:0|max:50',
            'address'                 => 'required|string',
            'province'                => 'required|string|max:100',
            'city'                    => 'required|string|max:100',
            'district'                => 'nullable|string|max:100',
            'village'                 => 'nullable|string|max:100',
            'rt_rw'                   => 'nullable|string|max:20',
            'postal_code'             => 'nullable|string|max:10',
            'status'                  => 'required|in:active,inactive',
            'latitude'                => 'nullable|numeric',
            'longitude'               => 'nullable|numeric',
            'maps_url'                => 'nullable|string|max:500',
        ]);

        $property->update($validated);

        return redirect()->route('manager.properties.show', $property)->with('success', 'Properti berhasil diperbarui.');
    }

    public function destroy(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        $property->delete();

        return redirect()->route('manager.properties.index')->with('success', 'Lokasi properti berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = Property::where('manager_id', Auth::id())
                          ->withCount([
                              'units',
                              'units as rented_units_count'      => fn($q) => $q->where('status', 'occupied'),
                          ])
                          ->orderBy('created_at', 'desc')
                          ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $properties = $query->get();

        return Excel::download(new PropertyExport($properties), 'data_lokasi_properti.xlsx');
    }
}
