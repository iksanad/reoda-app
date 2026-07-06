<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class ExplorePublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::where('status', 'active')
            ->whereHas('manager', fn($q) => $q->where('manager_status', 'approved'))
            ->withCount([
                'units',
                'units as available_units_count' => fn($q) => $q->where('status', 'available'),
            ])
            ->with(['manager:id,name', 'media', 'units' => fn($q) => $q->where('status', 'available')->orderBy('rent_price')]);

        // Filter
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('city'))   $query->where('city', 'like', "%{$request->city}%");
        if ($request->filled('province')) $query->where('province', 'like', "%{$request->province}%");

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('city', 'like', "%{$s}%")
                ->orWhere('province', 'like', "%{$s}%")
                ->orWhere('address', 'like', "%{$s}%")
            );
        }

        if ($request->filled('min_price')) {
            $query->whereHas('units', fn($q) => $q->where('rent_price', '>=', $request->min_price)->where('status', 'available'));
        }
        if ($request->filled('max_price')) {
            $query->whereHas('units', fn($q) => $q->where('rent_price', '<=', $request->max_price)->where('status', 'available'));
        }

        // Sort
        match($request->get('sort', 'latest')) {
            'price_asc'  => $query->orderByRaw('(SELECT MIN(rent_price) FROM units WHERE units.property_id = properties.id AND units.status = "available") ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(rent_price) FROM units WHERE units.property_id = properties.id AND units.status = "available") DESC'),
            default      => $query->latest(),
        };

        $properties = $query->paginate(12)->appends($request->query());

        // Build province -> cities grouped map for dynamic filtering
        $locations = Property::where('status','active')
            ->whereHas('manager', fn($q) => $q->where('manager_status','approved'))
            ->select('province', 'city')
            ->distinct()
            ->get();

        $citiesByProvince = [];
        $allCities = [];
        foreach ($locations as $loc) {
            $prov = $loc->province ?: 'Lainnya';
            $city = $loc->city ?: 'Lainnya';
            if (!isset($citiesByProvince[$prov])) $citiesByProvince[$prov] = [];
            if (!in_array($city, $citiesByProvince[$prov])) $citiesByProvince[$prov][] = $city;
            if (!in_array($city, $allCities)) $allCities[] = $city;
        }
        ksort($citiesByProvince);
        foreach ($citiesByProvince as &$cArr) sort($cArr);
        sort($allCities);
        $provinces = array_keys($citiesByProvince);

        // Also keep flat cities for backward compat
        $cities = $allCities;

        // Stats for hero
        $totalProps   = Property::where('status','active')->count();
        $totalCities  = Property::where('status','active')->distinct()->count('city');
        $totalAvail   = \App\Models\Unit::where('status','available')->count();

        return view('public.explore', compact(
            'properties', 'cities', 'provinces', 'citiesByProvince', 'allCities',
            'totalProps', 'totalCities', 'totalAvail'
        ));
    }
}
