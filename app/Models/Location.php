<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'location_type',
        'name',
        'address',
        'opening_hours',
        'is_active'
    ];

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'destination_location_id');
    }
} 