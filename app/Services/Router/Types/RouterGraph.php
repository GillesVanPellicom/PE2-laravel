<?php

namespace App\Services\Router\Types;

use App\Services\Router\GeoMath;
use App\Services\Router\Types\Exceptions\EdgeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\InvalidBooleanStringException;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\InvalidNodeIDException;
use App\Services\Router\Types\Exceptions\NodeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\SelfLoopException;

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
   * @param  string  $description  Display name of the node.
   * @param  float  $lat  Latitude of the node in degrees.
   * @param  float  $long  Longitude of the node in degrees.
   * @param  NodeType  $nodeType  Type of the node.
   * @param  string  $isEntryNode  Is this node a valid entry point for the route.
   * @param  string  $isExitNode  Is this node a valid exit point for the route.
   * @return string|null
   * @throws InvalidNodeIDException
   * @throws NodeAlreadyExistsException
   * @throws InvalidCoordinateException
   * @throws InvalidBooleanStringException
   * @throws InvalidRouterArgumentException
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
      throw new Exceptions\InvalidNodeIDException();
    }

    // Check if the node already exists
    if (isset($this->nodes[$ID])) {
      throw new NodeAlreadyExistsException($ID);
    }

    // Check if the latitude is within valid range
    if ($lat < -90.0 || $lat > 90.0) {
      throw new InvalidCoordinateException($ID, 'latitude', $lat);
    }

    // Check if the longitude is within valid range
    if ($long < -180.0 || $long > 180.0) {
      throw new InvalidCoordinateException($ID, 'longitude', $long);
    }

    // Check if isEntryNode and isExitNode are valid boolean strings
    if (!in_array($isEntryNode, ['true', 'false'], true)) {
      throw new InvalidBooleanStringException($ID, 'isEntryNode');
    }

    // Check if isEntryNode and isExitNode are valid boolean strings
    if (!in_array($isExitNode, ['true', 'false'], true)) {
      throw new InvalidBooleanStringException($ID, 'isExitNode');
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
   * @throws SelfLoopException
   * @throws NodeNotFoundException
   * @throws EdgeAlreadyExistsException
   */
  public function addEdge(string $startNodeID, string $endNodeID): void {
    // Check if the start node exists
    if (!isset($this->nodes[$startNodeID])) {
      throw new NodeNotFoundException($startNodeID);
    }

    // Check if the end node exists
    if (!isset($this->nodes[$endNodeID])) {
      throw new NodeNotFoundException($endNodeID);
    }

    // Check if the start and end nodes are the same
    if ($startNodeID === $endNodeID) {
      throw new SelfLoopException($startNodeID);
    }

    // Check if the edge already exists
    if (isset($this->edges[$startNodeID][$endNodeID]) || isset($this->edges[$endNodeID][$startNodeID])) {
      throw new EdgeAlreadyExistsException($startNodeID, $endNodeID);
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
    // Add both directions to the edge list to make the edge bidirectional
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
   * @return Node Node object
   * @throws NodeNotFoundException
   */
  public function getNode(string $NodeID): Node {
    // Check if the node exists
    if (!isset($this->nodes[$NodeID])) {
      throw new NodeNotFoundException($NodeID);
    }
    return $this->nodes[$NodeID];
  }

  /**
   * Get the neighbors of a node
   *
   * @param  string  $NodeID  ID of the node
   * @return array Array of neighbors
   * @throws NodeNotFoundException
   */
  public function getNeighbors(string $NodeID): array {
    // Check if the node exists
    if (!isset($this->nodes[$NodeID])) {
      throw new NodeNotFoundException($NodeID);
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

    echo "\033[1;34m=== Graph Structure ===\033[0m\n\n";

    // Nodes Section
    echo "\033[1;32mNodes:\033[0m\n";
    foreach ($this->nodes as $node) {
      $node->printNode();
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