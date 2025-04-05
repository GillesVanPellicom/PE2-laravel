<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'contract_id';
                                          //function_id
    protected $fillable = ['employee_id', 'job_id', 'start_date', 'end_date', 'location_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function function()
    {
        return $this->belongsTo(EmployeeFunction::class, 'job_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}

