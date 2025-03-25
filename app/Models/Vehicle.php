<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_type', 'license_plate', 'capacity', 'status'
    ];
    public function packages()
    {
        return $this->hasMany(Package::class, 'vehicle_id');
    }
}
