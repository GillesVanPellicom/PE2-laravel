<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'location_type',
        'name',
        'address',
        'contact_number',
        'opening_hours',
        'is_active'
    ];

    public function parcels()
    {
        return $this->hasMany(Package::class, 'destination_location_id');
    }
} 