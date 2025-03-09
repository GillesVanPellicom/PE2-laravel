<?php

namespace App\Services\Router;

use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\Node;
use SplPriorityQueue;
use InvalidArgumentException;

class Router {

  private $graph;

  /**
   * @param $graph
   */

  /**
   * Constructs a new Router instance.
   */
  public function __construct() {
    $this->graph = new RouterGraph();
    $this->deserialize('app/Services/Router/tmp.graphml');
    }


  /**
   * Deserializes a GraphML file into the router.
   *
   * @param  string  $graphmlFilePath  The path to the GraphML file.
   * @throws InvalidArgumentException If the file is not found or cannot be loaded.
   */
  private function deserialize(string $graphmlFilePath): void {
    if (!file_exists($graphmlFilePath)) {
      throw new InvalidArgumentException("File not found: $graphmlFilePath");
    }

    $graphml = simplexml_load_file($graphmlFilePath);
    if ($graphml === false) {
      throw new InvalidArgumentException("Failed to load GraphML file: $graphmlFilePath");
    }

    // Parse nodes
    // For all nodes
    foreach ($graphml->graph->node as $node) {
      $UUID = (string) $node['id'];
      $attributes = [];
      // For all data in the node
      foreach ($node->data as $data) {
        $key = (string) $data['key'];
        $value = (string) $data;
        $attributes[$key] = $value;
      }
      $type = NodeType::from($attributes['type']);
      $this->graph->addNode(
        $UUID,
        $attributes['latDeg'],
        $attributes['longDeg'],
        $type,
        $attributes['isEntryNode'],
        $attributes['isExitNode']
      );
    }

    // Parse edges
    foreach ($graphml->graph->edge as $edge) {
      $source = (string) $edge['nodeID_1'];
      $target = (string) $edge['nodeID_2'];
      $weight = (float) $edge->data;
      $this->graph->addEdge($source, $target);
    }
  }


  /**
   * @return void
   */
  public function test(): void {
    $this->graph->printGraph();
    $path = $this->aStar('DC_WARSAW', 'DC_LOS_ANGELES');

    echo "Shortest path: ";
    echo implode(" > ", array_map(fn($node) => $node->getUUID(), $path));
  }


  /**
   * Implements the A* algorithm to find the shortest path between two nodes in a graph.
   *
   * @param  string  $startNodeUUID  The UUID of the start node.
   * @param  string  $endNodeUUID  The UUID of the end node.
   * @return array The shortest path from start to end node as an array of nodes.
   * @throws InvalidArgumentException If no path is found.
   */
  private function aStar(
    string $startNodeUUID,
    string $endNodeUUID
  ): array {
    // Priority queue to hold nodes to be evaluated, with their fScore as priority
    $openSet = new SplPriorityQueue();

    $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    // Retrieve start and end nodes from the graph
    $startNode = $this->graph->getNode($startNodeUUID);
    $endNode = $this->graph->getNode($endNodeUUID);

    // Insert the start node into the open set with its heuristic cost (h(n))
    $openSet->insert($startNodeUUID, -($startNode->getDistanceTo($endNode)));

    // Maps to keep track of the path and scores
    $cameFrom = []; // For reconstructing the path
    $gScore = []; // Cost from start node to this node
    $fScore = []; // Estimated cost from start node to end node through this node

    // Initialize gScore and fScore for all nodes
    foreach ($this->graph->getNodes() as $node) {
      $gScore[$node->getUUID()] = PHP_INT_MAX; // ∞
      $fScore[$node->getUUID()] = PHP_INT_MAX; // ∞
    }

    // gScore of start node is 0
    $gScore[$startNodeUUID] = 0;
    // fScore of start node is the heuristic cost to the end node
    $fScore[$startNodeUUID] = $startNode->getDistanceTo($endNode);

    // Main loop to process nodes in the open set
    while (!$openSet->isEmpty()) {
      // Extract the node with the lowest fScore
      $extracted = $openSet->extract();
      $currentUUID = $extracted['data'];
      $current = $this->graph->getNode($currentUUID);
      $insertion_fScore = -$extracted['priority'];
      $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

      // Skip if this entry is outdated
      if ($gScore[$currentUUID] < $insertion_gScore) {
        continue;
      }

      // If the current node is the end node, reconstruct and return the path
      if ($currentUUID === $endNodeUUID) {
        return $this->reconstructPath($this->graph, $cameFrom, $currentUUID);
      }

      // Iterate through neighbors of the current node
      foreach ($this->graph->getNeighbors($currentUUID) as $neighborUUID => $weight) {
        // Calculate tentative gScore for the neighbor
        $tentativeGScore = $gScore[$currentUUID] + $weight;

        // If this path to the neighbor is better, update the scores and path
        if ($tentativeGScore < $gScore[$neighborUUID]) {
          $cameFrom[$neighborUUID] = $currentUUID;
          $gScore[$neighborUUID] = $tentativeGScore;
          $fScore[$neighborUUID] = $gScore[$neighborUUID] + $this->graph->getNode($neighborUUID)->getDistanceTo($endNode);

          // Add neighbor to open set with new fScore
          $openSet->insert($neighborUUID, -$fScore[$neighborUUID]);
        }
      }
    }

    // If the open set is empty and no path was found, throw an exception
    throw new InvalidArgumentException("No path found. The graph may be disconnected. Check if all edges are defined correctly.");
  }


  /**
   * Reconstructs the path from start to end node using the cameFrom map.
   *
   * @param  RouterGraph  $graph  The graph containing the nodes.
   * @param  array  $cameFrom  Map of nodes and their predecessors.
   * @param  string  $currentUUID  The UUID of the current node.
   * @return array The reconstructed path as an array of nodes.
   */
  private function reconstructPath(
    RouterGraph $graph,
    array $cameFrom,
    string $currentUUID
  ): array {
    $totalPath = [$graph->getNode($currentUUID)];
    // Traverse the cameFrom map to build the path
    while (isset($cameFrom[$currentUUID])) {
      $currentUUID = $cameFrom[$currentUUID];
      array_unshift($totalPath, $graph->getNode($currentUUID));
    }
    return $totalPath;
  }
}