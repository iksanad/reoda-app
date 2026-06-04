<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // SALE, WITHDRAW, WITHDRAW_REVERSAL, REFUND, ADJUSTMENT, credit, debit
        'amount',
        'balance_after',
        'reference_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
