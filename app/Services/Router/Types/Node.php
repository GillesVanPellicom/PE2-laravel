<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use InvalidArgumentException;

class Node {
  private string $UUID;
  private array $attributes;
  private NodeType $type;

  public function __construct(string $UUID, NodeType $type, array $attributes = []) {
    if (empty($UUID)) {
      throw new InvalidArgumentException("Node UUID cannot be empty.");
    }

    $this->UUID = $UUID;
    $this->type = $type;
    $this->attributes = $attributes;
  }

  public function getUUID(): string {
    return $this->UUID;
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

  public function getDistanceTo(Node $node): float {
    return GeoMath::sphericalCosinesDistance(
      $this->attributes['latRad'],
      $this->attributes['longRad'],
      $node->getAttribute('latRad'),
      $node->getAttribute('longRad'));
  }

  public function getType(): NodeType {
    return $this->type;
  }

  public function setType(NodeType $type): void {
    $this->type = $type;
  }

}