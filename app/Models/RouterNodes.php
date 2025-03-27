<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Router\Types\NodeType;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|RouterNodes find(mixed $id)
 */
class RouterNodes extends Model {
  protected $table = 'router_nodes';
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  public $incrementing = false;

  protected $casts = [
    'location_type' => NodeType::class,
  ];

  protected $fillable = [
    'id',
    'city_id',
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

  public function getDescription(): string
  {
      return $this->description ?? 'No description available';
  }
}