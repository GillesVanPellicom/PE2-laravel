<?php

namespace App\Services\Router\Types;

use InvalidArgumentException;

class Node {
  private string $name;
  private array $attributes;

  public function __construct(string $name, array $attributes = []) {
    if (empty($name)) {
      throw new InvalidArgumentException("Node name cannot be empty.");
    }

    $this->name = $name;
    $this->attributes = $attributes;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getAttributes(): array {
    return $this->attributes;
  }

  public function getAttribute(string $key) {
    if (empty($key)) {
      throw new InvalidArgumentException("Attribute key cannot be empty.");
    }
    return $this->attributes[$key] ?? null;
  }

  public function setAttribute(string $key, $value): void {
    if (empty($key)) {
      throw new InvalidArgumentException("Attribute key cannot be empty.");
    }
    $this->attributes[$key] = $value;
  }
}