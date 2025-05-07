<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [
        "location_id",
        "name",
        "city_id",
    ];
}
