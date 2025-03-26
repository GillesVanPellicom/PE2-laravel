<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable =   [
      'employees_id',
        'vehicle_id',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
    public function packageMovements () {
        return $this->hasMany(PackageMovement::class, 'handled_by_courier_id');
    }
}
