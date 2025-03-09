<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use App\Services\Router\Types\Factories\NodeFactory;
use InvalidArgumentException;
use App\Services\Router\Types\Node;

class RouterGraph {
  private array $nodes;
  private array $edges;

  public function __construct() {
    $this->nodes = [];
    $this->edges = [];
  }


//  public function addNode(Node $node): bool {
//    $NodeUUID = $node->getUUID();
//    if (!isset($this->nodes[$NodeUUID])) {
//      $this->nodes[$NodeUUID] = $node;
//      $this->edges[$NodeUUID] = [];
//      return true;
//    }
//    return false;
//  }

  public function addNode(
    string $name,
    float $lat,
    float $long,
    NodeType $nodeType,
    string $isEntryNode,
    string $isExitNode
  ): ?string {
    $node = NodeFactory::createNode($name, $lat, $long, $nodeType, $isEntryNode, $isExitNode);
    $nodeUUID = $node->getUUID();
    if (!isset($this->nodes[$nodeUUID])) {
      $this->nodes[$nodeUUID] = $node;
      $this->edges[$nodeUUID] = [];
      return $nodeUUID;
    }
    return null;
  }


  public function addEdge(string $startNodeUUID, string $endNodeUUID): void {

    if (!isset($this->nodes[$startNodeUUID])) {
      throw new InvalidArgumentException("addEdge(): Start node does not exist in the graph.");
    }

    if (!isset($this->nodes[$endNodeUUID])) {
      throw new InvalidArgumentException("End node does not exist in the graph.");
    }

    if ($startNodeUUID === $endNodeUUID) {
      throw new InvalidArgumentException("Looping edges are not allowed. Start and end nodes are the same.");
    }

    $startNode = $this->nodes[$startNodeUUID];
    $endNode = $this->nodes[$endNodeUUID];

    $weight = GeoMath::haversine(
        $startNode->getAttribute('latRad'),
        $startNode->getAttribute('longRad'),
        $endNode->getAttribute('latRad'),
        $endNode->getAttribute('longRad')
      ) * 1.2;

    $this->edges[$startNodeUUID][$endNodeUUID] = $weight;
    $this->edges[$endNodeUUID][$startNodeUUID] = $weight;
  }

  public function getNodes(): array {
    return array_values($this->nodes);
  }

  public function getNode(string $NodeUUID): Node {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: ".$NodeUUID);
    }
    return $this->nodes[$NodeUUID];
  }

  public function getNeighbors(string $NodeUUID): array {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: ".$NodeUUID);
    }
    return $this->edges[$NodeUUID];
  }

  public function getEdges(): array {
    return $this->edges;
  }

  public function printGraph(): void {
    foreach ($this->nodes as $node) {
      echo "Node UUID     : ".$node->getUUID()."\n";
      echo "Latitude      : ".$node->getAttribute('latDeg')."\n";
      echo "Longitude     : ".$node->getAttribute('longDeg')."\n";
      echo "Type          : ".$node->getType()->value."\n";
      echo "Is Entry Node : ".$node->getAttribute('isEntryNode')."\n";
      echo "Is Exit Node  : ".$node->getAttribute('isExitNode')."\n";
      echo "-------------------------\n";
    }

    echo "Edges:\n";
    $printedEdges = [];
    foreach ($this->edges as $startNodeUUID => $neighbors) {
      foreach ($neighbors as $endNodeUUID => $weight) {
        // Check if the edge has already been printed
        if (!isset($printedEdges[$endNodeUUID][$startNodeUUID])) {
          echo $startNodeUUID." - ".$endNodeUUID." (weight: ".$weight.")\n";
          $printedEdges[$startNodeUUID][$endNodeUUID] = true;
        }
      }
    }
    echo "\n\n";
  }
}