<?php

namespace App\Services\Router\Types\Exceptions;

use App\Services\Router\Types\Exceptions;

class NodeNotFoundException extends RouterException {
  public function __construct(string $nodeID) {
    parent::__construct("Node (ID: $nodeID) does not exist.");
  }
}