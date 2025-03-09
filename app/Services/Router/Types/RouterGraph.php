<?php

namespace App\Services\Router\Types;

use InvalidArgumentException;
use App\Services\Router\Types\Node;

class RouterGraph {
  private array $nodes;
  private array $edges;

  public function __construct() {
    $this->nodes = [];
    $this->edges = [];
  }


  public function addNode(Node $node): bool {
    $NodeUUID = $node->getName();
    if (!isset($this->nodes[$NodeUUID])) {
      $this->nodes[$NodeUUID] = $node;
      $this->edges[$NodeUUID] = [];
      return true;
    }
    return false;
  }


  public function addEdge(Node $startNode, Node $endNode, int $weight): void {
    $startNodeUUID = $startNode->getName();
    $endNodeUUID = $endNode->getName();

    if (!isset($this->nodes[$startNodeUUID])) {
      throw new InvalidArgumentException("Start node does not exist in the graph.");
    }

    if (!isset($this->nodes[$endNodeUUID])) {
      throw new InvalidArgumentException("End node does not exist in the graph.");
    }

    if ($startNode === $endNode) {
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