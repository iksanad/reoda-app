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

        // Query for active properties in selector dropdowns
        $query = Property::where('status', 'active')
            ->whereHas('manager', function($q) {
                $q->where('manager_status', 'approved');
            });

        // Apply filters
        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }
        if ($request->filled('filter_city')) {
            $query->where('city', $request->filter_city);
        }
        if ($request->filled('filter_province')) {
            $query->where('province', $request->filter_province);
        }

        $properties = $query->withCount(['units', 'units as available_units_count' => fn($q) => $q->where('status', 'available')])
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'type', 'cover_image']);

        // Data for filter options
        $locations = Property::where('status', 'active')
            ->whereHas('manager', fn($q) => $q->where('manager_status','approved'))
            ->select('province', 'city')
            ->distinct()
            ->get();

        $citiesByProvince = [];
        $allCities = [];
        foreach ($locations as $loc) {
            $prov = $loc->province ?: 'Lainnya';
            $city = $loc->city ?: 'Lainnya';
            if (!isset($citiesByProvince[$prov])) {
                $citiesByProvince[$prov] = [];
            }
            if (!in_array($city, $citiesByProvince[$prov])) {
                $citiesByProvince[$prov][] = $city;
            }
            if (!in_array($city, $allCities)) {
                $allCities[] = $city;
            }
        }
        ksort($citiesByProvince);
        foreach($citiesByProvince as &$cArr) {
            sort($cArr);
        }
        sort($allCities);
        
        $provinces = array_keys($citiesByProvince);

        return view('compare.index', compact('prop1', 'prop2', 'properties', 'provinces', 'citiesByProvince', 'allCities'));
    }
}
