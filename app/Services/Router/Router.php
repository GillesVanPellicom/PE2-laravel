<?php

namespace App\Services\Router;

use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\Node;
use SplPriorityQueue;
use InvalidArgumentException;

class Router {

  private RouterGraph $graph;
  private bool $debug = false;


  public function __construct() {
    $this->graph = new RouterGraph();
    $this->deserialize('app/Services/Router/tmp.graphml');
  }


  public function printRoute(string $nodeId_1, string $nodeId_2): void {
    $path = $this->aStar($nodeId_1, $nodeId_2);
    if ($this->debug) {
      echo "\033[1;32mShortest path:\033[0m ".implode(" > ", array_map(fn($node) => $node->getID(), $path))."\n";
    } else {
      echo implode(" > ", array_map(fn($node) => $node->getID(), $path))."\n";
    }
  }

  /**
   * Enable or disable debug mode.
   *
   * @param  bool  $enable  Enable or disable debug mode
   * @return void
   */
  public function setDebug(bool $enable): void {
    $this->debug = $enable;
  }

  /**
   * Deserialize GraphML into RouterGraph object.
   *
   * @param  string  $graphmlFilePath  Path to the GraphML file
   * @return void
   */
  private function deserialize(string $graphmlFilePath): void {
    // Check if the file exists
    if (!file_exists($graphmlFilePath)) {
      throw new InvalidArgumentException("File not found: $graphmlFilePath");
    }

    // Load the GraphML file
    $graphml = simplexml_load_file($graphmlFilePath);
    if ($graphml === false) {
      throw new InvalidArgumentException("Failed to load GraphML file: $graphmlFilePath");
    }

    // Iterate over each node in the GraphML file
    foreach ($graphml->graph->node as $node) {
      $ID = (string) $node['id'];
      $attributes = [];
      // Collect node attributes
      foreach ($node->data as $data) {
        $key = (string) $data['key'];
        $value = (string) $data;
        $attributes[$key] = $value;
      }
      $type = NodeType::from($attributes['type']);
      // Add the node to the graph
      $this->graph->addNode(
        $ID,
        $attributes['latDeg'],
        $attributes['longDeg'],
        $type,
        $attributes['isEntryNode'],
        $attributes['isExitNode']
      );
    }

    // Iterate over each edge in the GraphML file
    foreach ($graphml->graph->edge as $edge) {
      $source = (string) $edge['nodeID_1'];
      $target = (string) $edge['nodeID_2'];
      $weight = (float) $edge->data;
      // Add the edge to the graph
      $this->graph->addEdge($source, $target);
    }
  }

  /**
   * Finds the shortest path between two nodes.
   *
   * @param  string  $startNodeID  Start node ID
   * @param  string  $endNodeID  End node ID
   * @return array Path in hops between Node objects
   */
  private function aStar(string $startNodeID, string $endNodeID): array {

    if ($this->debug) {
      $this->graph->printGraph();
    }

    $openSet = new SplPriorityQueue();
    $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    $startNode = $this->graph->getNode($startNodeID);
    $endNode = $this->graph->getNode($endNodeID);

    // Insert the start node into the open set with its priority
    $openSet->insert($startNodeID, -($startNode->getDistanceTo($endNode)));

    $cameFrom = [];
    $gScore = [];
    $fScore = [];

    // Initialize gScore and fScore for each node
    foreach ($this->graph->getNodes() as $node) {
      $gScore[$node->getID()] = PHP_INT_MAX;
      $fScore[$node->getID()] = PHP_INT_MAX;
    }

    $gScore[$startNodeID] = 0;
    $fScore[$startNodeID] = $startNode->getDistanceTo($endNode);

    $epsilon = 1e-10; // Tolerance to avoid (very hard to debug) FP rounding errors.

    if ($this->debug) {
      echo "\033[1;34m=== Starting A* Search ===\033[0m\n";
      echo "From: $startNodeID | To: $endNodeID\n\n";
    }

    // Main loop of the A* algorithm
    while (!$openSet->isEmpty()) {
      if ($this->debug) {
        echo "\n\033[1;36m--- Iteration ---\033[0m\n";
        echo "Open Set Size: ".$openSet->count()."\n";
      }

      $extracted = $openSet->extract();
      $currentID = $extracted['data'];
      $current = $this->graph->getNode($currentID);
      $insertion_fScore = -$extracted['priority'];
      $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

      if ($this->debug) {
        echo "\033[32mProcessing Node: $currentID\033[0m\n";
        echo "  fScore: ".sprintf("%.6f", $insertion_fScore)." | gScore (queue): ".sprintf("%.6f",
            $insertion_gScore)."\n";
      }

      // Check if the current node's gScore is valid
      if ($gScore[$currentID] - $insertion_gScore <= $epsilon) {
        if ($this->debug) {
          echo "  \033[32mAction: Processing\033[0m | Current gScore: ".sprintf("%.6f", $gScore[$currentID])."\n";
        }
      } else {
        if ($this->debug) {
          echo "  \033[31mAction: Skipped\033[0m | Current gScore: ".sprintf("%.6f",
              $gScore[$currentID])." > Queue gScore: ".sprintf("%.6f", $insertion_gScore)."\n";
          echo "\033[1;36m-----------------\033[0m\n\n";
        }
        continue;
      }

      // Check if the current node is the goal
      if ($currentID === $endNodeID) {
        if ($this->debug) {
          echo "\033[1;32mGoal Reached: $currentID\033[0m\n\n>>> ROUTER DEBUG END\n\n";
        }
        return $this->reconstructPath($this->graph, $cameFrom, $currentID);
      }

      if ($this->debug) {
        echo "\n\033[1;33mNeighbors:\033[0m\n";
      }

      // Iterate over each neighbor of the current node
      foreach ($this->graph->getNeighbors($currentID) as $neighborID => $weight) {
        if ($this->debug) {
          echo "  \033[36m- Neighbor: $neighborID\033[0m | Edge Weight: ".sprintf("%.6f", $weight)."\n";
        }

        $tentativeGScore = $gScore[$currentID] + $weight;

        // Check if this path to the neighbor is better
        if ($tentativeGScore < $gScore[$neighborID]) {
          $cameFrom[$neighborID] = $currentID;
          $gScore[$neighborID] = $tentativeGScore;
          $fScore[$neighborID] = $gScore[$neighborID] + $this->graph->getNode($neighborID)->getDistanceTo($endNode);

          // Insert the neighbor into the open set with its priority
          $openSet->insert($neighborID, -$fScore[$neighborID]);

          if ($this->debug) {
            echo "    \033[33mUpdated:\033[0m New gScore: ".sprintf("%.6f",
                $tentativeGScore)." | New fScore: ".sprintf("%.6f", $fScore[$neighborID])."\n\n";
            echo "    \033[1;35mHeuristic Info:\033[0m\n";
            echo "      Current: $currentID | Neighbor: $neighborID\n";
            echo "      Σ Path Weight: ".sprintf("%.6f", $tentativeGScore)." | Heuristic: ".sprintf("%.6f",
                $this->graph->getNode($neighborID)->getDistanceTo($endNode))."\n";

            // Check heuristic admissibility
            $isAdmissible = $tentativeGScore <= $fScore[$neighborID];
            $admissibilitySymbol = $isAdmissible ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
            echo "      Heuristic Admissible: $admissibilitySymbol\n";

            echo "    \033[35m------------\033[0m\n";
          }
        } else {
          if ($this->debug) {
            echo "    \033[37mNot Updated:\033[0m Tentative gScore: ".sprintf("%.6f",
                $tentativeGScore)." ≥ Current gScore: ".sprintf("%.6f", $gScore[$neighborID])."\n\n";
          }
        }
      }
    }
    throw new InvalidArgumentException("No path found. The graph may be disconnected. Check if all edges are defined correctly.");
  }

  /**
   * Helper function for A*.
   * Reconstructs the path from the cameFrom array.
   *
   * @param  RouterGraph  $graph  RouterGraph object
   * @param  array  $cameFrom  Array of visited nodes
   * @param  string  $currentID  Current node ID
   * @return array
   */
  private function reconstructPath(RouterGraph $graph, array $cameFrom, string $currentID): array {
    $totalPath = [$graph->getNode($currentID)];
    // Reconstruct the path from the cameFrom array
    while (isset($cameFrom[$currentID])) {
      $currentID = $cameFrom[$currentID];
      array_unshift($totalPath, $graph->getNode($currentID));
    }
    return $totalPath;
  }
}