<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address', // Single address field
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}