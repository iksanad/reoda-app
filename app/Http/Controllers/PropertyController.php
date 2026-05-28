<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function store(Request $r)
    {
        if (auth()->user()->role !== 'owner') {
            return response()->json(['error'=>'Forbidden'],403);
        }

        $data = $r->validate([
            'name'=>'required',
            'city'=>'required',
            'price'=>'required'
        ]);

        $data['owner_id'] = auth()->id();

        return Property::create($data);
    }

    public function index()
    {
        return Property::with('facilities')->get();
    }

    public function show($id)
    {
        return Property::with('facilities')->findOrFail($id);
    }

    public function compare(Request $r)
    {
        $ids = explode(',', $r->ids);

        return Property::with('facilities')
            ->whereIn('property_id',$ids)
            ->get();
    }
}