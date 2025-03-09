<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class airport extends Model
{
    protected $fillable = [
        "location_id",
        "name",
    ];
}