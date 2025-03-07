<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'vacation_type', 'start_date', 'end_date', 'approve_status'
    ];
    
    // Define the relationship with Employee (optional)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
