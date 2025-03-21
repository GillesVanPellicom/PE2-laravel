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

    protected $fillable = ['code', 'name', 'description', 'requires_location', 'price', 'is_active'];

    public function packages () {
        return $this->hasMany(Package::class, 'delivery_method_id');

    }
}
