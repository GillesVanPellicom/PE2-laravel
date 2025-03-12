<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{

    use HasFactory; 

    protected $fillable = [
        'name',
        'location_type',
        'addresses_id',
        'contact_number',
        'opening_hours',
        'is_active'
    ];

    public function parcels()
    {
        return $this->hasMany(Package::class, 'destination_location_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'current_location_id');
    }
} 