<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'property_id'=>'required'
        ]);

        $data['tenant_id'] = auth()->id();

        return Rental::create($data);
    }
}