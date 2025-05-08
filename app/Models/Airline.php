<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'IATA_code',
        'contact_number',
        'headquarters_location',
    ];
}