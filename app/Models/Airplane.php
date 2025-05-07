<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airplane extends Model
{
    use HasFactory;
    protected $fillable = [
        'airline_id',
        'model',
        'capacity',
        'status',
    ];

}