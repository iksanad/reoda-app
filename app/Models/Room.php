<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $primaryKey = 'room_id';

    protected $fillable = [
        'property_id',
        'room_number',
        'price',
        'status'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
