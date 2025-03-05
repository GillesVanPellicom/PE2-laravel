<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Addresses extends Model
{
    protected $fillable = [
        'street',
        'number',
        'bus',
        'postal_code',
        'city',
        'country'
    ];

    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class, 'address_id');
    }

    public function senderParcels(): HasMany
    {
        return $this->hasMany(Parcel::class, 'sender_address_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'addresses_id');
    }
}
