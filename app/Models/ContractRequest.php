<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_contract_id',
        'tenant_id',
        'manager_id',
        'type',
        'requested_date',
        'reason',
        'status',
        'manager_response',
        'responded_at',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'responded_at'   => 'datetime',
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
}
