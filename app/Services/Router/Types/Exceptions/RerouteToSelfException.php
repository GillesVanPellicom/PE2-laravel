<?php

namespace App\Services\Router\Types\Exceptions;


use App\Models\Location;

class RerouteToSelfException extends RouterException {
  public function __construct(Location|string $destination) {

    if ($destination instanceof Location) {
      $destination = "Location {$destination->id}";
    }
    parent::__construct("Rerouting to self is not allowed. (destination: $destination) (Returning counts as rerouting)");
  }
}