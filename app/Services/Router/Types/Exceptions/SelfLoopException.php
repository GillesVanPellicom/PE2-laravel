<?php

namespace App\Services\Router\Types\Exceptions;

class SelfLoopException extends RouterException {
  public function __construct(string $nodeID) {
    parent::__construct("Start and end node (IDs: $nodeID) are the same.");
  }
}