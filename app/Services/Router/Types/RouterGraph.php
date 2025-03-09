<?php

namespace App\Services\Router\Types;

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
    bool $isEntryNode,
    bool $isExitNode
  ): ?string {
    $node= NodeFactory::createNode($name, $lat, $long, $nodeType, $isEntryNode, $isExitNode);
    $nodeUUID = $node->getUUID();
    if (!isset($this->nodes[$nodeUUID])) {
      $this->nodes[$nodeUUID] = $node;
      $this->edges[$nodeUUID] = [];
      return $nodeUUID;
    }
    return null;
  }


  public function addEdge(string $startNodeUUID, string $endNodeUUID, int $weight): void {

    if (!isset($this->nodes[$startNodeUUID])) {
      throw new InvalidArgumentException("Start node does not exist in the graph.");
    }

    if (!isset($this->nodes[$endNodeUUID])) {
      throw new InvalidArgumentException("End node does not exist in the graph.");
    }

    if ($startNodeUUID === $endNodeUUID) {
      throw new InvalidArgumentException("Looping edges are not allowed. Start and end nodes are the same.");
    }

    if ($weight <= 0) {
      throw new InvalidArgumentException("Weight must be a positive integer. Actual: " . $weight);
    }

    $this->edges[$startNodeUUID][$endNodeUUID] = $weight;
    $this->edges[$endNodeUUID][$startNodeUUID] = $weight;
  }

  public function getNodes(): array {
    return array_values($this->nodes);
  }

  public function getNode(string $NodeUUID): Node {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: " . $NodeUUID);
    }
    return $this->nodes[$NodeUUID];
  }
  
  public function getNeighbors(string $NodeUUID): array {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: " . $NodeUUID);
    }
    return $this->edges[$NodeUUID];
  }

  public function getEdges(): array {
    return $this->edges;
  }
}