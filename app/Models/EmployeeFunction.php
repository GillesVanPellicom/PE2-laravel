<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeFunction extends Model
{
    use HasFactory;
    protected $table = 'functions';
    protected $fillable = ['name', 'role', 'description', 'salary_min', 'salary_max'];

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class, 'function_id');
    }
}
