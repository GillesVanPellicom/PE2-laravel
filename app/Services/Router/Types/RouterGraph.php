<?php

namespace App\Services\Router\Types;

use App\Helpers\ConsoleHelper;
use App\Services\Router\GeoMath;
use App\Services\Router\Types\Node;
use InvalidArgumentException;

class RouterGraph {
  private array $nodes;
  private array $edges;

  public function __construct() {
    $this->nodes = [];
    $this->edges = [];
  }


  /**
   * Adds a node to the graph.
   *
   * @param  string  $ID  The ID of the node.
   * @param  string  $description Display name of the node.
   * @param  float  $lat  Latitude of the node in degrees.
   * @param  float  $long  Longitude of the node in degrees.
   * @param  NodeType  $nodeType  Type of the node.
   * @param  string  $isEntryNode  Is this node a valid entry point for the route.
   * @param  string  $isExitNode  Is this node a valid exit point for the route.
   * @return string|null
   */
  public function addNode(
    string $ID,
    string $description,
    float $lat,
    float $long,
    NodeType $nodeType,
    string $isEntryNode,
    string $isExitNode
  ): ?string {

    // Check if the node ID is empty
    if (empty($ID)) {
      ConsoleHelper::error("RouterGraph::addNode() - Node ID cannot be empty.");
      throw new InvalidArgumentException();
    }

    // Check if the node already exists
    if (isset($this->nodes[$ID])) {
      ConsoleHelper::error("RouterGraph::addNode() - Node (ID: {$ID}) already exists. IDs must be unique.");
      throw new InvalidArgumentException();
    }

    // Check if the latitude is within valid range
    if ($lat < -90.0 || $lat > 90.0) {
      ConsoleHelper::error("RouterGraph::addNode() - Node (ID: {$ID}) latitude must be between -90 and 90 degrees.");
      throw new InvalidArgumentException();
    }

    // Check if the longitude is within valid range
    if ($long < -180.0 || $long > 180.0) {
      ConsoleHelper::error("RouterGraph::addNode() - Node (ID: {$ID}) longitude must be between -180 and 180 degrees.");
      throw new InvalidArgumentException();
    }

    // Check if isEntryNode and isExitNode are valid boolean strings
    if (!in_array($isEntryNode, ['true', 'false'], true)) {
      ConsoleHelper::error("RouterGraph::addNode() - isEntryNode (ID: {$ID}) must be 'true' or 'false'.");
      throw new InvalidArgumentException();
    }

    // Check if isEntryNode and isExitNode are valid boolean strings
    if (!in_array($isExitNode, ['true', 'false'], true)) {
      ConsoleHelper::error("RouterGraph::addNode() - isExitNode (ID: {$ID}) must be 'true' or 'false'.");
      throw new InvalidArgumentException();
    }

    // Create the node attributes array
    $attributes = [
      'desc' => $description,
      'latDeg' => $lat,
      'longDeg' => $long,
      'latRad' => deg2rad($lat),
      'longRad' => deg2rad($long),
      'isEntryNode' => $isEntryNode,
      'isExitNode' => $isExitNode
    ];
    // Create a new node
    $node = new Node($ID, $nodeType, $attributes);

    // Add the node to the graph if it doesn't already exist
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
    // Check if the start node exists
    if (!isset($this->nodes[$startNodeID])) {
      ConsoleHelper::error("RouterGraph::addEdge() - Start node (ID: {$startNodeID}) does not exist.");
      throw new InvalidArgumentException();
    }

    // Check if the end node exists
    if (!isset($this->nodes[$endNodeID])) {
      ConsoleHelper::error("RouterGraph::addEdge() - End node  (ID: {$startNodeID}) does not exist.");
      throw new InvalidArgumentException();
    }

    // Check if the start and end nodes are the same
    if ($startNodeID === $endNodeID) {
      ConsoleHelper::error("RouterGraph::addEdge() - Start and end node (IDs: {$startNodeID}) are the same. Looping edges are not allowed.");
      throw new InvalidArgumentException();
    }

    // Get the start and end nodes
    $startNode = $this->nodes[$startNodeID];
    $endNode = $this->nodes[$endNodeID];

    // Calculate the weight of the edge using the spherical law of cosines
    $weight = GeoMath::sphericalCosinesDistance(
      $startNode->getAttribute('latRad'),
      $startNode->getAttribute('longRad'),
      $endNode->getAttribute('latRad'),
      $endNode->getAttribute('longRad')
    );

    // Add the edge to the graph
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
    // Check if the node exists
    if (!isset($this->nodes[$NodeID])) {
      ConsoleHelper::error("RouterGraph::getNode() - Node (ID: {$NodeID}) does not exist.");
      throw new InvalidArgumentException();
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
    // Check if the node exists
    if (!isset($this->nodes[$NodeID])) {
      ConsoleHelper::error("RouterGraph::getNeighbors() - Node (ID: {$NodeID}) does not exist.");
      throw new InvalidArgumentException();
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

    echo "\033[1;34m>>> ROUTER DEBUG BEGIN\n\n=== Graph Structure ===\033[0m\n\n";

    // Nodes Section
    echo "\033[1;32mNodes:\033[0m\n";
    foreach ($this->nodes as $node) {
      echo "\033[32mNode: ".$node->getID()."\033[0m\n";
      echo "  Desc.      :  ".$node->getAttribute('desc')."\n";
      echo "  Latitude   :  ".sprintf("%.4f", $node->getAttribute('latDeg'))."\n";
      echo "  Longitude  :  ".sprintf("%.4f", $node->getAttribute('longDeg'))."\n";
      echo "  Type       :  ".$node->getType()->value."\n";
      echo "  Entry Node :  ".$node->getAttribute('isEntryNode')."\n";
      echo "  Exit Node  :  ".$node->getAttribute('isExitNode')."\n";
      echo "\033[33m--------------------\033[0m\n";
    }

    // Edges Section
    echo "\n\033[1;36mEdges:\033[0m\n";
    $printedEdges = []; // Track printed edges to avoid duplicates
    foreach ($this->edges as $startNodeID => $neighbors) {
      foreach ($neighbors as $endNodeID => $weight) {
        $edgeKey = min($startNodeID, $endNodeID).'-'.max($startNodeID, $endNodeID);
        if (!isset($printedEdges[$edgeKey])) {
          printf("\033[36m%-16s ↔ %-16s\033[0m (%.4f km)\n", $startNodeID, $endNodeID, $weight);
          $printedEdges[$edgeKey] = true;
        }
      }
    }

    echo "\n\033[1;34m====================\033[0m\n\n";
  }
}