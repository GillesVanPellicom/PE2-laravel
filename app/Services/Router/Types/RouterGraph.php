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


  /**
   * Adds a node to the graph.
   *
   * @param  string  $UUID  The UUID of the node.
   * @param  float  $lat  Latitude of the node in degrees.
   * @param  float  $long  Longitude of the node in degrees.
   * @param  NodeType  $nodeType  Type of the node.
   * @param  string  $isEntryNode  Is this node a valid entry point for the route.
   * @param  string  $isExitNode  Is this node a valid exit point for the route.
   * @return string|null
   */
  public function addNode(
    string $UUID,
    float $lat,
    float $long,
    NodeType $nodeType,
    string $isEntryNode,
    string $isExitNode
  ): ?string {
    $node = NodeFactory::createNode($UUID, $lat, $long, $nodeType, $isEntryNode, $isExitNode);
    $nodeUUID = $node->getUUID();
    if (!isset($this->nodes[$nodeUUID])) {
      $this->nodes[$nodeUUID] = $node;
      $this->edges[$nodeUUID] = [];
      return $nodeUUID;
    }
    return null;
  }


  /**
   * Adds an unidirectional edge between two nodes
   *
   * @param  string  $startNodeUUID
   * @param  string  $endNodeUUID
   * @return void
   */
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


  /**
   * Get all nodes in the graph
   *
   * @return array Array of Node objects
   */
  public function getNodes(): array {
    return array_values($this->nodes);
  }


  /**
   * Get a node by UUID
   *
   * @param  string  $NodeUUID  UUID of the node
   * @return \App\Services\Router\Types\Node Node object
   */
  public function getNode(string $NodeUUID): Node {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: ".$NodeUUID);
    }
    return $this->nodes[$NodeUUID];
  }


  /**
   * Get the neighbors of a node
   *
   * @param  string  $NodeUUID  UUID of the node
   * @return array Array of neighbors
   */
  public function getNeighbors(string $NodeUUID): array {
    if (!isset($this->nodes[$NodeUUID])) {
      throw new InvalidArgumentException("Node does not exist: ".$NodeUUID);
    }
    return $this->edges[$NodeUUID];
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