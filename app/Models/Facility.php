<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'category',
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_facilities');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_facilities');
    }
}
