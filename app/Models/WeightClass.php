<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightClass extends Model
{
    protected $fillable = ['code', 'name', 'weight_min', 'weight_max', 'price', 'is_active'];
} 