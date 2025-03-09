<?php

namespace App\Services\Router\Types\Factories;

use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class NodeFactory {

  /**
   * Generates a new Node for RouterGraph compatible with A* algorithm.
   *
   * @param  string  $name
   * @param  float  $lat  Latitude of the Node in degrees
   * @param  float  $long  Longitude of the Node in degrees
   * @param  NodeType  $nodeType  Type of the Node
   * @param  bool  $isEntryNode  Is this Node a valid entry point for the route
   * @param  bool  $isExitNode  Is this Node a valid exit point for the route
   * @return Node
   */
  public static function createNode(
    string $name,
    float $lat,
    float $long,
    NodeType $nodeType,
    bool $isEntryNode,
    bool $isExitNode
  ): Node {
    $attributes = [
      'name' => $name,
      'latDeg' => $lat,
      'longDeg' => $long,
      'latRad' => deg2rad($lat),
      'longRad' => deg2rad($long),
      'isEntryNode' => $isEntryNode,
      'isExitNode' => $isExitNode
    ];

    return new Node(Uuid::uuid4()->toString(), $nodeType, $attributes);
  }
}