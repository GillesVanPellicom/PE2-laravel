<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightClass extends Model
{
    protected $fillable = ['name', 'weight_min', 'weight_max', 'price', 'is_active'];
} 