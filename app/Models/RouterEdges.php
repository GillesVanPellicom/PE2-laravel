<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouterEdges extends Model {
  protected $table = 'router_edges';

  protected $fillable = [
    'origin_node',
    'destination_node',
    'weight',
    'isUniDirectional',
    'validFrom',
    'validTo',
  ];

  public function originNode() {
    return $this->belongsTo(RouterNodes::class, 'origin_node');
  }

  public function destinationNode() {
    return $this->belongsTo(RouterNodes::class, 'destination_node');
  }
}