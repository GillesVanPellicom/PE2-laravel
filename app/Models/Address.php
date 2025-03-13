<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = ['street', 'house_number', 'cities_id', 'bus_number'];

    public function parcels(): HasMany
    {
        return $this->hasMany(Package::class, 'addresses_id');
    }

    public function senderParcels(): HasMany
    {
        return $this->hasMany(Package::class, 'sender_address_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'addresses_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'cities_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'address_id');
    }
}
