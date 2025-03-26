<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;
    protected $primaryKey = 'vacation_id'; 
    protected $fillable = [
        'employee_id', 'vacation_type', 'start_date', 'end_date', 'approve_status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacation()
    {
        return $this->belongsTo(Vacation::class); // Make sure you use the correct relationship
    }
}

