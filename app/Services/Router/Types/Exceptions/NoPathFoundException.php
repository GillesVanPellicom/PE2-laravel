<?php

namespace App\Services\Router\Types\Exceptions;

use App\Services\Router\Types\Exceptions;

class NoPathFoundException extends RouterException {
  public function __construct(string $startNodeID, string $endNodeID) {
    parent::__construct("No path found between $startNodeID and $endNodeID. The graph may be disconnected. Check if all edges are defined correctly.");
  }
}