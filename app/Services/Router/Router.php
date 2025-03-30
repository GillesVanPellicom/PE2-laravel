<?php


namespace App\Services\Router {

  use App\Helpers\ConsoleHelper;
  use App\Models\Location;
  use App\Models\Package;
  use App\Models\PackageMovement;
  use App\Models\RouterEdges;
  use App\Models\RouterNodes;
  use App\Services\Router\Types\CoordType;
  use App\Services\Router\Types\Exceptions\EdgeAlreadyExistsException;
  use App\Services\Router\Types\Exceptions\EdgeNotFoundException;
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
  use App\Services\Router\Types\KdTree;
  use App\Services\Router\Types\NodeType;
  use App\Services\Router\Types\RouterGraph;
  use App\Services\Router\Types\Node;
  use Exception;
  use Illuminate\Support\Facades\DB;
  use SplPriorityQueue;


  class Router {

    private bool $debug = true;

    // DATASTRUCTURES

    // Weighted graph
    private RouterGraph $graph;

    // KdTrees for different node types
    private KdTree $kdTreeAll;
    private KdTree $kdTreeEntry;
    private KdTree $kdTreeExit;
    private KdTree $kdTreeEntryExit;


    public function __construct() {
      try {
        $this->graph = new RouterGraph();
        $this->deserializeDb();
        $this->buildKdTrees();
      } catch (Exception $e) {
        ConsoleHelper::error($e->getMessage());
        exit(1);
      }
    }


    /**
     * Removes a route between two nodes safely.
     * Will automatically try and reroute all packages scheduled for the removed route.
     * Will not remove the route if this causes stranded packages, unless force is enabled.
     *
     * @param  string  $origin  The origin node ID.
     * @param  string  $destination  The destination node ID.
     * @param  bool  $force  Force removal even if this causes stranded packages. Default: false.
     * @return void
     * @throws InvalidRouterArgumentException
     * @throws NodeNotFoundException
     * @throws RouterException
     */
    public function removeRoute(string $origin, string $destination, bool $force = false): void {
      // Validate the origin and destination IDs
      if (empty($origin) || empty($destination)) {
        throw new InvalidRouterArgumentException("Origin or destination ID cannot be empty.");
      }

      // Make operation atomic
      try {
        DB::transaction(function () use ($origin, $destination, $force) {
          // Find the edge in the database
          $edge = RouterEdges::where(function ($query) use ($origin, $destination) {
            $query->where('origin_node', $origin)
              ->where('destination_node', $destination)
              ->orWhere(function ($query) use ($origin, $destination) {
                $query->where('origin_node', $destination)
                  ->where('destination_node', $origin)
                  ->where('isUniDirectional', false);
              });
          })->first();

          // If the edge does not exist, throw an exception
          if (!$edge) {
            throw new EdgeNotFoundException($origin, $destination);
          }

          // Remove the edge from the graph
          $this->graph->removeEdge($origin, $destination);

          // Refresh packages that were using this edge, if this strands packages:
          if (!$this->refresh($edge->id) && !$force) {
            throw new RouterException("Failed complete the route removal. Use force=true to remove the edge anyway.");
          }

          // Remove the edge from the database
          $edge->delete();

        });
      } catch (Exception $e) {
        throw new RouterException("Transaction failed: ".$e->getMessage());
      }
    }


    /**
     * @param  string  $origin  origin node ID
     * @param  string  $destination  destination node ID
     * @param  int  $validityDays  route validity in days (default: 3650 days, 10 years)
     * @return void
     * @throws InvalidRouterArgumentException
     * @throws SelfLoopException
     * @throws NodeNotFoundException
     * @throws Exception
     */
    public function addRoute(string $origin, string $destination, int $validityDays = 3650): void {

      // Validate the origin and destination IDs
      if (empty($origin) || empty($destination)) {
        throw new InvalidRouterArgumentException("Origin or destination ID cannot be empty.");
      }

      // Make operation atomic
      try {
        DB::transaction(function () use ($origin, $destination, $validityDays) {
          // Check if the edge already exists
          $existingEdge = RouterEdges::where(function ($query) use ($origin, $destination) {
            $query->where('origin_node', $origin)
              ->where('destination_node', $destination)
              ->orWhere(function ($query) use ($origin, $destination) {
                $query->where('origin_node', $destination)
                  ->where('destination_node', $origin)
                  ->where('isUniDirectional', false);
              });
          })->first();

          // If it exists, throw an exception
          if ($existingEdge) {
            throw new EdgeAlreadyExistsException($origin, $destination);
          }

          // Add the edge to the database
          RouterEdges::create([
            'origin_node' => $origin,
            'destination_node' => $destination,
            'weight' => 1.0,
            'isUniDirectional' => false,
            'validFrom' => now(),
            'validTo' => now()->addDays($validityDays),
          ]);

          // Add the edge to the graph
          $this->graph->addEdge($origin, $destination);
        });
      } catch (Exception $e) {
        throw new RouterException("Transaction failed: ".$e->getMessage());
      }
    }


    /**
     * @param  Location|string  $origin
     * @param  Location|string  $destination
     * @return array|null
     * @throws InvalidCoordinateException
     * @throws InvalidRouterArgumentException
     * @throws NoPathFoundException
     * @throws NodeNotFoundException
     * @throws RouterException
     */
    public function getPath(Location|string $origin, Location|string $destination): ?array {
      // Check type of origin and destination
      $oIsLoc = $origin instanceof Location;
      $dIsLoc = $destination instanceof Location;

      // Simple check for Node ID validity, actual screening is done in the graph
      if ((!$oIsLoc && $origin[0] !== '@') || (!$dIsLoc && $destination[0] !== '@')) {
        throw new InvalidRouterArgumentException("ID ($origin) is not a valid Node ID.");
      }

      if ($this->debug) {
        $this->graph->printGraph();


        $this->debug && print "\033[1;34m=== k-d trees ===\033[0m\n\n\n";

        echo "\033[32mk-d tree [1/4] (all nodes):\033[0m\n";
        $this->kdTreeAll->visualize();

        echo "\033[32mk-d tree [2/4] (exclusively entry nodes):\033[0m\n";
        $this->kdTreeEntry->visualize();

        echo "\033[32mk-d tree [3/4] (exclusively exit nodes):\033[0m\n";
        $this->kdTreeExit->visualize();

        echo "\033[32mk-d tree [4/4] (exclusively entry/exit nodes):\033[0m\n";
        $this->kdTreeEntryExit->visualize();

      }

      // Convert origin to Node if it is a Location
      if ($oIsLoc) {
        $oN = Router::locationToNode($origin);
        $origin = $this->findClosestNode(
          $oN->getLat(CoordType::RADIAN),
          $oN->getLong(CoordType::RADIAN),
          true,
          false
        );
      }

      // Convert destination to Node if it is a Location
      if ($dIsLoc) {
        $dN = Router::locationToNode($destination);
        $destination = $this->findClosestNode(
          $dN->getLat(CoordType::RADIAN),
          $dN->getLong(CoordType::RADIAN),
          false,
          true
        );
      }

      // Generate path
      $path = $this->aStar($origin, $destination);

      // Adjust path for Locations
      if ($oIsLoc) {
        array_unshift($path, $oN);
      }

      if ($dIsLoc) {
        $path[] = $dN;
      }

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
     * Finds the closest Node to a given point efficiently.
     *
     * @param  float  $latRad  Latitude in radians
     * @param  float  $longRad  Longitude in radians
     * @param  bool  $mustBeEntry  Whether closest the node must be an entry node
     * @param  bool  $mustBeExit  Whether closest the node must be an exit node
     * @return string|null Closest node ID
     * @throws RouterException
     */
    /**
     * Finds the closest Node to a given point efficiently.
     *
     * @param  float  $latRad  Latitude in radians
     * @param  float  $longRad  Longitude in radians
     * @param  bool  $mustBeEntry  Whether closest the node must be an entry node
     * @param  bool  $mustBeExit  Whether closest the node must be an exit node
     * @return string|null Closest node ID
     * @throws RouterException
     */
    public function findClosestNode(float $latRad, float $longRad, bool $mustBeEntry, bool $mustBeExit): ?string {
      $latDeg = rad2deg($latRad);
      $longDeg = rad2deg($longRad);

      // Debug output: Start of the function
      if ($this->debug) {
        echo "\033[1;34m=== Starting Closest Node Search ===\033[0m\n".
          "Input Coordinates: [\033[1;35m".sprintf("%.4f", $latDeg).", ".sprintf("%.4f",
            $longDeg)."\033[0m] (degrees)\n".
          "Criteria: \n  mustBeEntry: \033[1;".($mustBeEntry ? "32" : "31")."m".($mustBeEntry ? "true" : "false")."\033[0m".
          "\n  mustBeExit: \033[1;".($mustBeExit ? "32" : "31")."m".($mustBeExit ? "true" : "false")."\033[0m\n";
      }

      // Determine which kdTree to use based on entry/exit criteria
      if ($mustBeEntry && $mustBeExit) {
        $kdTree = $this->kdTreeEntryExit;
        $treeType = "EntryExit";
      } elseif ($mustBeEntry) {
        $kdTree = $this->kdTreeEntry;
        $treeType = "Entry";
      } elseif ($mustBeExit) {
        $kdTree = $this->kdTreeExit;
        $treeType = "Exit";
      } else {
        $kdTree = $this->kdTreeAll;
        $treeType = "All";
      }

      // Debug output: Selected KD-tree
      if ($this->debug) {
        echo "Selected KD-Tree: \033[1;33m".$treeType."\033[0m\nQuerying...\n";
      }

      // Find the closest node using the kdTree
      $closestNode = $kdTree->findNearest($latDeg, $longDeg);

      if ($closestNode === null) {
        // Debug output: No node found
        if ($this->debug) {
          echo "\033[1;31mNo suitable node found matching criteria.\033[0m\n";
        }
        throw new RouterException("Cannot connect imaginary node to graph. No suitable node found matching entry/exit criteria.");
      }

      // Debug output: Result
      if ($this->debug) {
        $nodeId = $closestNode->getID();
        $nodeLat = $closestNode->getLat(CoordType::DEGREE);
        $nodeLong = $closestNode->getLong(CoordType::DEGREE);
        echo "Closest Node Found: \033[1;33m".$nodeId."\033[0m [\033[1;35m".sprintf("%.4f", $nodeLat).", ".sprintf("%.4f", $nodeLong)."\033[0m]\n\n\n";
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
      $nodesModels = RouterNodes::all();
      // Iterate over each node and add it to the graph
      foreach ($nodesModels as $node) {

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
      $edges = RouterEdges::all();

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

      $this->debug && print "\033[1;34m=== Starting A* Search ===\033[0m\nFrom: $startNodeID | To: $endNodeID\n\n";


      // Main loop of the A* algorithm
      while (!$openSet->isEmpty()) {

        $extracted = $openSet->extract();
        $currentID = $extracted['data'];
        $current = $this->graph->getNode($currentID); // May throw NodeNotFoundException
        $insertion_fScore = -$extracted['priority'];
        $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

        $this->debug && print "\n\033[32mProcessing Node: $currentID ({$current->getDescription()})\033[0m\n  Open Set Size: ".$openSet->count()."\nfScore: ".sprintf("%.6f",
            $insertion_fScore)." | gScore (queue): ".sprintf("%.6f", $insertion_gScore)."\n";


        // Check if the current node's gScore is valid
        if ($gScore[$currentID] - $insertion_gScore <= $epsilon) {
          $this->debug && print "  \033[32mAction: Processing\033[0m | Current gScore: ".sprintf("%.6f",
              $gScore[$currentID])."\n";

        } else {
          $this->debug && print "  \033[31mAction: Skipped\033[0m | Current gScore: ".sprintf("%.6f",
              $gScore[$currentID])." > Queue gScore: ".sprintf("%.6f",
              $insertion_gScore)."\n\033[1;36m-----------------\033[0m\n\n";

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
          $this->debug && print "  \033[38;2;255;140;0m- Neighbor: $neighborID\033[0m | Edge Weight: ".sprintf("%.6f",
              $weight)."\n";

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
                  $tentativeGScore)." | New fScore: ".sprintf("%.6f",
                  $fScore[$neighborID])."\n    \033[1;35mHeuristic Info:\033[0m\n      Current: $currentID | Neighbor: $neighborID\n      Σ Path Weight: ".sprintf("%.6f",
                  $tentativeGScore)." | Heuristic: ".sprintf("%.6f",
                  $this->graph->getNode($neighborID)->getDistanceTo($endNode))."\n";

              // Check heuristic admissibility
              $isAdmissible = $tentativeGScore <= $fScore[$neighborID];
              $admissibilitySymbol = $isAdmissible ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
              echo "      Heuristic Admissible: $admissibilitySymbol\n\n";
            }
          } else {
            $this->debug && print  "    \033[37mNot Updated:\033[0m Tentative gScore: ".sprintf("%.6f",
                $tentativeGScore)." ≥ Current gScore: ".sprintf("%.6f", $gScore[$neighborID])."\n\n";
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


    /**
     * @param  Location  $location
     * @return Node
     * @throws InvalidCoordinateException
     * @throws InvalidRouterArgumentException
     */
    static private function locationToNode(Location $location): Node {
      return new Node(
        $location->getAttribute('location_type') == NodeType::ADDRESS
          ? $location->getAttribute('id')
          : $location->getAttribute('infrastructure_id'),
        $location->getAttribute('description'),
        $location->getAttribute('location_type'),
        $location->getAttribute('latitude'),
        $location->getAttribute('longitude'),
        $location->getAttribute('addresses_id')
      );
    }


    /**
     * Refreshes packages that have movements using the specified edge.
     * Requires the RouterEdge to still be in the database, but be removed in the graph.
     *
     * @param  int  $edgeId  The ID of the RouterEdge to check.
     * @return bool true if refresh completed without stranding packages, else false.
     */
    private function refresh(int $edgeId): bool {
      $failedPackages = [];

      // Get all package movements that use this edge
      $movements = PackageMovement::where('router_edge_id', $edgeId)->get();

      foreach ($movements as $movement) {
        // Get the associated package
        $package = Package::find($movement->package_id);

        if ($package) {
          try {
            // Attempt to reroute the package to its destination
            $package->reroute($package->destinationLocation);
          } catch (Exception $e) {
            // Add the package to the failed packages array if rerouting fails
            $failedPackages[] = $package;
          }
        }
      }
      // TODO: handle packages which failed to be rerouted
      return empty($failedPackages); // Return success or fail
    }


    /**
     * Builds k-d trees for all types of nodes in the graph.
     *
     * @return void
     */
    private function buildKdTrees(): void {
      // Get all nodes
      $allNodes = $this->graph->getNodes();

      // Filter nodes based on their types
      $entryNodes = array_filter($allNodes, fn($node) => $node->isEntryNode());
      $exitNodes = array_filter($allNodes, fn($node) => $node->isExitNode());
      $entryExitNodes = array_filter($allNodes, fn($node) => $node->isEntryNode() && $node->isExitNode());

      // Build k-d trees for all nodes, entry nodes, exit nodes, and entry-exit nodes
      $this->kdTreeAll = new KdTree($allNodes);
      $this->kdTreeEntry = new KdTree($entryNodes);
      $this->kdTreeExit = new KdTree($exitNodes);
      $this->kdTreeEntryExit = new KdTree($entryExitNodes);
    }
  }
}