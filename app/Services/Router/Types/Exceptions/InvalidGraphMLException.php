<?php

namespace App\Services\Router\Types\Exceptions;

class InvalidGraphMLException extends RouterException {
  public function __construct(string $filePath) {
    parent::__construct("Failed to load GraphML file: $filePath");
  }
}