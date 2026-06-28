<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'admin_fee',
        'amount_transferred',
        'bank_name',
        'bank_account',
        'account_name',
        'status', // PENDING, PROCESSING, SUCCESS, FAILED, REJECTED, CANCELLED
        'processed_by',
        'processed_at',
        'rejection_reason',
        'iris_reference_no',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
