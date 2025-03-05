<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['street', 'house_number', 'cities_id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'cities_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'address_id');
    }
}
