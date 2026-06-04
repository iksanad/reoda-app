<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PublicPropertyController extends Controller
{
    public function show(string $propertyCode)
    {
        $property = Property::where('property_code', $propertyCode)
            ->where('status', 'active')
            ->with(['units' => function ($q) {
                $q->where('status', 'available')->orderBy('unit_code');
            }, 'facilities', 'media', 'manager'])
            ->firstOrFail();

        return view('public.property-detail', compact('property'));
    }
}
