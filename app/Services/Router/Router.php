<?php

namespace App\Services\Router;

use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\Node;
use SplPriorityQueue;
use InvalidArgumentException;

class Router {

  private $graph;
  private bool $debug = false;

  public function __construct() {
    $this->graph = new RouterGraph();
    $this->deserialize('app/Services/Router/tmp.graphml');
  }

  // Method to enable/disable debug output
  public function setDebug(bool $enable): void {
    $this->debug = $enable;
    $this->graph->setDebug($enable);
  }

  private function deserialize(string $graphmlFilePath): void {
    if (!file_exists($graphmlFilePath)) {
      throw new InvalidArgumentException("File not found: $graphmlFilePath");
    }

    $graphml = simplexml_load_file($graphmlFilePath);
    if ($graphml === false) {
      throw new InvalidArgumentException("Failed to load GraphML file: $graphmlFilePath");
    }

    foreach ($graphml->graph->node as $node) {
      $ID = (string) $node['id'];
      $attributes = [];
      foreach ($node->data as $data) {
        $key = (string) $data['key'];
        $value = (string) $data;
        $attributes[$key] = $value;
      }
      $type = NodeType::from($attributes['type']);
      $this->graph->addNode(
        $ID,
        $attributes['latDeg'],
        $attributes['longDeg'],
        $type,
        $attributes['isEntryNode'],
        $attributes['isExitNode']
      );
    }

    foreach ($graphml->graph->edge as $edge) {
      $source = (string) $edge['nodeID_1'];
      $target = (string) $edge['nodeID_2'];
      $weight = (float) $edge->data;
      $this->graph->addEdge($source, $target);
    }
  }

  public function test(): void {
    $this->graph->printGraph();
    $path = $this->aStar('AIR_EGKK', 'DC_TILBURG');
    if ($this->debug) {
      echo "\033[1;32mShortest path:\033[0m " . implode(" > ", array_map(fn($node) => $node->getID(), $path)) . "\n";
    } else {
      echo implode(" > ", array_map(fn($node) => $node->getID(), $path)) . "\n";
    }
  }

  private function aStar(string $startNodeID, string $endNodeID): array {
    $openSet = new SplPriorityQueue();
    $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    $startNode = $this->graph->getNode($startNodeID);
    $endNode = $this->graph->getNode($endNodeID);

    $openSet->insert($startNodeID, -($startNode->getDistanceTo($endNode)));

    $cameFrom = [];
    $gScore = [];
    $fScore = [];

    foreach ($this->graph->getNodes() as $node) {
      $gScore[$node->getID()] = PHP_INT_MAX;
      $fScore[$node->getID()] = PHP_INT_MAX;
    }

    $gScore[$startNodeID] = 0;
    $fScore[$startNodeID] = $startNode->getDistanceTo($endNode);

    $epsilon = 1e-10; // Small tolerance for floating-point comparison

    if ($this->debug) {
      echo "\033[1;34m=== Starting A* Search ===\033[0m\n";
      echo "From: $startNodeID | To: $endNodeID\n\n";
    }

    while (!$openSet->isEmpty()) {
      if ($this->debug) {
        echo "\033[1;36m--- Iteration ---\033[0m\n";
        echo "Open Set Size: " . $openSet->count() . "\n";
      }

      $extracted = $openSet->extract();
      $currentID = $extracted['data'];
      $current = $this->graph->getNode($currentID);
      $insertion_fScore = -$extracted['priority'];
      $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

      if ($this->debug) {
        echo "\033[32mProcessing Node: $currentID\033[0m\n";
        echo "  fScore: " . sprintf("%.6f", $insertion_fScore) . " | gScore (queue): " . sprintf("%.6f", $insertion_gScore) . "\n";
      }

      if ($gScore[$currentID] - $insertion_gScore <= $epsilon) {
        if ($this->debug) {
          echo "  \033[32mAction: Processing\033[0m | Current gScore: " . sprintf("%.6f", $gScore[$currentID]) . "\n";
        }
      } else {
        if ($this->debug) {
          echo "  \033[31mAction: Skipped\033[0m | Current gScore: " . sprintf("%.6f", $gScore[$currentID]) . " > Queue gScore: " . sprintf("%.6f", $insertion_gScore) . "\n";
          echo "\033[1;36m-----------------\033[0m\n\n";
        }
        continue;
      }

      if ($currentID === $endNodeID) {
        if ($this->debug) {
          echo "\033[1;32mGoal Reached: $currentID\033[0m\n";
        }
        return $this->reconstructPath($this->graph, $cameFrom, $currentID);
      }

      if ($this->debug) {
        echo "\n\033[1;33mNeighbors:\033[0m\n";
      }

      foreach ($this->graph->getNeighbors($currentID) as $neighborID => $weight) {
        if ($this->debug) {
          echo "  \033[36m- Neighbor: $neighborID\033[0m | Edge Weight: " . sprintf("%.6f", $weight) . "\n";
        }

        $tentativeGScore = $gScore[$currentID] + $weight;

        if ($tentativeGScore < $gScore[$neighborID]) {
          $cameFrom[$neighborID] = $currentID;
          $gScore[$neighborID] = $tentativeGScore;
          $fScore[$neighborID] = $gScore[$neighborID] + $this->graph->getNode($neighborID)->getDistanceTo($endNode);

          $openSet->insert($neighborID, -$fScore[$neighborID]);

          if ($this->debug) {
            echo "    \033[33mUpdated:\033[0m New gScore: " . sprintf("%.6f", $tentativeGScore) . " | New fScore: " . sprintf("%.6f", $fScore[$neighborID]) . "\n";
            $this->printHeuristicAndWeights($currentID, $neighborID, $tentativeGScore, $this->graph->getNode($neighborID)->getDistanceTo($endNode));
          }
        } else {
          if ($this->debug) {
            echo "    \033[37mNot Updated:\033[0m Tentative gScore: " . sprintf("%.6f", $tentativeGScore) . " >= Current gScore: " . sprintf("%.6f", $gScore[$neighborID]) . "\n";
          }
        }
      }

      if ($this->debug) {
        echo "\033[1;36m-----------------\033[0m\n\n";
      }
    }

    throw new InvalidArgumentException("No path found. The graph may be disconnected. Check if all edges are defined correctly.");
  }

  private function printHeuristicAndWeights(string $currentID, string $neighborID, float $totalPathWeight, float $heuristic): void {
    if ($this->debug) {
      echo "    \033[1;35mHeuristic Info:\033[0m\n";
      echo "      Current: $currentID | Neighbor: $neighborID\n";
      echo "      Path Weight: " . sprintf("%.6f", $totalPathWeight) . " | Heuristic: " . sprintf("%.6f", $heuristic) . "\n";
      echo "    \033[35m------------\033[0m\n";
    }
  }

  private function reconstructPath(RouterGraph $graph, array $cameFrom, string $currentID): array {
    $totalPath = [$graph->getNode($currentID)];
    while (isset($cameFrom[$currentID])) {
      $currentID = $cameFrom[$currentID];
      array_unshift($totalPath, $graph->getNode($currentID));
    }
    return $totalPath;
  }
}