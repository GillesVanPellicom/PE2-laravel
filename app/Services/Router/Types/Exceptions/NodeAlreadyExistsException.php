<?php

namespace App\Services\Router\Types\Exceptions;


class NodeAlreadyExistsException extends RouterException {
  public function __construct(string $nodeID) {
    parent::__construct("Node (ID: $nodeID) already exists. IDs must be unique.");
  }
}