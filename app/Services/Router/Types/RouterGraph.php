<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use App\Services\Router\Types\Factories\NodeFactory;
use App\Services\Router\Types\Node;
use InvalidArgumentException;

class RouterGraph {
  private array $nodes;
  private array $edges;
  private bool $debug = false; // Debug enabled by default

  public function __construct() {
    $this->nodes = [];
    $this->edges = [];
  }

  // Method to enable/disable debug output
  public function setDebug(bool $enable): void {
    $this->debug = $enable;
  }

  /**
   * Adds a node to the graph.
   *
   * @param  string  $ID  The ID of the node.
   * @param  float  $lat  Latitude of the node in degrees.
   * @param  float  $long  Longitude of the node in degrees.
   * @param  NodeType  $nodeType  Type of the node.
   * @param  string  $isEntryNode  Is this node a valid entry point for the route.
   * @param  string  $isExitNode  Is this node a valid exit point for the route.
   * @return string|null
   */
  public function addNode(
    string $ID,
    float $lat,
    float $long,
    NodeType $nodeType,
    string $isEntryNode,
    string $isExitNode
  ): ?string {
    $attributes = [
      'latDeg' => $lat,
      'longDeg' => $long,
      'latRad' => deg2rad($lat),
      'longRad' => deg2rad($long),
      'isEntryNode' => $isEntryNode,
      'isExitNode' => $isExitNode
    ];
    $node = new Node($ID, $nodeType, $attributes);

    $nodeID = $node->getID();
    if (!isset($this->nodes[$nodeID])) {
      $this->nodes[$nodeID] = $node;
      $this->edges[$nodeID] = [];
      return $nodeID;
    }
    return null;
  }

  /**
   * Adds an unidirectional edge between two nodes
   *
   * @param  string  $startNodeID
   * @param  string  $endNodeID
   * @return void
   */
  public function addEdge(string $startNodeID, string $endNodeID): void {
    if (!isset($this->nodes[$startNodeID])) {
      throw new InvalidArgumentException("addEdge(): Start node does not exist in the graph.");
    }

    if (!isset($this->nodes[$endNodeID])) {
      throw new InvalidArgumentException("End node does not exist in the graph.");
    }

    if ($startNodeID === $endNodeID) {
      throw new InvalidArgumentException("Looping edges are not allowed. Start and end nodes are the same.");
    }

    $startNode = $this->nodes[$startNodeID];
    $endNode = $this->nodes[$endNodeID];

    $weight = GeoMath::sphericalCosinesDistance(
      $startNode->getAttribute('latRad'),
      $startNode->getAttribute('longRad'),
      $endNode->getAttribute('latRad'),
      $endNode->getAttribute('longRad')
    );

    $this->edges[$startNodeID][$endNodeID] = $weight;
    $this->edges[$endNodeID][$startNodeID] = $weight;
  }

  /**
   * Get all nodes in the graph
   *
   * @return array Array of Node objects
   */
  public function getNodes(): array {
    return array_values($this->nodes);
  }

  /**
   * Get a node by ID
   *
   * @param  string  $NodeID  ID of the node
   * @return \App\Services\Router\Types\Node Node object
   */
  public function getNode(string $NodeID): Node {
    if (!isset($this->nodes[$NodeID])) {
      throw new InvalidArgumentException("Node does not exist: " . $NodeID);
    }
    return $this->nodes[$NodeID];
  }

  /**
   * Get the neighbors of a node
   *
   * @param  string  $NodeID  ID of the node
   * @return array Array of neighbors
   */
  public function getNeighbors(string $NodeID): array {
    if (!isset($this->nodes[$NodeID])) {
      throw new InvalidArgumentException("Node does not exist: " . $NodeID);
    }
    return $this->edges[$NodeID];
  }

  /**
   * Get all edges of the graph
   *
   * @return array
   */
  public function getEdges(): array {
    return $this->edges;
  }

  /**
   * Print all nodes and edges to console
   * @return void
   */
  public function printGraph(): void {
    if (!$this->debug) {
      return; // Skip printing if debug is off
    }

    echo "\033[1;34m=== Graph Structure ===\033[0m\n\n";

    // Nodes Section
    echo "\033[1;32mNodes:\033[0m\n";
    foreach ($this->nodes as $node) {
      echo "\033[32mNode :" . $node->getID() . "\033[0m\n";
      echo "  Latitude   :  " . sprintf("%.4f", $node->getAttribute('latDeg')) . "\n";
      echo "  Longitude  :  " . sprintf("%.4f", $node->getAttribute('longDeg')) . "\n";
      echo "  Type       :  " . $node->getType()->value . "\n";
      echo "  Entry Node :  " . $node->getAttribute('isEntryNode') . "\n";
      echo "  Exit Node  :  " . $node->getAttribute('isExitNode') . "\n";
      echo "\033[33m--------------------\033[0m\n";
    }

    // Edges Section
    echo "\n\033[1;36mEdges:\033[0m\n";
    $printedEdges = []; // Track printed edges to avoid duplicates
    foreach ($this->edges as $startNodeID => $neighbors) {
      foreach ($neighbors as $endNodeID => $weight) {
        $edgeKey = min($startNodeID, $endNodeID) . '-' . max($startNodeID, $endNodeID);
        if (!isset($printedEdges[$edgeKey])) {
          echo "\033[36m" . $startNodeID . " ↔ " . $endNodeID . "\033[0m (Weight: " . sprintf("%.4f", $weight) . " km)\n";
          $printedEdges[$edgeKey] = true;
        }
      }
    }

    echo "\n\033[1;34m====================\033[0m\n\n";
  }
}