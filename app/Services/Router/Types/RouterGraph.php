<?php

namespace App\Services\Router\Types;

use App\Models\Address;
use App\Services\Router\GeoMath;
use App\Services\Router\Types\Exceptions\EdgeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\EdgeNotFoundException;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\InvalidNodeIDException;
use App\Services\Router\Types\Exceptions\NodeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\SelfLoopException;
use Carbon\Carbon;

class RouterGraph {
  private array $nodes;
  private array $edges;

  public function __construct() {
    $this->nodes = [];
    $this->edges = [];
  }

  /**
   * @throws InvalidRouterArgumentException
   * @throws InvalidCoordinateException
   */
  public static function newNode(
    string $ID,
    string $description,
    NodeType $type,
    float $latDeg,
    float $lonDeg,
    int $addressId,
    bool $isEntryNode = false,
    bool $isExitNode = false,
    ?Carbon $arrivedAt = null,
    ?Carbon $departedAt = null,
    ?Carbon $checkedInAt = null,
    ?Carbon $checkedOutAt = null
  ): Node {
    $node = new Node(
      $ID,
      $description,
      $type,
      $latDeg,
      $lonDeg,
      $isEntryNode,
      $isExitNode,
      $addressId
    );

    $node->setArrivedAt($arrivedAt ? new Carbon($arrivedAt) : null);
    $node->setDepartedAt($departedAt ? new Carbon($departedAt) : null);
    $node->setCheckedInAt($checkedInAt ? new Carbon($checkedInAt) : null);
    $node->setCheckedOutAt($checkedOutAt ? new Carbon($checkedOutAt) : null);

    return $node;
  }

  /**
   * Adds a node to the graph.
   *
   * @param  string  $ID  The ID of the node.
   * @param  string  $description  Display name of the node.
   * @param  float  $latDeg  Latitude of the node in degrees.
   * @param  float  $longDeg  Longitude of the node in degrees.
   * @param  NodeType  $nodeType  Type of the node.
   * @param  bool  $isEntryNode  Is this node a valid entry point for the route.
   * @param  bool  $isExitNode  Is this node a valid exit point for the route.
   * @return string|null
   * @throws InvalidNodeIDException
   * @throws NodeAlreadyExistsException
   * @throws InvalidCoordinateException
   * @throws InvalidRouterArgumentException
   */
  public function addNode(
    string $ID,
    string $description,
    float $latDeg,
    float $longDeg,
    NodeType $nodeType,
    bool $isEntryNode,
    bool $isExitNode
  ): ?string {

    // Check if the node ID is empty
    if (empty($ID)) {
      throw new InvalidNodeIDException();
    }

    // Check if the node already exists
    if (isset($this->nodes[$ID])) {
      throw new NodeAlreadyExistsException($ID);
    }

    // Check if the latitude is within valid range
    if ($latDeg < -90.0 || $latDeg > 90.0) {
      throw new InvalidCoordinateException($ID, 'latitude', $latDeg);
    }

    // Check if the longitude is within valid range
    if ($longDeg < -180.0 || $longDeg > 180.0) {
      throw new InvalidCoordinateException($ID, 'longitude', $longDeg);
    }

    // Create a new node
    // Address id is set to 1 sicne the router doesn't need the metadata
    $node = new Node($ID, $description, $nodeType, $latDeg, $longDeg, 1, $isEntryNode, $isExitNode);

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
   * Removes an edge between two nodes.
   *
   * @param  string  $startNodeID  The ID of the start node.
   * @param  string  $endNodeID  The ID of the end node.
   * @return void
   * @throws NodeNotFoundException If either node does not exist.
   * @throws EdgeNotFoundException If the edge does not exist.
   */
  public function removeEdge(string $startNodeID, string $endNodeID): void {
    // Check if the start node exists
    if (!isset($this->nodes[$startNodeID])) {
      throw new NodeNotFoundException($startNodeID);
    }

    // Check if the end node exists
    if (!isset($this->nodes[$endNodeID])) {
      throw new NodeNotFoundException($endNodeID);
    }

    // Check if the edge exists
    if (!isset($this->edges[$startNodeID][$endNodeID]) && !isset($this->edges[$endNodeID][$startNodeID])) {
      throw new EdgeNotFoundException($startNodeID, $endNodeID);
    }

    // Remove the edge from the graph
    unset($this->edges[$startNodeID][$endNodeID]);
    unset($this->edges[$endNodeID][$startNodeID]);
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
      $startNode->getLat(CoordType::RADIAN),
      $startNode->getLong(CoordType::RADIAN),
      $endNode->getLat(CoordType::RADIAN),
      $endNode->getLong(CoordType::RADIAN)
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

    // Nodes Section
    echo "\033[1;34m=== Nodes ===\033[0m\n\n";
    foreach ($this->nodes as $node) {
      $node->printNode();
    }

    // Edges Section
    print "\033[1;34m=== Edges ===\033[0m\n\n";
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

    echo "\n\n";
  }
}