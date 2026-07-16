<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::where('status', 'active')
            ->whereHas('manager', function($q) {
                $q->where('manager_status', 'approved');
            })
            ->withCount(['units', 'units as available_units_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->with(['manager:id,name', 'media']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        // Search by name, address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter: only show properties with at least 1 available unit (defaults to true)
        $availableFilter = $request->input('available', '1');
        if ($availableFilter == '1') {
            $query->whereHas('units', function ($q) {
                $q->where('status', 'available');
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match($sort) {
            'oldest'  => $query->oldest(),
            'price_asc' => $query->orderByRaw('(SELECT MIN(rent_price) FROM units WHERE units.property_id = properties.id AND units.status = "available") ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(rent_price) FROM units WHERE units.property_id = properties.id AND units.status = "available") DESC'),
            default   => $query->latest(),
        };

        $properties = $query->paginate(12)->appends(request()->query());

        // For city filter dropdown
        $cities = Property::where('status', 'active')
            ->whereHas('manager', function($q) {
                $q->where('manager_status', 'approved');
            })
            ->distinct()->pluck('city')->sort()->values();

        return view('tenant.explore.index', compact('properties', 'cities'));
    }

    public function show(Property $property)
    {
        if ($property->status !== 'active' || optional($property->manager)->manager_status !== 'approved') {
            abort(404);
        }

        $property->load([
            'manager:id,name,phone',
            'media',
            'units' => function ($q) {
                $q->orderBy('status')->orderBy('unit_code');
            }
        ]);

        $availableUnits = $property->units->where('status', 'available');
        $rentedUnits    = $property->units->where('status', 'rented');

        return view('tenant.explore.show', compact('property', 'availableUnits', 'rentedUnits'));
    }
}
