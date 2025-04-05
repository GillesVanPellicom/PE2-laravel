<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFunction extends Model
{
    protected $table = 'functions';
    protected $fillable = ['name', 'role', 'description', 'salary_min', 'salary_max'];

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class, 'function_id');
    }
}
