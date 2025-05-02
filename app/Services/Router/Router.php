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
  use App\Services\Router\Types\TurnType;
  use App\Services\Router\Types\VehicleType;
  use App\Services\Router\NodeHandlingTimeProvider;
  use App\Services\Router\TurnPenaltyCalculator;
  use App\Services\Router\VehicleSpeedProvider;
  use App\Services\Router\VehicleTypeResolver;
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

    // Helper classes
    private VehicleTypeResolver $vehicleTypeResolver;
    private VehicleSpeedProvider $vehicleSpeedProvider;
    private NodeHandlingTimeProvider $nodeHandlingTimeProvider;
    private TurnPenaltyCalculator $turnPenaltyCalculator;


    public function __construct() {
      try {
        // Initialize helper classes
        $this->vehicleTypeResolver = new VehicleTypeResolver();
        $this->vehicleSpeedProvider = new VehicleSpeedProvider();
        $this->nodeHandlingTimeProvider = new NodeHandlingTimeProvider();
        $this->turnPenaltyCalculator = new TurnPenaltyCalculator();

        // Initialize graph and data structures
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
     * @param  \DateTime|null  $startTime  Start time for the path (default: current time)
     * @param  bool  $showETA  Whether to show ETA details (default: false)
     * @return array|null
     * @throws InvalidCoordinateException
     * @throws InvalidRouterArgumentException
     * @throws NoPathFoundException
     * @throws NodeNotFoundException
     * @throws RouterException
     */
    public function getPath(
      Location|string $origin,
      Location|string $destination,
      ?\DateTime $startTime = null,
      bool $showETA = false
    ): ?array {
      // If no start time is provided, use current time
      $startTime = $startTime ?? new \DateTime();

      // Check type of origin and destination
      $oIsLoc = $origin instanceof Location;
      $dIsLoc = $destination instanceof Location;

      // Simple check for Node ID validity, actual screening is done in the graph
      if ((!$oIsLoc && $origin[0] !== '@') || (!$dIsLoc && $destination[0] !== '@')) {
        throw new InvalidRouterArgumentException("ID ($origin) is not a valid Node ID.");
      }

      if ($this->debug) {
        $this->graph->printGraph();


        $this->debug && print "\033[1;34m=== k-d trees ===\033[0m\n";

        echo "\033[32mk-d tree [1/4] (all nodes irrespective of criteria):\033[0m\n";
        $this->kdTreeAll->visualize();

        echo "\033[32mk-d tree [2/4] (exclusively entry nodes):\033[0m\n";
        $this->kdTreeEntry->visualize();

        echo "\033[32mk-d tree [3/4] (exclusively exit nodes):\033[0m\n";
        $this->kdTreeExit->visualize();

        echo "\033[32mk-d tree [4/4] (Entry and exit nodes, not one or the other):\033[0m\n";
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

      // Calculate ETA details first (needed for both debug output and return)
      $etaDetails = null;

      // Generate path with time constraints
      $path = $this->aStar($origin, $destination, $startTime);

      // Adjust path for Locations
      if ($oIsLoc) {
        array_unshift($path, $oN);
      }

      if ($dIsLoc) {
        $path[] = $dN;
      }

      // Calculate ETA details
      $etaDetails = $this->calculateETA($path, $startTime);

      // Print ETA if requested
      if ($showETA || $this->debug) {
        $this->printETA($etaDetails, $startTime);

        // Print debug output end message after ETA details
        $this->debug && print "\033[1;34m>>> ROUTER DEBUG END\033[0m\n\n";
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
     * Calculates the estimated time of arrival (ETA) for a given path.
     * Takes into account vehicle speed, handling time at nodes, and turn penalties.
     *
     * @param  array  $path  Array of node objects
     * @param  \DateTime  $startTime  Start time for the path
     * @return array Associative array with 'eta' (\DateTime) and 'totalTime' (float, in hours)
     */
    public function calculateETA(array $path, \DateTime $startTime): array {
      if (count($path) < 2) {
        return [
          'eta' => clone $startTime,
          'totalTime' => 0
        ];
      }

      $totalTime = 0; // Total time in hours
      $currentTime = clone $startTime;
      $segments = [];
      $totalSegments = count($path) - 1;

      // Process each segment of the path
      for ($i = 0; $i < $totalSegments; $i++) {
        $currentNode = $path[$i];
        $nextNode = $path[$i + 1];

        // Calculate distance between nodes
        $distance = $currentNode->getDistanceTo($nextNode);

        // Determine vehicle type based on position in path
        $vehicleType = $this->vehicleTypeResolver->resolveVehicleType(
          $currentNode,
          $nextNode,
          $i,
          $totalSegments
        );

        // Calculate travel time for this segment
        $travelTime = $this->vehicleSpeedProvider->calculateTravelTime($distance, $vehicleType);

        // Add handling time for the next node
        $nodeType = $nextNode->getType();
        $handlingTime = $this->nodeHandlingTimeProvider->getHandlingTime($nodeType);

        // Calculate turn penalty if not the first segment
        $turnPenalty = 0;
        if ($i > 0) {
          $previousNode = $path[$i - 1];

          // Calculate the angle and turn penalty
          $angle = $this->calculateAngleBetweenNodes($previousNode, $currentNode, $nextNode);
          $turnPenalty = $this->turnPenaltyCalculator->calculateTurnPenalty($angle, $vehicleType, $distance);
        }

        // Add times for this segment
        $segmentTime = $travelTime + $handlingTime + $turnPenalty;
        $totalTime += $segmentTime;

        // Update current time
        $currentTime->modify('+'.(int) ($segmentTime * 60).' minutes');

        // Store segment details for debugging
        $segments[] = [
          'from' => $currentNode->getID(),
          'to' => $nextNode->getID(),
          'distance' => $distance,
          'vehicleType' => $vehicleType->value,
          'travelTime' => $travelTime,
          'handlingTime' => $handlingTime,
          'turnPenalty' => $turnPenalty,
          'segmentTime' => $segmentTime
        ];
      }

      return [
        'eta' => $currentTime,
        'totalTime' => $totalTime,
        'segments' => $segments
      ];
    }

    /**
     * Prints the estimated time of arrival (ETA) details for a given path.
     *
     * @param  array  $etaDetails  ETA details from calculateETA method
     * @param  \DateTime  $startTime  Start time for the path
     * @return void
     */
    public function printETA(array $etaDetails, \DateTime $startTime): void {
      $eta = $etaDetails['eta'];
      $totalTime = $etaDetails['totalTime'];
      $segments = $etaDetails['segments'];

      print "\n\033[1;34m=== ETA Details ===\033[0m\n";
      print "Start Time: ".$startTime->format('Y-m-d H:i:s')."\n";
      print "Estimated Arrival: ".$eta->format('Y-m-d H:i:s')."\n";
      print "Total Travel Time: ".sprintf("%.2f", $totalTime)." hours (".sprintf("%.0f",
          $totalTime * 60)." minutes)\n\n";

      if (!empty($segments)) {
        print "\033[1;33mSegment Details:\033[0m\n";
        print "╔════════════════════╦════════════════════╦═════════════╦══════════════╦════════════╦═════════════╦════════════╦════════════╗\n";
        print "║ \033[1mFrom\033[0m               ║ \033[1mTo\033[0m                 ║ \033[1mDistance\033[0m    ║ \033[1mVehicle\033[0m      ║ \033[1mTravel\033[0m     ║ \033[1mHandling\033[0m    ║ \033[1mTurn\033[0m       ║ \033[1mTotal\033[0m      ║\n";
        print "╠════════════════════╬════════════════════╬═════════════╬══════════════╬════════════╬═════════════╬════════════╬════════════╣\n";

        foreach ($segments as $segment) {
          printf(
            "║ \033[38;2;255;140;0m%-18s\033[0m ║ \033[38;2;255;140;0m%-18s\033[0m ║ %8.2f km ║ %-12s ║ %7.2f h  ║ %8.2f h  ║ %7.2f h  ║ %7.2f h  ║\n",
            $segment['from'],
            $segment['to'],
            $segment['distance'],
            $segment['vehicleType'],
            $segment['travelTime'],
            $segment['handlingTime'],
            $segment['turnPenalty'],
            $segment['segmentTime']
          );
        }

        print "╚════════════════════╩════════════════════╩═════════════╩══════════════╩════════════╩═════════════╩════════════╩════════════╝\n";
      }
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
          "Input Coordinates: [\033[1;35m".sprintf("%.4f", $latDeg)."\033[0m,\033[1;35m ".sprintf("%.4f",
            $longDeg)."\033[0m]\n".
          "Criteria: \n".
          "  mustBeEntry : \033[1;".($mustBeEntry ? "32" : "31")."m".($mustBeEntry ? "Yes" : "No")."\033[0m\n".
          "  mustBeExit  : \033[1;".($mustBeExit ? "32" : "31")."m".($mustBeExit ? "Yes" : "No")."\033[0m\n";
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
        echo "Closest conforming Node found:\n  \033[38;2;255;140;0m".$nodeId."\033[0m [\033[1;35m".sprintf("%.4f",
            $nodeLat)."\033[0m,\033[1;35m ".sprintf("%.4f", $nodeLong)."\033[0m]\n\n\n\n";
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
        // Convert validFrom and validTo to DateTime objects
        $validFrom = new \DateTime($edge->validFrom);
        $validTo = new \DateTime($edge->validTo);

        // Get vehicle type from the edge if available, default to 'Truck'
        $vehicleType = $edge->vehicle_type ?? 'Truck';

        $this->graph->addEdge(
          $edge->origin_node,
          $edge->destination_node,
          $validFrom,
          $validTo,
          $vehicleType
        );
      }
    }


    // These arrays are now handled by the helper classes:
    // - VehicleSpeedProvider
    // - NodeHandlingTimeProvider
    // - TurnPenaltyCalculator

    /**
     * Finds the shortest path between two nodes, considering time constraints.
     *
     * @param  string  $startNodeID  Start node ID
     * @param  string  $endNodeID  End node ID
     * @param  \DateTime|null  $startTime  Start time for the path (default: current time)
     * @return array Path in hops between Node objects
     * @throws NodeNotFoundException|NoPathFoundException
     * @throws InvalidRouterArgumentException
     */
    private function aStar(string $startNodeID, string $endNodeID, ?\DateTime $startTime = null): array {
      // If no start time is provided, use current time
      $startTime = $startTime ?? new \DateTime();

      $openSet = new SplPriorityQueue();
      $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

      $startNode = $this->graph->getNode($startNodeID); // May throw NodeNotFoundException
      $endNode = $this->graph->getNode($endNodeID);     // May throw NodeNotFoundException

      // Insert the start node into the open set with its priority
      $openSet->insert($startNodeID, -($startNode->getDistanceTo($endNode)));

      $cameFrom = [];
      $gScore = []; // Distance score
      $tScore = []; // Time score
      $fScore = []; // Combined score for priority queue

      // Initialize gScore, tScore, and fScore for each node
      foreach ($this->graph->getNodes() as $node) {
        $gScore[$node->getID()] = PHP_INT_MAX;
        $tScore[$node->getID()] = PHP_INT_MAX;
        $fScore[$node->getID()] = PHP_INT_MAX;
      }

      $gScore[$startNodeID] = 0;
      $tScore[$startNodeID] = 0;
      $fScore[$startNodeID] = $startNode->getDistanceTo($endNode);

      $epsilon = 1e-10; // Tolerance to avoid (very hard to debug) FP rounding errors.

      $this->debug && print "\033[1;34m=== Starting Routing ===\033[0m\n
      From: $startNodeID | To: $endNodeID\n
      Start Time: ".$startTime->format('Y-m-d H:i:s')."\n";

      // Main loop of the A* algorithm
      while (!$openSet->isEmpty()) {
        $extracted = $openSet->extract();
        $currentID = $extracted['data'];
        $current = $this->graph->getNode($currentID); // May throw NodeNotFoundException
        $insertion_fScore = -$extracted['priority'];
        $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

        $this->debug && print "\n\033[1;32mNode:\n  \033[38;2;255;140;0m$currentID\033[0m ({$current->getDescription()})\n  Open Set Size: ".$openSet->count()."\n  fScore: ".sprintf("%.6f",
            $insertion_fScore)." km | gScore (queue): ".sprintf("%.6f",
            $insertion_gScore)." km | tScore: ".sprintf("%.6f", $tScore[$currentID])." hours\n";


        // Check if the current node is the goal
        if ($currentID === $endNodeID) {
          $this->debug && print "  \033[1;32mDestination reached\033[0m\n";

          // Reconstruct the path first
          // Debug output end will be printed in getPath after ETA details
          return $this->reconstructPath($this->graph, $cameFrom, $currentID);
        }

        // Check if the current node's gScore is valid
        if ($gScore[$currentID] - $insertion_gScore <= $epsilon) {
          $this->debug && print "  \033[32mAction: Processing\033[0m\n";
        } else {
          $this->debug && print "  \033[31mAction: Skipped\033[0m | Current gScore: ".sprintf("%.6f",
              $gScore[$currentID])." > Queue gScore: ".sprintf("%.6f",
              $insertion_gScore)."\n\n";
          continue;
        }

        $this->debug && print "\n\033[1;33mNeighbors:\033[0m\n";

        // Calculate the current time at this node
        $currentTime = clone $startTime;
        $currentTime->modify('+'.(int) ($tScore[$currentID] * 60).' minutes'); // Convert hours to minutes

        // For A* algorithm, we don't need to determine the segment index
        // The vehicle type will be determined based on node IDs

        // Iterate over each neighbor of the current node, considering time constraints
        foreach ($this->graph->getNeighbors($currentID, $currentTime) as $neighborID => $edgeData) {
          $weight = $edgeData['weight']; // Distance in km

          // Convert string vehicle type to enum
          $vehicleTypeString = $edgeData['vehicleType'];
          $vehicleType = VehicleType::from($vehicleTypeString);

          // Calculate time to travel this edge in hours
          $timeToTravel = $this->vehicleSpeedProvider->calculateTravelTime($weight, $vehicleType);

          $this->debug && print "  \033[38;2;255;140;0m- $neighborID\033[0m (".sprintf("%.6f",
              $weight)." km, ".sprintf("%.6f", $timeToTravel)." hours, {$vehicleType->value})\n";

          // Calculate tentative scores
          $tentativeGScore = $gScore[$currentID] + $weight;

          // Base travel time
          $tentativeTScore = $tScore[$currentID] + $timeToTravel;

          // Add handling time based on node type
          $neighborNode = $this->graph->getNode($neighborID);
          $nodeType = $neighborNode->getType();
          $handlingTime = $this->nodeHandlingTimeProvider->getHandlingTime($nodeType);
          $tentativeTScore += $handlingTime;

          // Calculate turn penalty if we have a previous node
          if (isset($cameFrom[$currentID])) {
            $previousID = $cameFrom[$currentID];
            $previousNode = $this->graph->getNode($previousID);

            // Calculate the angle between previous, current, and next nodes
            $angle = $this->calculateAngleBetweenNodes($previousNode, $current, $neighborNode);

            // Calculate turn penalty using the TurnPenaltyCalculator
            $turnPenalty = $this->turnPenaltyCalculator->calculateTurnPenalty($angle, $vehicleType, $weight);

            // Add turn penalty to time score
            $tentativeTScore += $turnPenalty;

            // Debug output for turn penalty
            if ($this->debug) {
              $turnType = $this->turnPenaltyCalculator->getTurnTypeDisplayName($angle);
              print "    \033[36mTurn Penalty:\033[0m ".sprintf("%.6f",
                  $turnPenalty)." hours ($turnType, ".sprintf("%.2f", $angle)." degrees)\n";
            }
          }

          // Debug output for handling time
          if ($this->debug && $handlingTime > 0) {
            print "    \033[36mHandling Time:\033[0m ".sprintf("%.6f", $handlingTime)." hours ({$nodeType->value})\n";
          }

          // Check if this path to the neighbor is better
          if ($tentativeGScore < $gScore[$neighborID]) {
            // Check if the edge will still be valid when we reach it
            $arrivalTime = clone $startTime;
            $arrivalTime->modify('+'.(int) ($tentativeTScore * 60).' minutes'); // Convert hours to minutes

            if ($arrivalTime <= $edgeData['validTo']) {
              $cameFrom[$neighborID] = $currentID;
              $gScore[$neighborID] = $tentativeGScore;
              $tScore[$neighborID] = $tentativeTScore;
              $fScore[$neighborID] = $gScore[$neighborID] + $this->graph->getNode($neighborID)->getDistanceTo($endNode);

              // Insert the neighbor into the open set with its priority
              $openSet->insert($neighborID, -$fScore[$neighborID]);

              if ($this->debug) {
                echo "    \033[33mUpdated:\033[0m New gScore: ".sprintf("%.6f",
                    $tentativeGScore)." km | New tScore: ".sprintf("%.6f",
                    $tentativeTScore)." hours | New fScore: ".sprintf("%.6f",
                    $fScore[$neighborID])." km\n    \033[33mHeuristic Info:\033[0m\n      Σ Path Weight: ".sprintf("%.6f",
                    $tentativeGScore)." km | Heuristic: ".sprintf("%.6f",
                    $this->graph->getNode($neighborID)->getDistanceTo($endNode))." km\n";

                echo "      Arrival Time: ".$arrivalTime->format('Y-m-d H:i:s')." | Valid Until: ".$edgeData['validTo']->format('Y-m-d H:i:s')."\n";

                // Check heuristic admissibility
                $isAdmissible = $tentativeGScore <= $fScore[$neighborID];
                $admissibilitySymbol = $isAdmissible ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
                echo "      Heuristic Admissible: $admissibilitySymbol\n\n";
              }
            } else {
              $this->debug && print "    \033[1;31mSkipped:\033[0m Edge will not be valid at arrival time ".
                $arrivalTime->format('Y-m-d H:i:s')." (valid until ".$edgeData['validTo']->format('Y-m-d H:i:s').")\n\n";
            }
          } else {
            $this->debug && print "    \033[1;35mNot Updated:\033[0m Tentative gScore: ".sprintf("%.6f",
                $tentativeGScore)." km ≥ Current gScore: ".sprintf("%.6f", $gScore[$neighborID])." km\n\n";
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

    /**
     * Calculate the angle between three nodes.
     *
     * @param  Node  $previousNode  The previous node
     * @param  Node  $currentNode  The current node
     * @param  Node  $nextNode  The next node
     * @return float The angle in degrees
     */
    private function calculateAngleBetweenNodes(Node $previousNode, Node $currentNode, Node $nextNode): float {
      return GeoMath::calculateTurnAngle(
        $previousNode->getLat(CoordType::RADIAN),
        $previousNode->getLong(CoordType::RADIAN),
        $currentNode->getLat(CoordType::RADIAN),
        $currentNode->getLong(CoordType::RADIAN),
        $nextNode->getLat(CoordType::RADIAN),
        $nextNode->getLong(CoordType::RADIAN)
      );
    }
  }
}
