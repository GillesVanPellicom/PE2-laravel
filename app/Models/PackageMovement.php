<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageMovement extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'package_id', 
        'from_location_id', 
        'to_location_id', 
        'handled_by_courier_id', 
        'vehicle_id', 
        'departure_time', 
        'arrival_time', 
        'check_in_time', 
        'check_out_time',
    ];
}
