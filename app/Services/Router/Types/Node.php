<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use RuntimeException;
use Carbon\Carbon;

class Node {

  // Local globals
  private string $ID;
  private NodeType $type;

  private string $desc;

  private float $latDeg;
  private float $longDeg;
  private float $latRad;
  private float $longRad;
  private ?Carbon $arrivedAt = null;
  private ?Carbon $departedAt = null;
  private bool $entryNode = false;
  private bool $exitNode = false;

  /**
   * @param  string  $ID  ID of the Node
   * @param  string  $description  Description/display name of the Node
   * @param  NodeType  $type  Type of the Node
   * @param  float  $latDeg  Latitude of the Node in degrees
   * @param  float  $longDeg  Longitude of the Node in degrees
   * @param  bool  $isEntryNode  True if the Node is an entry node, false otherwise
   * @param  bool  $isExitNode  True if the Node is an exit node, false otherwise
   * @throws InvalidRouterArgumentException If the node ID is empty
   * @throws InvalidCoordinateException If the node ID is empty
   */
  public function __construct(
    string $ID,
    string $description,
    NodeType $type,
    float $latDeg,
    float $longDeg,
    bool $isEntryNode = false,
    bool $isExitNode = false
  ) {
    if (empty($ID)) {
      throw new InvalidRouterArgumentException("Node ID cannot be empty.");
    }
    if (empty($description)) {
      throw new InvalidRouterArgumentException("Description cannot be empty.");
    }
    if ($latDeg < -90.0 || $latDeg > 90.0) {
      throw new InvalidCoordinateException("Node::__construct", "latitude", $latDeg);
    }
    if ($longDeg < -180.0 || $longDeg > 180.0) {
      throw new InvalidCoordinateException("Node::__construct", "latitude", $longDeg);
    }

    $this->ID = $ID;
    $this->desc = $description;
    $this->type = $type;
    $this->latDeg = $latDeg;
    $this->longDeg = $longDeg;
    $this->latRad = deg2rad($latDeg);
    $this->longRad = deg2rad($longDeg);
    $this->entryNode = $isEntryNode;
    $this->exitNode = $isExitNode;
  }

  /**
   * @return string ID of the Node
   */
  public function getID(): string {
    return $this->ID;
  }

  /**
   * @return string Description/display name of the Node
   */
  public function getDescription(): string {
    return $this->desc;
  }

  /**
   * @return Carbon|null Arrival time at the Node, null if not yet arrived
   */
  public function getArrivedAt(): ?Carbon {
    return $this->arrivedAt;
  }

  /**
   * @return Carbon|null Departure time from the Node, null if not yet departed
   */
  public function getDepartedAt(): ?Carbon {
    return $this->departedAt;
  }

  /**
   * @param  CoordType  $type  Format of coordinates. Either DEGREE or RADIAN
   * @return float Latitude of the Node
   */
  public function getLat(CoordType $type): float {
    if ($type === CoordType::DEGREE) {
      return $this->latDeg;
    } else {
      return $this->latRad;
    }
  }

  /**
   * @param  CoordType  $type  Format of coordinates. Either DEGREE or RADIAN
   * @return float Latitude of the Node
   */
  public function getLong(CoordType $type): float {
    if ($type === CoordType::DEGREE) {
      return $this->longDeg;
    } else {
      return $this->longRad;
    }
  }

  /**
   * @return bool True if the Node is an entry node, false otherwise
   */
  public function isEntryNode(): bool {
    return $this->entryNode;
  }

  /**
   * @return bool True if the Node is an exit node, false otherwise
   */
  public function isExitNode(): bool {
    return $this->exitNode;
  }


  /**
   * Calculates the distance to another node using spherical cosines
   *
   * @param  Node  $node  Node to calculate the distance to
   * @return float Distance in kilometers to the given Node
   */
  public function getDistanceTo(Node $node): float {
    $nodeLatRad = $node->getLat(CoordType::RADIAN);
    $nodeLongRad = $node->getLong(CoordType::RADIAN);

    return GeoMath::sphericalCosinesDistance(
      $this->getLat(CoordType::RADIAN),
      $this->getLong(CoordType::RADIAN),
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
   */
  public function printNode(): void {
    echo "\033[32mNode: ".$this->getID()."\033[0m\n";
    echo "  Desc.      :  ".$this->getDescription()."\n";
    echo "  Latitude   :  ".sprintf("%.4f", $this->getLat(CoordType::DEGREE))."\n";
    echo "  Longitude  :  ".sprintf("%.4f", $this->getLong(CoordType::DEGREE))."\n";
    echo "  Type       :  ".$this->getType()->value."\n";
    echo "  Entry Node :  ".($this->isEntryNode() ? 'true' : 'false')."\n";
    echo "  Exit Node  :  ".($this->isExitNode() ? 'true' : 'false')."\n";
    echo "\033[33m--------------------\033[0m\n";
  }

  /**
   * Prints the address node details to the console
   *
   * @return void
   * @throws RuntimeException If required attributes (desc, latDeg, longDeg) are missing
   */
  public function printAddressNode(): void {
    echo "\033[32mImaginary node: ".$this->getID()."\033[0m\n";
    echo "  Desc.      :  ".$this->getDescription()."\n";
    echo "  Latitude   :  ".sprintf("%.4f", $this->getLat(CoordType::DEGREE))."\n";
    echo "  Longitude  :  ".sprintf("%.4f", $this->getLong(CoordType::DEGREE))."\n";
    echo "  Type       :  ".$this->getType()->value."\n";
    echo "\033[33m--------------------\033[0m\n";
  }
}