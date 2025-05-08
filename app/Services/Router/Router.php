<?php

namespace App\Services\Router;

use App\Helpers\ConsoleHelper;
use App\Models\Location;
use App\Models\Package;
use App\Models\PackageMovement;
use App\Models\RouterEdges;
use App\Models\RouterNodes;
use App\Services\Router\Helpers\GeoMath;
use App\Services\Router\Helpers\NodeHandlingTimeProvider;
use App\Services\Router\Helpers\TurnPenaltyCalculator;
use App\Services\Router\Helpers\VehicleSpeedProvider;
use App\Services\Router\Helpers\VehicleTypeResolver;
use App\Services\Router\Types\CoordType;
use App\Services\Router\Types\Exceptions\EdgeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\EdgeNotFoundException;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidNodeIDException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeAlreadyExistsException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\Exceptions\SelfLoopException;
use App\Services\Router\Types\KdTree;
use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\VehicleType;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use SplPriorityQueue;

/**
 * Class Router
 *
 * The Router class is responsible for finding optimal paths between locations
 * using an A* algorithm with time constraints. It manages a graph of nodes and edges,
 * and provides methods for adding/removing routes and calculating paths.
 *
 * @package App\Services\Router
 */
class Router {
  /**
   * Whether to output debug information
   */
  private bool $debug = true;

  // DATASTRUCTURES

  /**
   * Weighted graph representing the routing network
   */
  private RouterGraph $graph;

  /**
   * KdTree for all nodes regardless of type
   */
  private KdTree $kdTreeAll;

  /**
   * KdTree for entry nodes only
   */
  private KdTree $kdTreeEntry;

  /**
   * KdTree for exit nodes only
   */
  private KdTree $kdTreeExit;

  /**
   * KdTree for nodes that are both entry and exit
   */
  private KdTree $kdTreeEntryExit;

  // Helper classes

  /**
   * Service for resolving vehicle types
   */
  private VehicleTypeResolver $vehicleTypeResolver;

  /**
   * Service for calculating vehicle speeds
   */
  private VehicleSpeedProvider $vehicleSpeedProvider;

  /**
   * Service for calculating node handling times
   */
  private NodeHandlingTimeProvider $nodeHandlingTimeProvider;

  /**
   * Service for calculating turn penalties
   */
  private TurnPenaltyCalculator $turnPenaltyCalculator;


  /**
   * Initialize the Router with all required dependencies and data structures.
   *
   * Loads the routing graph from the database and builds the k-d trees
   * for efficient spatial queries.
   */
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
        if (!$this->refresh($edge->id, $force) && !$force) {
          throw new RouterException("Failed to complete the route removal. Use force=true to remove the edge anyway.");
        }

        // Remove the edge from the database
        $edge->delete();
      });
    } catch (Exception $e) {
      throw new RouterException("Transaction failed: ".$e->getMessage());
    }
  }


  /**
   * Adds a new route between two nodes.
   *
   * Creates a bidirectional edge between the specified origin and destination nodes.
   * The edge is added to both the in-memory graph and the database.
   *
   * @param  string  $origin  Origin node ID
   * @param  string  $destination  Destination node ID
   * @param  int  $validityDays  Route validity in days (default: 3650 days, 10 years)
   * @return void
   * @throws InvalidRouterArgumentException  If origin or destination ID is empty
   * @throws EdgeAlreadyExistsException  If the edge already exists
   * @throws SelfLoopException  If origin and destination are the same
   * @throws NodeNotFoundException  If either node doesn't exist
   * @throws RouterException  If the transaction fails
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
   * Finds the shortest path between two locations or nodes.
   *
   * This method calculates the optimal route between the given origin and destination,
   * taking into account time constraints, vehicle types, and handling times.
   * It can accept either Location objects or node IDs as input.
   *
   * @param  Location|string  $origin  Starting location or node ID
   * @param  Location|string  $destination  Ending location or node ID
   * @param  DateTime|null  $startTime  Start time for the path (default: current time)
   * @param  bool  $showETA  Whether to show ETA details (default: false)
   * @return array|null  Array of Node objects representing the path
   * @throws InvalidCoordinateException  If coordinates are invalid
   * @throws InvalidRouterArgumentException  If node IDs are invalid
   * @throws NoPathFoundException  If no path can be found
   * @throws NodeNotFoundException  If a node cannot be found
   * @throws RouterException  If there's a general routing error
   */
  public function getPath(
    Location|string $origin,
    Location|string $destination,
    DateTime $startTime = null,
    bool $showETA = false
  ): ?array {
    // If no start time is provided, use current time
    $startTime = $startTime ?? new DateTime();

    // Check type of origin and destination
    $oIsLoc = $origin instanceof Location;
    $dIsLoc = $destination instanceof Location;

    // Simple check for Node ID validity, actual screening is done in the graph
    if ((!$oIsLoc && $origin[0] !== '@') || (!$dIsLoc && $destination[0] !== '@')) {
      throw new InvalidRouterArgumentException("ID ($origin) is not a valid Node ID.");
    }

    if ($origin == $destination) {

      throw new SelfLoopException($oIsLoc ? $origin->id : $origin);
    }

    // Debug output of graph and k-d trees
    $this->debugPrintGraphAndTrees();

    // Convert origin to Node if it is a Location
    if ($oIsLoc) {
      $oN = self::locationToNode($origin);
      $origin = $this->findClosestNode(
        $oN->getLat(CoordType::RADIAN),
        $oN->getLong(CoordType::RADIAN),
        true,
        false
      );
    }

    // Convert destination to Node if it is a Location
    if ($dIsLoc) {
      $dN = self::locationToNode($destination);
      $destination = $this->findClosestNode(
        $dN->getLat(CoordType::RADIAN),
        $dN->getLong(CoordType::RADIAN),
        false,
        true
      );
    }

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
   * Prints debug information about the graph and k-d trees.
   * Only executes if debug mode is enabled.
   */
  private function debugPrintGraphAndTrees(): void {
    if (!$this->debug) {
      return;
    }

    $this->graph->printGraph();

    print "\033[1;34m=== k-d trees ===\033[0m\n";

    echo "\033[32mk-d tree [1/4] (all nodes irrespective of criteria):\033[0m\n";
    $this->kdTreeAll->visualize();

    echo "\033[32mk-d tree [2/4] (exclusively entry nodes):\033[0m\n";
    $this->kdTreeEntry->visualize();

    echo "\033[32mk-d tree [3/4] (exclusively exit nodes):\033[0m\n";
    $this->kdTreeExit->visualize();

    echo "\033[32mk-d tree [4/4] (Entry and exit nodes, not one or the other):\033[0m\n";
    $this->kdTreeEntryExit->visualize();
  }


  /**
   * Prints the path in both ID and description formats.
   *
   * Displays a formatted representation of the path showing both node IDs
   * and node descriptions.
   *
   * @param  array  &$path  Array of node objects
   * @return void
   */
  public static function printPath(array &$path): void {
    // Extract IDs and descriptions from path nodes
    $ids = array_map(fn($node) => $node->getID(), $path);
    $descs = array_map(fn($node) => $node->getDescription(), $path);

    // Print path with color formatting
    print "\033[1;32mShortest path: (as ID)\033[0m\n Start:\t> ".implode("\n\t> ", $ids)."\n\n";
    print "\033[1;32mShortest path: (as desc.)\033[0m\n Start:\t> ".implode("\n\t> ", $descs)."\n";
  }

  /**
   * Calculates the estimated time of arrival (ETA) for a given path.
   *
   * This method computes the total travel time and ETA by considering:
   * - Vehicle speed based on the segment type
   * - Handling time at each node based on node type
   * - Turn penalties based on the angle between segments
   *
   * @param  array  &$path  Array of node objects representing the route
   * @param  DateTime  $startTime  Start time for the path
   * @return array Associative array with:
   *               - 'eta' (DateTime): The estimated arrival time
   *               - 'totalTime' (float): Total travel time in hours
   *               - 'segments' (array): Detailed information about each segment
   */
  public function calculateETA(array &$path, DateTime $startTime): array {
    // Handle empty or single-node paths
    if (count($path) < 2) {
      return [
        'eta' => clone $startTime,
        'totalTime' => 0,
        'segments' => []
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
   * Displays a formatted table with the start time, estimated arrival time,
   * total travel time, and detailed information about each segment of the path.
   *
   * @param  array  &$etaDetails  ETA details from calculateETA method
   * @param  DateTime  $startTime  Start time for the path
   * @return void
   */
  private function printETA(array &$etaDetails, DateTime $startTime): void {
    $eta = $etaDetails['eta'];
    $totalTime = $etaDetails['totalTime'];
    $segments = $etaDetails['segments'];

    print "\n\033[1;34m=== ETA Details ===\033[0m\n";
    print "Start Time: ".$startTime->format('Y-m-d H:i:s')."\n";
    print "Estimated Arrival: ".$eta->format('Y-m-d H:i:s')."\n";
    print "Total Travel Time: ".sprintf("%.2f", $totalTime)." hours (".
      sprintf("%.0f", $totalTime * 60)." minutes)\n";
    print "Note: This is the actual travel time for the optimal path. It may be less than the tScore shown during path finding\n";
    print "      because tScore includes travel times for nodes that may not be on the final optimal path.\n\n";

    if (!empty($segments)) {
      print "\033[1;33mSegment Details:\033[0m\n";
      print "╔════════════════════╦════════════════════╦═════════════╦══════════════╦════════════╦═════════════╦════════════╦════════════╗\n";
      print "║ \033[1mFrom (ID)\033[0m          ║ \033[1mTo (ID)\033[0m            ║ \033[1mDist. (km)\033[0m  ║ \033[1mVehicle\033[0m      ║ \033[1mTravel (h)\033[0m ║ \033[1mHndl. (h)\033[0m   ║ \033[1mTurn (h)\033[0m   ║ \033[1mTotal (h)\033[0m  ║\n";
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
   * Finds the closest Node to a given point efficiently using k-d trees.
   *
   * This method selects the appropriate k-d tree based on the entry/exit criteria
   * and finds the nearest node to the specified coordinates.
   *
   * @param  float  $latRad  Latitude in radians
   * @param  float  $longRad  Longitude in radians
   * @param  bool  $mustBeEntry  Whether the closest node must be an entry node
   * @param  bool  $mustBeExit  Whether the closest node must be an exit node
   * @return string|null Closest node ID
   * @throws RouterException  If no suitable node is found matching the criteria
   */
  public function findClosestNode(float $latRad, float $longRad, bool $mustBeEntry, bool $mustBeExit): ?string {
    $latDeg = rad2deg($latRad);
    $longDeg = rad2deg($longRad);

    // Debug output: Start of the function
    $this->debugClosestNodeSearch($latDeg, $longDeg, $mustBeEntry, $mustBeExit);

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
    $this->debugClosestNodeResult($closestNode);

    return $closestNode->getID();
  }

  public function setDebugMode(bool $debug): void {
    $this->debug = $debug;
  }

  public function isDebugMode(): bool {
    return $this->debug;
  }

  /**
   * Outputs debug information at the start of the closest node search.
   *
   * @param  float  $latDeg  Latitude in degrees
   * @param  float  $longDeg  Longitude in degrees
   * @param  bool  $mustBeEntry  Whether the node must be an entry node
   * @param  bool  $mustBeExit  Whether the node must be an exit node
   * @return void
   */
  private function debugClosestNodeSearch(float $latDeg, float $longDeg, bool $mustBeEntry, bool $mustBeExit): void {
    if (!$this->debug) {
      return;
    }

    echo "\033[1;34m=== Starting Closest Node Search ===\033[0m\n".
      "Input Coordinates: [\033[1;35m".sprintf("%.4f", $latDeg)."\033[0m,\033[1;35m ".
      sprintf("%.4f", $longDeg)."\033[0m]\n".
      "Criteria: \n".
      "  mustBeEntry : \033[1;".($mustBeEntry ? "32" : "31")."m".
      ($mustBeEntry ? "Yes" : "No")."\033[0m\n".
      "  mustBeExit  : \033[1;".($mustBeExit ? "32" : "31")."m".
      ($mustBeExit ? "Yes" : "No")."\033[0m\n";
  }

  /**
   * Outputs debug information about the found closest node.
   *
   * @param  Node  $closestNode  The closest node found
   * @return void
   */
  private function debugClosestNodeResult(Node $closestNode): void {
    if (!$this->debug) {
      return;
    }

    $nodeId = $closestNode->getID();
    $nodeLat = $closestNode->getLat();
    $nodeLong = $closestNode->getLong();

    echo "Closest conforming Node found:\n  \033[38;2;255;140;0m".$nodeId."\033[0m [\033[1;35m".
      sprintf("%.4f", $nodeLat)."\033[0m,\033[1;35m ".
      sprintf("%.4f", $nodeLong)."\033[0m]\n\n\n\n";
  }

  /**
   * Loads the routing graph from the database.
   *
   * This method fetches all nodes and edges from the database and adds them to the graph.
   * It's called during initialization of the Router.
   *
   * @throws NodeAlreadyExistsException  If a node with the same ID already exists
   * @throws InvalidRouterArgumentException  If node or edge data is invalid
   * @throws InvalidNodeIDException  If a node ID is invalid
   * @throws InvalidCoordinateException  If coordinates are invalid
   * @throws SelfLoopException  If an edge connects a node to itself
   * @throws NodeNotFoundException  If a referenced node doesn't exist
   * @throws EdgeAlreadyExistsException  If an edge already exists
   * @throws Exception  For other unexpected errors
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
      $validFrom = new DateTime($edge->validFrom);
      $validTo = new DateTime($edge->validTo);

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


  /**
   * Finds the shortest path between two nodes, considering time constraints.
   *
   * This method implements the A* algorithm with time-based constraints.
   * It takes into account:
   * - Distance between nodes
   * - Travel time based on vehicle type
   * - Handling time at nodes
   * - Turn penalties
   * - Time-based edge validity
   *
   * @param  string  $startNodeID  Start node ID
   * @param  string  $endNodeID  End node ID
   * @param  DateTime|null  $startTime  Start time for the path (default: current time)
   * @return array  Path as an array of Node objects
   * @throws NodeNotFoundException  If start or end node cannot be found
   * @throws NoPathFoundException  If no path can be found between the nodes
   */
  private function aStar(string $startNodeID, string $endNodeID, ?DateTime $startTime = null): array {
    // If no start time is provided, use current time
    $startTime = $startTime ?? new DateTime();

    // Initialize the priority queue for the open set
    $openSet = new SplPriorityQueue();
    $openSet->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    // Get the start and end nodes
    $startNode = $this->graph->getNode($startNodeID); // May throw NodeNotFoundException
    $endNode = $this->graph->getNode($endNodeID);     // May throw NodeNotFoundException

    // Insert the start node into the open set with its priority
    $openSet->insert($startNodeID, -($startNode->getDistanceTo($endNode)));

    // Initialize tracking arrays
    $cameFrom = [];
    $gScore = []; // Distance score
    $tScore = []; // Time score
    $fScore = []; // Combined score for priority queue

    // Initialize scores for all nodes
    $this->initializeScores($gScore, $tScore, $fScore);

    // Set initial scores for the start node
    $gScore[$startNodeID] = 0;
    $tScore[$startNodeID] = 0;
    $fScore[$startNodeID] = $startNode->getDistanceTo($endNode);

    // Tolerance to avoid floating-point rounding errors
    $epsilon = 1e-10;

    // Debug output: Start of routing
    $this->debugStartRouting($startNodeID, $endNodeID, $startTime);

    // Main loop of the A* algorithm
    while (!$openSet->isEmpty()) {
      // Extract the node with the lowest f-score
      $extracted = $openSet->extract();
      $currentID = $extracted['data'];
      $current = $this->graph->getNode($currentID); // May throw NodeNotFoundException
      $insertion_fScore = -$extracted['priority'];
      $insertion_gScore = $insertion_fScore - $current->getDistanceTo($endNode);

      // Debug output: Current node
      $this->debugCurrentNode($currentID, $current, $openSet, $insertion_fScore, $insertion_gScore, $tScore);

      // Check if we've reached the destination
      if ($currentID === $endNodeID) {
        $this->debug && print "  \033[1;32mDestination reached\033[0m\n";
        return $this->reconstructPath($this->graph, $cameFrom, $currentID);
      }

      // Skip if the node's score has improved since it was added to the queue
      if ($gScore[$currentID] - $insertion_gScore > $epsilon) {
        $this->debug && print "  \033[31mAction: Skipped\033[0m | Current gScore: ".sprintf("%.6f",
            $gScore[$currentID])." > Queue gScore: ".sprintf("%.6f",
            $insertion_gScore)."\n\n";
        continue;
      }

      $this->debug && print "  \033[32mAction: Processing\033[0m\n";
      $this->debug && print "\n\033[1;33mNeighbors:\033[0m\n";

      // Calculate the current time at this node
      $currentTime = clone $startTime;
      $currentTime->modify('+'.(int) ($tScore[$currentID] * 60).' minutes'); // Convert hours to minutes

      // Process each neighbor
      $this->processNeighbors(
        $currentID,
        $current,
        $endNode,
        $currentTime,
        $startTime,
        $cameFrom,
        $gScore,
        $tScore,
        $fScore,
        $openSet
      );
    }

    // If we've exhausted the open set without finding a path
    throw new NoPathFoundException($startNodeID, $endNodeID);
  }

  /**
   * Initialize score arrays for all nodes in the graph.
   *
   * @param  array &$gScore  Distance score array to initialize
   * @param  array &$tScore  Time score array to initialize
   * @param  array &$fScore  Combined score array to initialize
   */
  private function initializeScores(array &$gScore, array &$tScore, array &$fScore): void {
    foreach ($this->graph->getNodes() as $node) {
      $gScore[$node->getID()] = PHP_INT_MAX;
      $tScore[$node->getID()] = PHP_INT_MAX;
      $fScore[$node->getID()] = PHP_INT_MAX;
    }
  }

  /**
   * Output debug information at the start of routing.
   *
   * @param  string  $startNodeID  Start node ID
   * @param  string  $endNodeID  End node ID
   * @param  DateTime  $startTime  Start time
   */
  private function debugStartRouting(string $startNodeID, string $endNodeID, DateTime $startTime): void {
    if (!$this->debug) {
      return;
    }

    print "\033[1;34m=== Starting Routing ===\033[0m\n".
      "From: $startNodeID | To: $endNodeID\n".
      "Start Time: ".$startTime->format('Y-m-d H:i:s')."\n";
  }

  /**
   * Output debug information about the current node being processed.
   *
   * @param  string  $currentID  Current node ID
   * @param  Node  $current  Current node
   * @param  SplPriorityQueue  $openSet  Open set
   * @param  float  $insertion_fScore  F-score at insertion
   * @param  float  $insertion_gScore  G-score at insertion
   * @param  array&  $tScore  Time score array
   */
  private function debugCurrentNode(
    string $currentID,
    Node $current,
    SplPriorityQueue $openSet,
    float $insertion_fScore,
    float $insertion_gScore,
    array $tScore
  ): void {
    if (!$this->debug) {
      return;
    }

    print "\n\033[1;32mNode:\n  \033[38;2;255;140;0m$currentID\033[0m ({$current->getDescription()})\n".
      "  Open Set Size: ".$openSet->count()."\n".
      "  fScore: ".sprintf("%.6f", $insertion_fScore)." km | ".
      "gScore: ".sprintf("%.6f", $insertion_gScore)." km | ".
      "tScore: ".sprintf("%.6f", $tScore[$currentID])." hours\n";
  }

  /**
   * Process all neighbors of the current node in the A* algorithm.
   *
   * @param  string  $currentID  Current node ID
   * @param  Node  $current  Current node
   * @param  Node  $endNode  End node
   * @param  DateTime  $currentTime  Current time at this node
   * @param  DateTime  $startTime  Overall start time
   * @param  array &$cameFrom  Path tracking array
   * @param  array &$gScore  Distance score array
   * @param  array &$tScore  Time score array
   * @param  array &$fScore  Combined score array
   * @param  SplPriorityQueue  $openSet  Open set priority queue
   * @throws NodeNotFoundException
   */
  private function processNeighbors(
    string $currentID,
    Node $current,
    Node $endNode,
    DateTime $currentTime,
    DateTime $startTime,
    array &$cameFrom,
    array &$gScore,
    array &$tScore,
    array &$fScore,
    SplPriorityQueue $openSet
  ): void {
    // Iterate over each neighbor of the current node, considering time constraints
    foreach ($this->graph->getNeighbors($currentID, $currentTime) as $neighborID => $edgeData) {
      $weight = $edgeData['weight']; // Distance in km

      // Convert string vehicle type to enum
      $vehicleTypeString = $edgeData['vehicleType'];
      $vehicleType = VehicleType::from($vehicleTypeString);

      // Calculate time to travel this edge in hours
      $timeToTravel = $this->vehicleSpeedProvider->calculateTravelTime($weight, $vehicleType);

      $this->debug && print "  \033[38;2;255;140;0m- $neighborID\033[0m (".sprintf("%.6f",
          $weight)." km, ".sprintf("%.6f", $timeToTravel)." hours, $vehicleType->value)\n";

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
      $turnPenalty = $this->calculateTurnPenalty($currentID, $current, $neighborNode, $vehicleType, $weight, $cameFrom);
      $tentativeTScore += $turnPenalty;

      // Debug output for handling time
      if ($this->debug && $handlingTime > 0) {
        print "    \033[36mHandling Time:\033[0m ".sprintf("%.6f", $handlingTime)." hours ($nodeType->value)\n";
      }

      // Check if this path to the neighbor is better
      if ($tentativeGScore < $gScore[$neighborID]) {
        // Check if the edge will still be valid when we reach it
        $arrivalTime = clone $startTime;
        $arrivalTime->modify('+'.(int) ($tentativeTScore * 60).' minutes'); // Convert hours to minutes

        if ($arrivalTime <= $edgeData['validTo']) {
          // Update path and scores
          $cameFrom[$neighborID] = $currentID;
          $gScore[$neighborID] = $tentativeGScore;
          $tScore[$neighborID] = $tentativeTScore;
          $fScore[$neighborID] = $gScore[$neighborID] + $neighborNode->getDistanceTo($endNode);

          // Insert the neighbor into the open set with its priority
          $openSet->insert($neighborID, -$fScore[$neighborID]);

          // Debug output for updated node
          $this->debugUpdatedNode($neighborID, $tentativeGScore, $tentativeTScore, $fScore, $arrivalTime, $edgeData);
        } else {
          // Debug output for skipped edge due to validity
          $this->debug && print "    \033[1;31mSkipped:\033[0m Edge will not be valid at arrival time ".
            $arrivalTime->format('Y-m-d H:i:s')." (valid until ".$edgeData['validTo']->format('Y-m-d H:i:s').")\n\n";
        }
      } else {
        // Debug output for not updated node
        $this->debug && print "    \033[1;35mNot Updated:\033[0m Tentative gScore: ".sprintf("%.6f",
            $tentativeGScore)." km ≥ Current gScore: ".sprintf("%.6f", $gScore[$neighborID])." km\n\n";
      }
    }
  }

  /**
   * Calculate turn penalty for a node transition.
   *
   * @param  string  $currentID  Current node ID
   * @param  Node  $current  Current node
   * @param  Node  $neighborNode  Neighbor node
   * @param  VehicleType  $vehicleType  Vehicle type
   * @param  float  $weight  Edge weight
   * @param  array  &$cameFrom  Path tracking array
   * @return float Turn penalty in hours
   * @throws NodeNotFoundException
   */
  private function calculateTurnPenalty(
    string $currentID,
    Node $current,
    Node $neighborNode,
    VehicleType $vehicleType,
    float $weight,
    array $cameFrom
  ): float {
    $turnPenalty = 0;

    // Calculate turn penalty if we have a previous node
    if (isset($cameFrom[$currentID])) {
      $previousID = $cameFrom[$currentID];
      $previousNode = $this->graph->getNode($previousID);

      // Calculate the angle between previous, current, and next nodes
      $angle = $this->calculateAngleBetweenNodes($previousNode, $current, $neighborNode);

      // Calculate turn penalty using the TurnPenaltyCalculator
      $turnPenalty = $this->turnPenaltyCalculator->calculateTurnPenalty($angle, $vehicleType, $weight);

      // Debug output for turn penalty
      if ($this->debug) {
        $turnType = $this->turnPenaltyCalculator->getTurnTypeDisplayName($angle);
        print "    \033[36mTurn Penalty:\033[0m ".sprintf("%.6f",
            $turnPenalty)." hours ($turnType, ".sprintf("%.2f", $angle)." degrees)\n";
      }
    }

    return $turnPenalty;
  }

  /**
   * Output debug information about an updated node.
   *
   * @param  string  $neighborID  Neighbor node ID
   * @param  float  $tentativeGScore  Tentative g-score
   * @param  float  $tentativeTScore  Tentative t-score
   * @param  array  &$fScore  F-score array
   * @param  DateTime  $arrivalTime  Arrival time
   * @param  array  &$edgeData  Edge data
   * @throws NodeNotFoundException
   */
  private function debugUpdatedNode(
    string $neighborID,
    float $tentativeGScore,
    float $tentativeTScore,
    array $fScore,
    DateTime $arrivalTime,
    array $edgeData
  ): void {
    if (!$this->debug) {
      return;
    }

    $neighborNode = $this->graph->getNode($neighborID);
    // Use the destination node ID from the edge data if available, otherwise use the neighbor ID
    $destinationID = $edgeData['to'] ?? $neighborID;
    $heuristic = $neighborNode->getDistanceTo($this->graph->getNode($destinationID));

    echo "    \033[33mUpdated:\033[0m New gScore: ".sprintf("%.6f", $tentativeGScore).
      " km | New tScore: ".sprintf("%.6f", $tentativeTScore).
      " hours | New fScore: ".sprintf("%.6f", $fScore[$neighborID])." km\n".
      "    \033[33mHeuristic Info:\033[0m\n".
      "      Σ Path Weight: ".sprintf("%.6f", $tentativeGScore).
      " km | Heuristic: ".sprintf("%.6f", $heuristic)." km\n";

    echo "      Arrival Time: ".$arrivalTime->format('Y-m-d H:i:s').
      " | Valid Until: ".$edgeData['validTo']->format('Y-m-d H:i:s')."\n";

    // Check heuristic admissibility
    $isAdmissible = $tentativeGScore <= $fScore[$neighborID];
    $admissibilitySymbol = $isAdmissible ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
    echo "      Heuristic Admissible: $admissibilitySymbol\n\n";
  }


  /**
   * Reconstructs the complete path from the cameFrom array.
   *
   * This helper function for A* algorithm traces back from the destination
   * to the start node using the cameFrom array to build the complete path.
   *
   * @param  RouterGraph  $graph  RouterGraph object containing all nodes
   * @param  array  &$cameFrom  Array mapping each node to its predecessor
   * @param  string  $currentID  Current (destination) node ID
   * @return array  Array of Node objects representing the path from start to destination
   * @throws NodeNotFoundException  If a node in the path cannot be found
   */
  private function reconstructPath(RouterGraph $graph, array $cameFrom, string $currentID): array {
    // Start with the destination node
    $totalPath = [$graph->getNode($currentID)]; // May throw NodeNotFoundException

    // Reconstruct the path from the cameFrom array by working backwards
    while (isset($cameFrom[$currentID])) {
      $currentID = $cameFrom[$currentID];
      array_unshift($totalPath, $graph->getNode($currentID)); // May throw NodeNotFoundException
    }

    return $totalPath;
  }


  /**
   * Converts a Location model to a Node object.
   *
   * Creates a Node object from a Location model, using the appropriate ID
   * based on the location type (address ID or infrastructure ID).
   *
   * @param  Location  $location  The Location model to convert
   * @return Node  The created Node object
   * @throws InvalidCoordinateException  If coordinates are invalid
   * @throws InvalidRouterArgumentException  If location data is invalid
   */
  private static function locationToNode(Location $location): Node {
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
   *
   * This method finds all package movements that use the specified edge and
   * attempts to reroute the associated packages to their destinations.
   * It requires the RouterEdge to still be in the database, but be removed from the graph.
   * If force is enabled, packages that cannot be rerouted will be marked as stranded.
   *
   * @param  int  $edgeId  The ID of the RouterEdge to check
   * @param  bool  $force  Whether to force the operation even if packages cannot be rerouted
   * @return bool  True if refresh completed without stranding packages, false otherwise
   */
  private function refresh(int $edgeId, bool $force = false): bool {
    $failedPackages = [];

    // Get all package movements that use this edge
    $movements = PackageMovement::where('router_edge_id', $edgeId)->get();

    foreach ($movements as $movement) {
      // Get the associated package
      $package = Package::find($movement->package_id);

      if ($package) {
        try {
          // Attempt to reroute the package to its destination
          $package->reroute($package->destination_location_id);
        } catch (Exception $e) {
          $package->clearMovements();
          // Add the package to the failed packages array if rerouting fails
          $failedPackages[] = $package;
        }
      }
    }

    // Handle packages which failed to be rerouted
    if (!empty($failedPackages) && $force) {
      foreach ($failedPackages as $package) {
        // Mark the package as stranded
        $package->status = 'Stranded';
        $package->save();
      }
    }

    return empty($failedPackages); // Return success or fail
  }


  /**
   * Builds k-d trees for all types of nodes in the graph.
   *
   * Creates four different k-d trees for efficient spatial queries:
   * 1. All nodes regardless of type
   * 2. Entry nodes only
   * 3. Exit nodes only
   * 4. Nodes that are both entry and exit
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
   * Calculates the angle between three nodes.
   *
   * This method determines the turn angle when moving from previousNode to currentNode
   * and then to nextNode. The angle is used to calculate turn penalties.
   *
   * @param  Node  $previousNode  The previous node in the path
   * @param  Node  $currentNode  The current node in the path
   * @param  Node  $nextNode  The next node in the path
   * @return float  The angle in degrees between the three nodes
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
