<?php

namespace App\Models;

use App\View\Components\Courier;
use App\Models\RouterEdges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class PackageMovement extends Model {
  use HasFactory;

  protected $fillable = [
    'package_id',
    'handled_by_courier_id',
    'vehicle_id',
    'departure_time',
    'arrival_time',
    'check_in_time',
    'check_out_time',
    'current_node_id',
    'next_movement',
    'router_edge_id'
  ];

  public function package() {
    return $this->belongsTo(Package::class, 'package_id');
  }

  public function handledByCourier() {
    return $this->belongsTo(User::class, 'handled_by_courier_id');
  }

  public function vehicle() {
    return $this->belongsTo(Vehicle::class, 'vehicle_id');
  }

  public function node() {
    return $this->belongsTo(Location::class, 'current_node_id');
  }

  public function nextHop() {
    return $this->belongsTo(PackageMovement::class, 'next_movement');
  }

  public function routerEdge() {
    return $this->belongsTo(RouterEdges::class, 'router_edge_id');
  }

  public function getHopDepartedAttribute(): bool {
    return !is_null($this->departure_time);
  }

  public function getHopArrivedAttribute(): bool {
    return !is_null($this->arrival_time);
  }

  /*
  public function toLocation()
  {
    return $this->belongsTo(RouterNodes::class, 'to_node_id', 'id');
  }

  public function fromLocation()
  {
    return $this->belongsTo(RouterNodes::class, 'from_node_id', 'id');
  }*/

  public function getArrivedAt()
  {
    return $this->arrival_time;
  }

  public function getCheckedInAt()
  {
    return $this->check_in_time;
  }

  public function getCheckedOutAt()
  {
    return $this->check_out_time;
  }

  public function getDepartedAt()
  {
    return $this->departure_time;
  }
}
