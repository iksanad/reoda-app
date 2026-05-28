<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Show the compare selection + result page.
     * Query params: prop1=ID, prop2=ID
     */
    public function index(Request $request)
    {
        $prop1 = null;
        $prop2 = null;

        if ($request->filled('prop1')) {
            $prop1 = Property::where('status', 'active')
                ->whereHas('manager', function($q) {
                    $q->where('manager_status', 'approved');
                })
                ->with(['manager:id,name,phone', 'facilities', 'units' => fn($q) => $q->orderBy('rent_price')])
                ->find($request->prop1);
        }

        if ($request->filled('prop2')) {
            $prop2 = Property::where('status', 'active')
                ->whereHas('manager', function($q) {
                    $q->where('manager_status', 'approved');
                })
                ->with(['manager:id,name,phone', 'facilities', 'units' => fn($q) => $q->orderBy('rent_price')])
                ->find($request->prop2);
        }

        // All active properties for the selector dropdowns
        $properties = Property::where('status', 'active')
            ->whereHas('manager', function($q) {
                $q->where('manager_status', 'approved');
            })
            ->withCount(['units', 'units as available_units_count' => fn($q) => $q->where('status', 'available')])
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'type', 'cover_image']);

        return view('compare.index', compact('prop1', 'prop2', 'properties'));
    }
}
