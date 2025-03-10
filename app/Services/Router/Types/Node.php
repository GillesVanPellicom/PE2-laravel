<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use InvalidArgumentException;

class Node {

  // Local globals
  private string $ID;
  private array $attributes;
  private NodeType $type;


  /**
   * @param  string  $ID  ID of the Node
   * @param  NodeType  $type  Type of the Node
   * @param  array  $attributes  Associative array containing attributes of the Node
   */
  public function __construct(string $ID, NodeType $type, array $attributes = []) {
    if (empty($ID)) {
      throw new InvalidArgumentException("Node ID cannot be empty.");
    }

    $this->ID = $ID;
    $this->type = $type;
    $this->attributes = $attributes;
  }


  /**
   * @return string ID of the Node
   */
  public function getID(): string {
    return $this->ID;
  }


  /**
   * @return array Associative array containing attributes of the Node
   */
  public function getAttributes(): array {
    return $this->attributes;
  }


  /**
   * @param  string  $key  Key of the attribute to retrieve
   * @return mixed|null Value of the attribute if it exists, null otherwise
   */
  public function getAttribute(string $key): mixed {
    if (empty($key)) {
      throw new InvalidArgumentException("Attribute key cannot be empty.");
    }
    return $this->attributes[$key] ?? null;
  }


  /**
   * @param  string  $key  Key of the attribute to set
   * @param  string  $value  of the attribute to set
   * @return void
   */
  public function setAttribute(string $key, string $value): void {
    if (empty($key)) {
      throw new InvalidArgumentException("Attribute key cannot be empty.");
    }
    $this->attributes[$key] = $value;
  }


  /**
   * @param  Node  $node  Node to calculate the distance to
   * @return float Distance in km to the given Node
   */
  public function getDistanceTo(Node $node): float {
    return GeoMath::sphericalCosinesDistance(
      $this->attributes['latRad'],
      $this->attributes['longRad'],
      $node->getAttribute('latRad'),
      $node->getAttribute('longRad'));
  }


  /**
   * @return NodeType Type of the Node
   */
  public function getType(): NodeType {
    return $this->type;
  }

  /**
   * @param  NodeType  $type  Type of the Node
   * @return void
   */
  public function setType(NodeType $type): void {
    $this->type = $type;
  }

}