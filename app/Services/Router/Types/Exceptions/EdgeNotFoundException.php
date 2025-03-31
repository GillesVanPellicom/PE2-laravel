<?php

namespace App\Services\Router\Types\Exceptions;

use App\Services\Router\Types\Exceptions;

class EdgeNotFoundException extends RouterException {
  public function __construct(string $nodeID_1, string $nodeID_2) {
    parent::__construct("Cannot remove edge ($nodeID_1 ↔ $nodeID_2) since it doesn't exist.");
  }
}