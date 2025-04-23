<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $table = 'flights';

    protected $primaryKey = 'id'; // Explicitly set the primary key

    public $incrementing = true; // Since it's an auto-incrementing ID
    protected $keyType = 'int'; // Ensure it's an integer

    protected $fillable = [
        'airplane_id',
        'departure_time',
        'arrival_time',
        'depart_location_id', // Fix the name
        'arrive_location_id',
        'delayed_minutes',
        'isActive',
        'status'
    ];

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'depart_location_id', 'location_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrive_location_id', 'location_id');
    }

    public function departureLocation()
    {
        return $this->belongsTo(Location::class, 'depart_location_id', 'id');
    }

    public function arrivalLocation()
    {
        return $this->belongsTo(Location::class, 'arrive_location_id', 'id');
    }

    public function contract()
    {
        return $this->hasOne(FlightContract::class, 'flight_id', 'id');
    }
}