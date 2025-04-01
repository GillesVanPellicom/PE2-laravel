<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'leave_balance', 'team_id'];

    public function contracts()
    {
        return $this->hasOne(EmployeeContract::class, 'employee_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'employee_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

}
