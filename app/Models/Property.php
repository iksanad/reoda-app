<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $manager_id
 * @property string $property_code
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property string $address
 * @property string|null $rt_rw
 * @property string $village
 * @property string $district
 * @property string $city
 * @property string $province
 * @property string|null $postal_code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $cover_image
 * @property string $status
 * @property string|null $property_terms
 * @property float|null $yearly_discount_percent
 * @property float|null $ipl_amount
 * @property string|null $maps_url
 * @property string $electricity_config
 * @property string $water_config
 * @property-read \App\Models\User $manager
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Unit[] $units
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Facility[] $facilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyMedia[] $media
 * @property-read string $cover_image_url
 */
class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'manager_id',
        'property_code',
        'name',
        'type',
        'description',
        'address',
        'rt_rw',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'cover_image',
        'status',
        'property_terms',
        'yearly_discount_percent',
        'ipl_amount',
        'maps_url',
        'electricity_config',
        'water_config',
    ];

    /** Apakah pengelola perlu menginput tagihan listrik manual tiap bulan? */
    public function needsElectricityBilling(): bool
    {
        return $this->electricity_config === 'postpaid';
    }

    /** Apakah pengelola perlu menginput tagihan air manual tiap bulan? */
    public function needsWaterBilling(): bool
    {
        return $this->water_config === 'postpaid' || ($this->type === 'apartemen');
    }

    /** Apakah properti ini mengenakan IPL/Maintenance Fee? */
    public function needsIplBilling(): bool
    {
        return $this->type === 'apartemen';
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'property_facilities');
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : asset('template/logo/Reoda-4C74AF.png'); // Fallback logo
    }
}