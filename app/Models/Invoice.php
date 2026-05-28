<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'lease_contract_id',
        'tenant_id',
        'manager_id',
        'type',
        'billing_month',
        'billing_year',
        'amount',
        'meter_start',
        'meter_end',
        'price_per_unit',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function leaseContract()
    {
        return $this->belongsTo(LeaseContract::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
