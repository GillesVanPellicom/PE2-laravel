<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    
    protected $fillable = ['name', 'postcode', 'country_id'];
    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    
    public function addresses()
    {
        return $this->hasMany(Address::class, 'cities_id');
    }
}
