<?php

namespace App\Services\Router\Types\Exceptions;


class InvalidCoordinateException extends RouterException {
  public function __construct(string $nodeID, string $coordinateType, float $value) {
    $message = "Node (ID: $nodeID) $coordinateType must be between ";
    $message .= $coordinateType === 'latitude' ? "-90 and 90 degrees" : "-180 and 180 degrees";
    $message .= ". Value provided: $value";
    parent::__construct($message);
  }
}