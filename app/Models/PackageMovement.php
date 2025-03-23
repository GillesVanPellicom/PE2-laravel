<?php

namespace App\Models;

use App\View\Components\Courier;
use App\Models\RouterEdges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    'node_id',
    'current_node_id',
    'next_movement',
    'router_edge_id'
  ];

  public function package() {
    return $this->belongsTo(Package::class, 'package_id');
  }

  public function handledByCourier() {
    return $this->belongsTo(Courier::class, 'handled_by_courier_id');
  }

  public function vehicle() {
    return $this->belongsTo(Vehicle::class, 'vehicle_id');
  }

  public function node() {
    return $this->belongsTo(Location::class, 'node_id');
  }

  public function nextHop() {
    return $this->belongsTo(PackageMovement::class, 'next_hop');
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

  public function toLocation()
  {
    return $this->belongsTo(Location::class, 'current_node_id', 'id');
  }
}
