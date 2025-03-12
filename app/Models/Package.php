<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;
use App\Models\Addresses;
use App\Models\Customer;

class Package extends Model {
    use HasFactory;

    protected $primaryKey = 'id'; // Custom primary key
    protected $fillable = [
        'reference',
        'user_id',
        'origin_location_id',
        'current_location_id',
        'destination_location_id',
        'addresses_id',
        'status',
        'name',
        'lastName',
        'receiverEmail',
        'receiver_phone_number',
        'weight_id',
        'delivery_method_id',
        'dimension',
        'weight_price',
        'delivery_price'
    ];

    protected $attributes = [
        'status' => 'pending',
        'current_location_id' => null,
        'destination_location_id' => null,
        'addresses_id' => null
    ];

    public function weightClass()
    {
        return $this->belongsTo(WeightClass::class, 'weight_id');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'addresses_id');
    }

    public function originLocation()
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function currentLocation()
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }
    
    public function movements()
{
    return $this->hasMany(PackageMovement::class, 'package_id');
}

}
