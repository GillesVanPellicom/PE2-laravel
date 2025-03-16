<?php

namespace App\Models;

use App\Services\Router\Types\NodeType;
use Illuminate\Database\Eloquent\Model;

class RouterNodes extends Model {
  protected $table = 'router_nodes';

  protected $casts = [
    'location_type' => NodeType::class,
  ];
}
