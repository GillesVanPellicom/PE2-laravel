<?php

namespace App\Services\Router\Types\Exceptions;


class InvalidCoordinateException extends RouterException {
  public function __construct(string $nodeID, string $coordinateKind, float $value) {
    $message = "Node (ID: $nodeID) $coordinateKind must be between ";
    $message .= $coordinateKind === 'latitude' ? "-90 and 90 degrees" : "-180 and 180 degrees";
    $message .= ". Value provided: $value";
    parent::__construct($message);
  }
}