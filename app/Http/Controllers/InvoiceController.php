<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function create($rental_id)
    {
        return Invoice::create([
            'rental_id'=>$rental_id,
            'amount'=>500000,
            'due_date'=>now()->addDays(7),
            'status'=>'unpaid'
        ]);
    }

    public function index()
    {
        return Invoice::all();
    }
}