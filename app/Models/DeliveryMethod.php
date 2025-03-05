<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'delivery_method';

    protected $fillable = ['code', 'name', 'description', 'price', 'is_active'];
} 