<?php

namespace App\Models;

use App\Services\Router\Router;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\Node;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;

use Illuminate\Support\Facades\App;

class Package extends Model {
  use HasFactory;

  protected $primaryKey = 'id'; // Custom primary key
  protected $fillable = [
    'reference',
    'user_id',
    'origin_location_id',
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
    'destination_location_id' => null,
    'addresses_id' => null
  ];

  public function user() {
    return $this->belongsTo(User::class, "user_id");
  }

  public function weightClass() {
    return $this->belongsTo(WeightClass::class, 'weight_id');
  }

  public function deliveryMethod() {
    return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
  }

  public function destinationLocation() {
    return $this->belongsTo(Location::class, 'destination_location_id');
  }

  public function address() {
    return $this->belongsTo(Address::class, 'addresses_id');
  }

  public function originLocation() {
    return $this->belongsTo(Location::class, 'origin_location_id');
  }

  public function movements() {
    return $this->hasMany(PackageMovement::class, 'package_id');
  }

  /**
   * @return Node[]|null
   * @throws RouterException
   * @throws InvalidRouterArgumentException
   * @throws NodeNotFoundException
   * @throws InvalidCoordinateException
   * @throws NoPathFoundException
   */
  public function getMovements(): ?array {
    /** @var Router $router */
    $router = App::make(Router::class);
    return $router->getPath($this->getAttribute('originLocation'), $this->getAttribute('destinationLocation'));
  }
}
