<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'birth_date', 'address_id', 'nationality', 'leave_balance', 'city_id'];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
