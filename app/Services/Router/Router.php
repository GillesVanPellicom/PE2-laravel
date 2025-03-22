<?php


namespace App\Services\Router {

  use App\Helpers\ConsoleHelper;
  use App\Models\Location;
  use App\Services\Router\Types\CoordType;
  use App\Services\Router\Types\Exceptions\EdgeAlreadyExistsException;
  use App\Services\Router\Types\Exceptions\FailedCoordinatesFetchException;
  use App\Services\Router\Types\Exceptions\FileNotFoundException;
  use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
  use App\Services\Router\Types\Exceptions\InvalidGraphMLException;
  use App\Services\Router\Types\Exceptions\InvalidNodeIDException;
  use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
  use App\Services\Router\Types\Exceptions\NodeAlreadyExistsException;
  use App\Services\Router\Types\Exceptions\NodeNotFoundException;
  use App\Services\Router\Types\Exceptions\NoPathFoundException;
  use App\Services\Router\Types\Exceptions\RouterException;
  use App\Services\Router\Types\Exceptions\SelfLoopException;
  use App\Services\Router\Types\NodeType;
  use App\Services\Router\Types\RouterGraph;
  use App\Services\Router\Types\Node;
  use Exception;
  use SplPriorityQueue;


  class Router {

    private RouterGraph $graph;
    private bool $debug = false;


    public function __construct() {
      try {
        $this->graph = new RouterGraph();
        echo("Deserializing database...\n");
        $this->deserializeDb();
      } catch (Exception $e) {
        ConsoleHelper::error($e->getMessage());
        exit(1);
      }
    }


    /**
     * @returns Node[]|null Array of node objects representing the path in movements
     * @throws RouterException
     * @throws InvalidRouterArgumentException
     * @throws InvalidCoordinateException
     * @throws NodeNotFoundException
     * @throws NoPathFoundException
     */
    public function getPath(Location $origin, Location $destination): ?array {
      $oN = new Node(
        $origin->getAttribute('location_type') == NodeType::ADDRESS
          ? $origin->getAttribute('id')
          : $origin->getAttribute('infrastructure_id'),
        $origin->getAttribute('description'),
        $origin->getAttribute('location_type'),
        $origin->getAttribute('latitude'),
        $origin->getAttribute('longitude'),
      );

      $dN = new Node(
        $destination->getAttribute('location_type') == NodeType::ADDRESS
          ? $destination->getAttribute('id')
          : $destination->getAttribute('infrastructure_id'),
        $destination->getAttribute('description'),
        $origin->getAttribute('location_type'),
        $destination->getAttribute('latitude'),
        $destination->getAttribute('longitude'),
        );

      $oN->setArrivedAt(now());
      $oN->setCheckedInAt(now());


      $path = $this->aStar(
        $this->findClosestNode(
          $oN->getLat(CoordType::RADIAN),
          $oN->getLong(CoordType::RADIAN),
          true,
          false),

        $this->findClosestNode(
          $dN->getLat(CoordType::RADIAN),
          $dN->getLong(CoordType::RADIAN),
          false,
          true
        ));

      array_unshift($path, $oN);
      $path[] = $dN;
      return $path;
    }


    /**
     * Builds the complete path including imaginary nodes if needed
     *
     * @param  string  $startId  Starting node ID
     * @param  string  $endId  Ending node ID
     * @param ?object  $startNode  Starting imaginary node if created
     * @param ?object  $endNode  Ending imaginary node if created
     * @return array Array of node objects representing the path
     * @throws NoPathFoundException
     * @throws InvalidRouterArgumentException
     * @throws NodeNotFoundException
     */
    private function buildPath(string $startId, string $endId, ?object $startNode, ?object $endNode): array {
      // Get core path using A* algorithm
      $path = $this->aStar($startId, $endId);

      // Add imaginary nodes if they exist
      $startNode && array_unshift($path, $startNode);
      $endNode && $path[] = $endNode;

      return $path;
    }


    /**
     * Prints the path in both ID and description formats
     *
     * @param  array  $path  Array of node objects
     * @return void
     */
    public static function printPath(array $path): void {
      // Extract IDs and descriptions from path nodes
      $ids = array_map(fn($node) => $node->getID(), $path);
      $descs = array_map(fn($node) => $node->getDescription(), $path);

      // Print path with color formatting
      print "\033[1;32mShortest path: (as ID)\033[0m\n Start:\t> ".implode("\n\t> ", $ids)."\n\n";
      print "\033[1;32mShortest path: (as desc.)\033[0m\n Start:\t> ".implode("\n\t> ", $descs)."\n";
    }





    /**
     * Finds the closest Node to a given point.
     *
     * @param  float  $latRad  Latitude in radians
     * @param  float  $longRad  Longitude in radians
     * @param  bool  $mustBeEntry  Whether closest the node must be an entry node
     * @param  bool  $mustBeExit  Whether closest the node must be an exit node
     * @return string|null Closest node ID
     * @throws RouterException
     */
    private function findClosestNode(float $latRad, float $longRad, bool $mustBeEntry, bool $mustBeExit): ?string {
      $closestNode = null;
      $minDistance = PHP_INT_MAX;

      /** @var Node $node */
      foreach ($this->graph->getNodes() as $node) {
        if ($mustBeExit && !$node->isExitNode()) {
          continue;
        }
        if ($mustBeEntry && !$node->isEntryNode()) {
          continue;
        }

        $distance = GeoMath::sphericalCosinesDistance(
          $latRad,
          $longRad,
          $node->getLat(CoordType::RADIAN),
          $node->getLong(CoordType::RADIAN));
        if ($distance < $minDistance) {
          $minDistance = $distance;
          $closestNode = $node;
        }
      }

      if ($closestNode === null) {
        throw new RouterException("Cannot connect imaginary node to graph. No suitable node found matching entry/exit criteria to connect to address node variant.. ");
      }

      return $closestNode->getID();
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
     * @throws NodeAlreadyExistsException
     * @throws InvalidRouterArgumentException
     * @throws InvalidNodeIDException
     * @throws InvalidCoordinateException
     * @throws SelfLoopException
     * @throws NodeNotFoundException
     * @throws EdgeAlreadyExistsException
     */
    private function deserializeDb(): void {
      // Fetch all nodes from the database
      $nodes = \App\Models\RouterNodes::all();
      // Iterate over each node and add it to the graph
      foreach ($nodes as $node) {

        $this->graph->addNode(
          $node->id,
          $node->description,
          (float) $node->latDeg,
          (float) $node->lonDeg,
          $node->location_type,
          $node->isEntry,
          $node->isExit
        );
      }

      // Fetch all edges from the database
      $edges = \App\Models\RouterEdges::all();

      // Iterate over each edge and add it to the graph
      foreach ($edges as $edge) {
        $this->graph->addEdge($edge->origin_node, $edge->destination_node);
      }
    }

    /**
     * Finds the shortest path between two nodes.
     *
     * @param  string  $startNodeID  Start node ID
     * @param  string  $endNodeID  End node ID
     * @return array Path in hops between Node objects
     * @throws NodeNotFoundException|NoPathFoundException
     * @throws InvalidRouterArgumentException
     */
    private function aStar(string $startNodeID, string $endNodeID): array {

      if ($this->debug) {
        $this->graph->printGraph();
      }

      $openSet = new SplPriorityQueue();
      $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

      $startNode = $this->graph->getNode($startNodeID); // May throw NodeNotFoundException
      $endNode = $this->graph->getNode($endNodeID);     // May throw NodeNotFoundException

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

        $extracted = $openSet->extract();
        $currentID = $extracted['data'];
        $current = $this->graph->getNode($currentID); // May throw NodeNotFoundException
        $insertion_fScore = -$extracted['priority'];
        $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

        if ($this->debug) {
          echo "\n\033[32mProcessing Node: $currentID ({$current->getDescription()})\033[0m\n";
          echo "  Open Set Size: ".$openSet->count()."\n";
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
          $this->debug && print "\033[1;34m>>> ROUTER DEBUG END\033[0m\n\n";

          return $this->reconstructPath($this->graph, $cameFrom, $currentID);
        }

        $this->debug && print "\n\033[1;33mNeighbors:\033[0m\n";

        // Iterate over each neighbor of the current node
        foreach ($this->graph->getNeighbors($currentID) as $neighborID => $weight) {
          if ($this->debug) {
            echo "  \033[38;2;255;140;0m- Neighbor: $neighborID\033[0m | Edge Weight: ".sprintf("%.6f", $weight)."\n";
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
                  $tentativeGScore)." | New fScore: ".sprintf("%.6f", $fScore[$neighborID])."\n";
              echo "    \033[1;35mHeuristic Info:\033[0m\n";
              echo "      Current: $currentID | Neighbor: $neighborID\n";
              echo "      Σ Path Weight: ".sprintf("%.6f", $tentativeGScore)." | Heuristic: ".sprintf("%.6f",
                  $this->graph->getNode($neighborID)->getDistanceTo($endNode))."\n";

              // Check heuristic admissibility
              $isAdmissible = $tentativeGScore <= $fScore[$neighborID];
              $admissibilitySymbol = $isAdmissible ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
              echo "      Heuristic Admissible: $admissibilitySymbol\n\n";
            }
          } else {
            if ($this->debug) {
              echo "    \033[37mNot Updated:\033[0m Tentative gScore: ".sprintf("%.6f",
                  $tentativeGScore)." ≥ Current gScore: ".sprintf("%.6f", $gScore[$neighborID])."\n\n";
            }
          }
        }
      }
      throw new NoPathFoundException($startNodeID, $endNodeID);
    }

    /**
     * Helper function for A*.
     * Reconstructs the path from the cameFrom array.
     *
     * @param  RouterGraph  $graph  RouterGraph object
     * @param  array  $cameFrom  Array of visited nodes
     * @param  string  $currentID  Current node ID
     * @return array
     * @throws NodeNotFoundException
     */
    private function reconstructPath(RouterGraph $graph, array $cameFrom, string $currentID): array {
      $totalPath = [$graph->getNode($currentID)]; // May throw NodeNotFoundException
      // Reconstruct the path from the cameFrom array
      while (isset($cameFrom[$currentID])) {
        $currentID = $cameFrom[$currentID];
        array_unshift($totalPath, $graph->getNode($currentID)); // May throw NodeNotFoundException
      }
      return $totalPath;
    }
  }

}