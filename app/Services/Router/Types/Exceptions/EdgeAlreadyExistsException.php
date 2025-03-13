<?php

namespace App\Services\Router\Types\Exceptions;


class EdgeAlreadyExistsException extends RouterException {
  public function __construct(string $nodeID_1, string $nodeID_2) {
    parent::__construct("Edge ($nodeID_1 ↔ $nodeID_2) already exists. Edges must be unique.");
  }
}