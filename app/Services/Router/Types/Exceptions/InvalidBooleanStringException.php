<?php

namespace App\Services\Router\Types\Exceptions;

class InvalidBooleanStringException extends RouterException {
  public function __construct(string $nodeID, string $field) {
    parent::__construct("$field (ID: $nodeID) must be 'true' or 'false'.");
  }
}