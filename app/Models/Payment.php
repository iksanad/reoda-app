<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'invoice_id',
        'tenant_id',
        'manager_id',
        'amount',
        'payment_method',
        'proof_of_payment',
        'bank_name',
        'bank_account',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'status',
        'paid_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofUrlAttribute()
    {
        return $this->proof_of_payment ? asset('storage/' . $this->proof_of_payment) : null;
    }
}
