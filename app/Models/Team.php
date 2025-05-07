<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use hasFactory;
    protected $fillable = ['department', 'manager_id'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'team_id');
    }
    
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
