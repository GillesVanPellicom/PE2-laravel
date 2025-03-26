<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightClass extends Model
{
    protected $fillable = ['name', 'weight_min', 'weight_max', 'price', 'is_active'];

    public function packages()
    {
        return $this->hasMany(Package::class, 'weight_class_id');
    }
}





