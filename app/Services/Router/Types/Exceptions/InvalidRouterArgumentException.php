<?php

namespace App\Services\Router\Types\Exceptions;

class InvalidRouterArgumentException extends RouterException {
  public function __construct(string $msg) {
    parent::__construct($msg);
  }
}