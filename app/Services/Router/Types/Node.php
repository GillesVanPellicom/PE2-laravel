<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use RuntimeException;

class Node {

  // Local globals
  private string $ID;
  private array $attributes;
  private NodeType $type;

  /**
   * @param  string  $ID  ID of the Node
   * @param  NodeType  $type  Type of the Node
   * @param  array  $attributes  Associative array containing attributes of the Node
   * @throws InvalidRouterArgumentException If the node ID is empty
   */
  public function __construct(string $ID, NodeType $type, array $attributes = []) {
    if (empty($ID)) {
      throw new InvalidRouterArgumentException("Node ID cannot be empty.");
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
   * @throws InvalidRouterArgumentException If the attribute key is empty
   */
  public function getAttribute(string $key): mixed {
    if (empty($key)) {
      throw new InvalidRouterArgumentException("Attribute key cannot be empty.");
    }
    return $this->attributes[$key] ?? null;
  }

  /**
   * @param  string  $key  Key of the attribute to set
   * @param  string  $value  Value of the attribute to set
   * @return void
   * @throws InvalidRouterArgumentException If the attribute key is empty
   */
  public function setAttribute(string $key, string $value): void {
    if (empty($key)) {
      throw new InvalidRouterArgumentException("Attribute key cannot be empty.");
    }
    $this->attributes[$key] = $value;
  }

  /**
   * Calculates the distance to another node using spherical cosines
   *
   * @param  Node  $node  Node to calculate the distance to
   * @return float Distance in kilometers to the given Node
   * @throws InvalidRouterArgumentException If latitude or longitude attributes are missing or invalid in either node
   */
  public function getDistanceTo(Node $node): float {
    if (!isset($this->attributes['latRad']) || !isset($this->attributes['longRad'])) {
      throw new InvalidRouterArgumentException("Node {$this->ID} missing latitude or longitude attributes.");
    }
    $nodeLatRad = $node->getAttribute('latRad');
    $nodeLongRad = $node->getAttribute('longRad');
    if ($nodeLatRad === null || $nodeLongRad === null) {
      throw new InvalidRouterArgumentException("Target node {$node->getID()} missing latitude or longitude attributes.");
    }
    return GeoMath::sphericalCosinesDistance(
      $this->attributes['latRad'],
      $this->attributes['longRad'],
      $nodeLatRad,
      $nodeLongRad
    );
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

  /**
   * Prints the node details to the console
   *
   * @return void
   * @throws RuntimeException If required attributes (desc, latDeg, longDeg, isEntryNode, isExitNode) are missing
   * @throws InvalidRouterArgumentException
   */
  public function printNode(): void {
    echo "\033[32mNode: ".$this->getID()."\033[0m\n";
    echo "  Desc.      :  ".$this->getAttribute('desc')."\n";
    echo "  Latitude   :  ".sprintf("%.4f", $this->getAttribute('latDeg'))."\n";
    echo "  Longitude  :  ".sprintf("%.4f", $this->getAttribute('longDeg'))."\n";
    echo "  Type       :  ".$this->getType()->value."\n";
    echo "  Entry Node :  ".$this->getAttribute('isEntryNode')."\n";
    echo "  Exit Node  :  ".$this->getAttribute('isExitNode')."\n";
    echo "\033[33m--------------------\033[0m\n";
  }

  /**
   * Prints the address node details to the console
   *
   * @return void
   * @throws RuntimeException If required attributes (desc, latDeg, longDeg) are missing
   * @throws InvalidRouterArgumentException
   */
  public function printAddressNode(): void {
    echo "\033[32mImaginary node: ".$this->getID()."\033[0m\n";
    echo "  Desc.      :  ".$this->getAttribute('desc')."\n";
    echo "  Latitude   :  ".sprintf("%.4f", $this->getAttribute('latDeg'))."\n";
    echo "  Longitude  :  ".sprintf("%.4f", $this->getAttribute('longDeg'))."\n";
    echo "  Type       :  ".$this->getType()->value."\n";
    echo "\033[33m--------------------\033[0m\n";
  }
}