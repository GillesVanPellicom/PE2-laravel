<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = '_flight__contracts';
    
    protected $fillable = [
        'flight_id',  // Flight ID (foreign key to the flights table)
        'airline_id', // Airline ID (foreign key to the airlines table)
        'max_capacity', // Max capacity for the flight contract
        'price' // Price for the contract
    ];
}