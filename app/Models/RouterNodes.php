<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Router\Types\NodeType;

class RouterNodes extends Model {
  protected $table = 'router_nodes';

  protected $casts = [
    'location_type' => NodeType::class,
  ];

  protected $fillable = [
    'id',
    'description',
    'location_type',
    'latDeg',
    'lonDeg',
    'isEntry',
    'isExit',
  ];

  public function originEdges() {
    return $this->hasMany(RouterEdges::class, 'origin_node');
  }

  public function destinationEdges() {
    return $this->hasMany(RouterEdges::class, 'destination_node');
  }
}