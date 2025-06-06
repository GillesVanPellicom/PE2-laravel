<?php

namespace App\Services\Router\Types;

use App\Models\Address;
use App\Models\Location;
use App\Models\PackageMovement;
use App\Models\RouterNodes;
use App\Services\Router\Helpers\GeoMath;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use Carbon\Carbon;
use RuntimeException;

class Node {

  // Router variables
  private string $ID;
  private NodeType $type;
  private string $desc;
  private float $latDeg;
  private float $longDeg;
  private float $latRad;
  private float $longRad;
  private bool $entryNode = false;
  private bool $exitNode = false;

  // Metadata
  private ?Carbon $arrivedAt = null;
  private ?Carbon $departedAt = null;
  private ?Carbon $checkedInAt = null;
  private ?Carbon $checkedOutAt = null;
  private Address $address;

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
    int $address_id,
    bool $isEntryNode = false,
    bool $isExitNode = false,
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

    $a = Address::find($address_id);
    if (!$a) {
      throw new InvalidRouterArgumentException("Address (ID: {$address_id}) not found.");
    }

    $this->ID = $ID;
    $this->desc = $description;
    $this->type = $type;
    $this->latDeg = $latDeg;
    $this->longDeg = $longDeg;
    $this->latRad = deg2rad($latDeg);
    $this->longRad = deg2rad($longDeg);
    $this->address = $a;
    $this->entryNode = $isEntryNode;
    $this->exitNode = $isExitNode;
  }

  /**
   * Creates a Node from a Location
   *
   * @throws InvalidRouterArgumentException
   * @throws InvalidCoordinateException
   */
  public static function fromLocation(Location $loc): Node {
    return new self($loc->id, $loc->description, $loc->location_type, $loc->latitude,
      $loc->longitude, $loc->addresses_id);
  }

  /**
   * Creates a Node from a RouterNode
   *
   * @throws InvalidRouterArgumentException
   * @throws InvalidCoordinateException
   */
  public static function fromRouterNode(RouterNodes $node): Node {
    return new self($node->id, $node->description, $node->location_type, $node->latDeg, $node->lonDeg,
      $node->address_id, $node->isEntry, $node->isExit);
  }

  /**
   * Get a Node object from a given ID.
   *
   * This method checks both RouterNodes and Location models to find the node.
   *
   * @param  string  $id  The ID of the node.
   * @return Node|null The Node object or null if not found.
   * @throws InvalidRouterArgumentException If the node ID is empty
   * @throws InvalidCoordinateException If the node ID is empty
   */
  public static function fromId(string|null $id): ?Node {
    $nodeData = RouterNodes::find($id) ?? Location::find($id);

    if (!$nodeData) {
      return null;
    }

    if ($nodeData instanceof RouterNodes) {
      return Node::fromRouterNode($nodeData);
    } else {
      return Node::fromLocation($nodeData);
    }
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
   * @param  CoordType  $type  Format of coordinates. Either DEGREE or RADIAN
   * @return float Latitude of the Node
   */
  public function getLat(CoordType $type = CoordType::DEGREE): float {
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
  public function getLong(CoordType $type = CoordType::DEGREE): float {
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
   * @return Carbon|null Arrival time at the Node, null if not yet arrived
   */
  public function getCheckedInAt(): ?Carbon {
    return $this->checkedInAt;
  }

  /**
   * @return Carbon|null Departure time from the Node, null if not yet departed
   */
  public function getCheckedOutAt(): ?Carbon {
    return $this->checkedOutAt;
  }

  // Setter for arrivedAt
  public function setArrivedAt(?Carbon $arrivedAt): void {
    $this->arrivedAt = $arrivedAt;
  }

// Setter for checkedInAt
  public function setCheckedInAt(?Carbon $checkedInAt): void {
    $this->checkedInAt = $checkedInAt;
  }

// Setter for checkedOutAt
  public function setCheckedOutAt(?Carbon $checkedOutAt): void {
    $this->checkedOutAt = $checkedOutAt;
  }

// Setter for departedAt
  public function setDepartedAt(?Carbon $departedAt): void {
    $this->departedAt = $departedAt;
  }

  public function getAddress(): Address {
    return $this->address;
  }

  /**
   * Initialize a Node object with movement timestamps.
   *
   * @param  PackageMovement  $movement  The movement containing timestamps.
   * @return Node The initialized Node object.
   */
  public function initializeTimes(PackageMovement $movement): Node {
    $this->setArrivedAt($movement->arrival_time ? new Carbon($movement->arrival_time) : null);
    $this->setCheckedInAt($movement->check_in_time ? new Carbon($movement->check_in_time) : null);
    $this->setCheckedOutAt($movement->check_out_time ? new Carbon($movement->check_out_time) : null);
    $this->setDepartedAt($movement->departure_time ? new Carbon($movement->departure_time) : null);
    return $this;
  }

  /**
   * Prints the node details to the console
   *
   * @return void
   * @throws RuntimeException If required attributes (desc, latDeg, longDeg, isEntryNode, isExitNode) are missing
   */
  public function printNode(): void {
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    ini_set('default_charset', 'utf-8');

    // Ensure description is clean UTF-8
    $desc = $this->getDescription();
    $desc = mb_convert_encoding($desc, 'UTF-8', mb_detect_encoding($desc, ['UTF-8', 'ISO-8859-1', 'Windows-1252']));
    $desc = preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $desc);

    $id = mb_substr($this->getID(), 0, 20, 'UTF-8');
    if (mb_strlen($this->getID(), 'UTF-8') < 20) {
      $id = str_pad($id, 20, ' ', STR_PAD_RIGHT);
    }

    $descLen = mb_strlen($desc, 'UTF-8');
    if ($descLen > 38) {
      $desc = mb_substr($desc, 0, 35, 'UTF-8').'...';
    } else {
      $paddingNeeded = 38 - $descLen;
      $desc = $desc.str_repeat(' ', $paddingNeeded);
    }

    $type = mb_substr($this->getType()->value, 0, 20, 'UTF-8');
    if (mb_strlen($this->getType()->value, 'UTF-8') > 20) {
      $type = mb_substr($type, 0, 17, 'UTF-8').'...';
    } else {
      $type = str_pad($type, 20, ' ', STR_PAD_RIGHT);
    }

    printf(
      "║ \033[38;2;255;140;0m%-29s\033[0m ║ %-38s ║ \033[1;35m%10.4f\033[0m ║ \033[1;35m%11.4f\033[0m ║ %-20s ║",
      $id,
      $desc,
      $this->getLat(),
      $this->getLong(),
      $type,

    );
        $ex = $this->isEntryNode() ? "\033[32mYes\033[0m  " : "\033[31mNo\033[0m   ";
        $en = $this->isExitNode() ? "\033[32mYes\033[0m  " : "\033[31mNo\033[0m   ";
        echo ' '.$ex.' ║ '.$en."║\n";
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
    echo "  Latitude   :  ".sprintf("%.4f", $this->getLat())."\n";
    echo "  Longitude  :  ".sprintf("%.4f", $this->getLong())."\n";
    echo "  Type       :  ".$this->getType()->value."\n";
    echo "\033[33m--------------------\033[0m\n";
  }
}