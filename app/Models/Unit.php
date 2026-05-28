<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'unit_code',
        'name',
        'type',
        'rent_price',
        'area_sqm',
        'floor',
        'status',
        'description',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'unit_facilities');
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function leaseContracts()
    {
        return $this->hasMany(LeaseContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(LeaseContract::class)
                    ->where('status', 'active')
                    ->latest();
    }
}
