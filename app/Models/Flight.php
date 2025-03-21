<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $table = 'flights';

    protected $primaryKey = 'flight_id'; // Explicitly set the primary key

    public $incrementing = true; // Since it's an auto-incrementing ID
    protected $keyType = 'int'; // Ensure it's an integer

    protected $fillable = [
        'airplane_id',
        'departure_time',
        'arrival_time',
        'depart_location_id', // Fix the name
        'arrive_location_id',
        'status'
    ];
}