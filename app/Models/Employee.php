<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    //protected $table = 'employees';         // table name, needs to be defined if not following laravel conventions (table name is plural of model name)
    //protected $primaryKey = 'employee_id';  // primary key of table, needs to be defined if not following laravel conventions (primary key is named id)
    //public $timestamps = true;

    protected $fillable = [
        'name',
        'firstname',
        'email',
        'birthdate',
        // hire_date needs to be fillable if we want to be able to add employees that are not yet hired or were hired in the past (not auto generated NOW Date)
        'hire_date',
        'vacation_days',
        /*'street',
        'number',
        'city_id',
        'position_id',
        'department_id',
        'contract',
        'is_manager',
        'manager_id'*/
    ];

    /*public function city()
    {
        return $this->belongsTo(city::class);
    }

    public function position()
    {
        return $this->belongsTo(position::class);
    }

    public function department()
    {
        return $this->belongsTo(department::class);
    }*/
}
