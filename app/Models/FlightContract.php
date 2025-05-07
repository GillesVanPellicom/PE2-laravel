<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightContract extends Model
{
    use HasFactory;

    protected $table = '_flight__contracts'; // Explicitly specify the table name

    protected $fillable = [
        'flight_id',
        'airline_id',
        'max_capacity',
        'price',
        'start_date',
        'end_date',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function isExpired()
    {
        return $this->end_date && now()->greaterThan($this->end_date);
    }
}
