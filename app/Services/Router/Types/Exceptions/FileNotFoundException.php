<?php

namespace App\Services\Router\Types\Exceptions;


class FileNotFoundException extends RouterException {
  public function __construct(string $filePath) {
    parent::__construct("File not found: $filePath");
  }
}