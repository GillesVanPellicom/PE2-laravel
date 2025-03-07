<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street',
        'house_number',
        'cities_id',
        'country_id',
    ];    

    public function city()
    {
        return $this->belongsTo(Cities::class, 'cities_id');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }
}
