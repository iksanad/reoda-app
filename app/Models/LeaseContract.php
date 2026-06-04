<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'tenant_id',
        'unit_id',
        'manager_id',
        'start_date',
        'end_date',
        'rental_type',
        'contract_duration',
        'payment_cycle',
        'tolerance_days',
        'rent_amount',
        'deposit_amount',
        'status',
        'contract_document',
        'notes',
        'terminated_at',
        'termination_reason',
        'tenant_sign_at',
        'manager_approved_at',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'end_date'           => 'date',
        'terminated_at'      => 'datetime',
        'tenant_sign_at'     => 'datetime',
        'manager_approved_at'=> 'datetime',
    ];

    /**
     * Check if this is a kos-type contract (no fixed end date, monthly auto-renew).
     */
    public function getIsKosAttribute(): bool
    {
        return $this->unit && $this->unit->property && $this->unit->property->type === 'kos';
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
