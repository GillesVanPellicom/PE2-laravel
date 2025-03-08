<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contract extends Model
{
    protected $fillable = [
        "airline",
        "flight",
        "weight",
        "room"
    ];
}