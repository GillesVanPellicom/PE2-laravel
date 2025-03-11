<?php

namespace App\Services\Router\Types\Exceptions;

class FailedCoordinatesFetchException extends RouterException {
  public function __construct(string $address) {
    parent::__construct("No data returned for address: $address. Is the address spelled correctly?");
  }
}