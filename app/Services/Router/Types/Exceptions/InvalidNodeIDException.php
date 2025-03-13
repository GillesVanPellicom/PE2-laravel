<?php

namespace App\Services\Router\Types\Exceptions;

class InvalidNodeIDException extends RouterException {
  public function __construct() {
    parent::__construct("Node ID cannot be empty.");
  }
}