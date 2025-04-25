<?php

namespace App\Models;

use App\Helpers\ConsoleHelper;
use App\Models\Address;
use App\Models\DeliveryMethod;
use App\Models\Location;
use App\Models\User;
use App\Models\WeightClass;
use App\Services\Router\Router;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RerouteToSelfException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\MoveOperationType;
use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Package find(mixed $id)
 */
class Package extends Model {
  use HasFactory;

  protected $primaryKey = 'id';
  protected $fillable = [
    'reference',
    'user_id',
    'origin_location_id',
    'destination_location_id',
    'current_location_id',
    'addresses_id',
    'status',
    'name',
    'lastName',
    'receiverEmail',
    'receiver_phone_number',
    'weight',
    'weight_id',
    'delivery_method_id',
    'dimension',
    'weight_price',
    'delivery_price',
    'paid'
  ];

  protected $attributes = [
    'status' => 'pending',
    'destination_location_id' => null,
    'addresses_id' => null
  ];

  ### Relationships


  /**
   * Get the user that owns the package.
   *
   * @return BelongsTo
   */
  public function user(): BelongsTo {
    return $this->belongsTo(User::class, 'user_id');
  }


  /**
   * Get the weight class of the package.
   *
   * @return BelongsTo
   */
  public function weightClass(): BelongsTo {
    return $this->belongsTo(WeightClass::class, 'weight_id');
  }


  /**
   * Get the delivery method of the package.
   *
   * @return BelongsTo
   */
  public function deliveryMethod(): BelongsTo {
    return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
  }


  /**
   * Get the destination location of the package.
   *
   * @return BelongsTo
   */
  public function destinationLocation(): BelongsTo {
    return $this->belongsTo(Location::class, 'destination_location_id');
  }


  /**
   * Get the address associated with the package.
   *
   * @return BelongsTo
   */
  public function address(): BelongsTo {
    return $this->belongsTo(Address::class, 'addresses_id');
  }


  /**
   * Get the origin location of the package.
   *
   * @return BelongsTo
   */
  public function originLocation(): BelongsTo {
    return $this->belongsTo(Location::class, 'origin_location_id');
  }


  /**
   * Get the current Location of the package.
   * (Only works if the current location is not a RouterNode but a Location.)
   *
   * @return BelongsTo
   */
  public function currentLocation(): BelongsTo {
    return $this->belongsTo(Location::class, 'current_location_id');
  }

  /**
   * Get the current Node.
   *
   * @return Node|null
   * @throws InvalidRouterArgumentException If the node ID is empty
   * @throws InvalidCoordinateException If the node ID is empty
   */
  public function currentNode(): ?Node {
    return Node::fromId($this->current_location_id);
  }


  /**
   * Get the movements of the package.
   *
   * @return HasMany
   */
  public function movements(): HasMany {
    return $this->hasMany(PackageMovement::class, 'package_id');
  }


  /**
   * Get the flight associated with the package.
   *
   * @return BelongsTo
   */
  public function flight(): BelongsTo {
    return $this->belongsTo(Flight::class, 'flight_id');
  }

  ### Public Methods


  /**
   * @return void
   * @throws InvalidCoordinateException
   * @throws InvalidRouterArgumentException
   * @throws NoPathFoundException
   * @throws NodeNotFoundException
   * @throws RouterException
   * @throws Exception
   */
  public function return(): void {
    $this->reroute($this->getAttribute('originLocation'));
  }


  /**
   * Reroute the package to a new destination.
   *
   * This method updates the package's movements to a new destination.
   * It generates movements if none exist and commits the new path as movements.
   *
   * @param  Location|string  $destination  The new destination for the package.
   * @return void
   * @throws Exception If movements are uninitialized or current movement not found.
   * @throws RouterException
   * @throws InvalidRouterArgumentException
   * @throws NodeNotFoundException
   * @throws InvalidCoordinateException
   * @throws NoPathFoundException
   */
  public function reroute(Location|string $destination): void {

    // Check for self-rerouting
    if (($destination == $this->current_location_id) ||
      ($destination instanceof Location && $destination->id == $this->current_location_id)) {
      throw new RerouteToSelfException($destination);
    }

    $movements = $this->movements()->orderBy('id')->get();

    // If no movements exist, generate them
    if ($movements->isEmpty()) {
      $this->generateMovements();
    }

    // Get the current location node
    $currentNode = $this->getCurrentMovement();
    if (!$currentNode) {
      throw new RouterException('Current location not found.');
    }

    // Resolve the Router service
    /** @var Router $router */
    $router = App::make(Router::class);

    $currentLocation = $this->getCurrentMovement()->getID();
    if (is_numeric($currentLocation)) {
      $currentLocation = Location::find($currentLocation);
    }

    // Get the path from origin to destination
    $path = $router->getPath(
      $currentLocation,
      $destination
    );

    // Commit the path as movements
    $this->commitMovements($path);
  }


  /**
   * Get the next location for the package.
   *
   * This method retrieves the next location node for the package based on its current movement.
   * It throws an exception if the current location or next movement is not found.
   *
   * @return Node|null The next location node or null if not found.
   * @throws Exception If movements are uninitialized or current movement not found.
   */
  public function getNextMovement(): ?Node {
    // Load all movements once (in this case to minimize I/O overhead/caching)
    $movements = $this->movements()->orderBy('id')->get();

    // If no movements exist, generate them
    if ($movements->isEmpty()) {
      $this->generateMovements();
    }

    // Get the current location node
    $currentNode = $this->getCurrentMovement();
    if (!$currentNode) {
      throw new RouterException('Current location not found.');
    }

    // Find the current movement based on the current node's ID
    $currentMovement = $movements->firstWhere('current_node_id', $currentNode->getID());
    if (!$currentMovement) {
      throw new RouterException('Current movement not found.');
    }

    // If there is no next movement, return null
    if (is_null($currentMovement->next_movement)) {
      return null;
    }

    // Find the next movement based on the next_movement ID
    $nextMovement = $movements->firstWhere('id', $currentMovement->next_movement);
    if (!$nextMovement) {
      return null;
    }

    // Get the node for the next movement's current_node_id
    $node = Node::fromId($nextMovement->current_node_id);
    if (!$node) {
      throw new RouterException('No next movement found.');
    }

    // Initialize and return the node with movement timestamps
    return $this->initializeNode($node, $nextMovement);
  }


  /**
   * Move the package forward one step.
   *
   * This method updates the package's movement by setting the appropriate scan times (arrival, check-in, check-out, departure).
   * If the final node is not an ADDRESS, you can keep running move() to set the final timestamps as you would, without moving.
   * If the final node is an ADDRESS, it automatically fills all timestamps in that movement with now() upon arrival.
   *
   * @param  MoveOperationType  $operation  The type of operation (IN, OUT, DELIVER).
   * @return array [success, message]
   * @throws Exception If no movements exist.
   */
  public function move(MoveOperationType $operation): array {
    // Load all movements once for efficiency
    $movements = $this->movements()->orderBy('id')->get();

    // If no movements exist, generate them
    if ($movements->isEmpty()) {
      $this->generateMovements();
    }

    // Find the current movement based on the current location ID
    $currentMovement = $movements->firstWhere('current_node_id', $this->current_location_id);
    if (!$currentMovement) {
      return [false, "This package does not have a valid package movement."];
    }

    // If delivery operation
    if ($operation === MoveOperationType::DELIVER) {
      // Handle delivery
      return $this->deliverPackage($currentMovement);
    }

    // If not delivery operation, handle IN or OUT operations
    return $this->performMovementOperation($currentMovement, $operation);
  }


  /**
   * Simulate a move. For seeding purposes
   *
   * This method sets the next unset timestamp in the sequence (arrival, check-in, check-out, departure).
   * If the package is at an ADDRESS and it's the final movement, all timestamps are set to now().
   *
   * @throws Exception If no movements exist or current movement not found.
   */
  public function fakeMove(): void {
    // Load all movements once for efficiency
    $movements = $this->movements()->orderBy('id')->get();

    // If no movements exist, generate them
    if ($movements->isEmpty()) {
      $this->generateMovements();
    }

    // Find the current movement based on the current location ID
    $currentMovement = $movements->firstWhere('current_node_id', $this->current_location_id);
    if (!$currentMovement) {
      throw new RouterException("No current movement found.");
    }

    // List of timestamps in sequence
    $timestamps = ['arrival_time', 'check_in_time', 'check_out_time', 'departure_time'];

    // Iterate through timestamps and set the first unset one
    foreach ($timestamps as $timestamp) {
      if (is_null($currentMovement->$timestamp)) {
        $currentMovement->$timestamp = now();
        $currentMovement->save();

        // If arriving at the final ADDRESS, set all timestamps
        if ($timestamp === 'arrival_time' && is_null($currentMovement->next_movement) && $this->currentLocation->location_type === NodeType::ADDRESS) {
          $currentMovement->check_in_time = now();
          $currentMovement->check_out_time = now();
          $currentMovement->departure_time = now();
          $currentMovement->save();
        }

        // If departing and there is a next movement, update current location
        if ($timestamp === 'departure_time' && !is_null($currentMovement->next_movement)) {
          $nextMovement = $movements->firstWhere('id', $currentMovement->next_movement);
          if ($nextMovement) {
            $this->current_location_id = $nextMovement->current_node_id;
            $this->save();
          }
        }

        return;
      }
    }

    // If all timestamps are set and there is a next movement, throw an exception
    if (!is_null($currentMovement->next_movement)) {
      throw new RouterException('Package did not move to next movement.');
    }
  }


  /**
   * Get the current location of the package as a Node.
   *
   * This method retrieves the current location of the package and converts it to a Node object.
   * It returns null if the current location is not found.
   *
   * @return Node|null The current location node or null if not found.
   * @throws Exception If movements are uninitialized.
   */
  public function getCurrentMovement(): ?Node {

    // If no movements exist, generate them
    if (!$this->movements()->exists()) {
      $this->generateMovements();
    }

    // Get the node for the current location ID
    $node = Node::fromId($this->current_location_id);
    if (!$node) {
      return null;
    }

    // Find the movement for the current node
    $movement = $this->movements()->where('current_node_id', $this->current_location_id)->first();
    if (!$movement) {
      return null;
    }

    // Initialize and return the node with movement timestamps
    return $this->initializeNode($node, $movement);
  }


  /**
   * Get the movements of the package as an array of Node objects.
   *
   * This method retrieves the chronological movements of the package as an array of Node objects.
   * It throws various exceptions if there are issues with the coordinates, arguments, or pathfinding.
   *
   * @return Node[]|null Array of Node objects representing chronological package movements.
   * @throws InvalidCoordinateException If any input coordinates are blatantly wrong.
   * @throws InvalidRouterArgumentException General bad argument exception.
   * @throws NoPathFoundException No possible path found. There may not exist a route.
   * @throws NodeNotFoundException Given node ID might not exist.
   * @throws RouterException General router error.
   */
  public function getMovements(): ?array {

    // Check if movements exist; if not, generate them
    if (!$this->movements()->exists()) {
      $this->generateMovements();
    }
    return $this->getMovementsFromDb();
  }


  /**
   * Generate movements for the package.
   *
   * This method is called internally if movements do not exist yet.
   * Could be called manually but there shouldn't be a need.
   * Does not re-generate movements.
   *
   * @throws InvalidCoordinateException If any input coordinates are blatantly wrong.
   * @throws InvalidRouterArgumentException General bad argument exception.
   * @throws NoPathFoundException No possible path found. There may not exist a route.
   * @throws NodeNotFoundException Given node ID might not exist.
   * @throws RouterException General router error.
   */
  public function generateMovements(): void {
    // Do not regenerate if movements already exist
    if ($this->movements()->exists()) {
      return;
    }

    // Resolve the Router service
    /** @var Router $router */
    $router = App::make(Router::class);

    $o = $this->getAttribute('origin_location_id');
    $d = $this->getAttribute('destination_location_id');

    // Get the path from origin to destination, cast to Location if appropriate;
    $path = $router->getPath(
      is_numeric($o) ? Location::find($o) : $o,
      is_numeric($d) ? Location::find($d) : $d,
    );

    // Commit the path as movements
    $this->commitMovements($path);

    // Set the current location to the first node in the path
    $this->setCurrentMovement($path[0]->getID());
  }

  ### Private Helper Methods


  /**
   * Handle the delivery of the package.
   *
   * This method is called when the move() operation is DELIVER.
   * It checks if the package is at the final destination and is an NodeType::ADDRESS.
   * If so, it sets all timestamps to now() if not already set.
   *
   * @param  PackageMovement  $currentMovement  The current movement of the package.
   * @return array [success, message]
   */
  private function deliverPackage(PackageMovement $currentMovement): array {
    // Check if this is the final movement
    if (!is_null($currentMovement->next_movement)) {
      return [false, "This package has not yet reached its final destination."];
    }

    // Get the current location
    $location = Location::find($this->current_location_id);
    if (!$location || $location->location_type !== NodeType::ADDRESS) {
      return [
        false,
        "This package is not fit for delivery => ".($location ? $location->location_type->value : 'Unknown')
      ];
    }

    // If arrival time is not set, set all timestamps to now
    if (is_null($currentMovement->arrival_time)) {
      $now = now();
      $currentMovement->arrival_time = $now;
      $currentMovement->check_in_time = $now;
      $currentMovement->check_out_time = $now;
      $currentMovement->departure_time = $now;
      $currentMovement->save();
      return [true, "Delivery Successful"];
    }

    // If arrival time is already set, the package has already been delivered
    return [false, "This package has already been delivered"];
  }


  /**
   * Perform a movement operation (IN or OUT).
   *
   * This method handles the scanning of the package for IN and OUT operations.
   * It sets the appropriate timestamp based on the operation and updates the current location if necessary.
   *
   * @param  PackageMovement  $currentMovement  The current movement of the package.
   * @param  MoveOperationType  $operation  The operation to perform (IN or OUT).
   * @return array [success, message]
   * @throws Exception If all timestamps are already set.
   */
  private function performMovementOperation(PackageMovement $currentMovement, MoveOperationType $operation): array {
    // Map of timestamps to their corresponding operation types
    $timestamps = [
      'arrival_time' => MoveOperationType::OUT,
      'check_in_time' => MoveOperationType::IN,
      'check_out_time' => MoveOperationType::OUT,
      'departure_time' => MoveOperationType::IN,
    ];

    // Iterate through timestamps and set the first unset one if the operation matches
    foreach ($timestamps as $timestamp => $expectedOperation) {
      if (is_null($currentMovement->$timestamp)) {
        if ($timestamp === "arrival_time" && is_null($currentMovement->next_movement) && is_numeric($this->current_location_id) && $this->currentLocation->location_type == NodeType::ADDRESS) {
          return [false, "This package has to be delivered."];
        }
        if ($operation === $expectedOperation) {

          $currentMovement->$timestamp = now();
          $currentMovement->save();

          // If departing and there is a next movement, update the current location
          if ($timestamp === 'departure_time' && !is_null($currentMovement->next_movement)) {
            $nextMovement = $this->movements()->find($currentMovement->next_movement);
            if ($nextMovement) {
              $this->current_location_id = $nextMovement->current_node_id;
              $this->save();
            }
          }

          return [true, "Package successfully scanned ".$operation->value];
        }
        // If the operation does not match, return an error
        return [
          false,
          "This package was not previously scanned ".($operation === MoveOperationType::OUT ? "in" : "out")."."
        ];
      }
    }

    // If all timestamps are set and there is no next movement, the package is at its final destination
    if (is_null($currentMovement->next_movement)) {
      return [false, "Package has reached its final destination."];
    }

    // If all timestamps are set but there is a next movement, throw an exception
    throw new RouterException('All timestamps are already set for the current movement.');
  }

  /**
   * @throws RouterException
   * @throws InvalidRouterArgumentException
   * @throws NodeNotFoundException
   * @throws InvalidCoordinateException
   * @throws NoPathFoundException
   */
  public function undoMove(MoveOperationType $operation): array {
    $timestamps = [
      'arrival_time' => MoveOperationType::OUT,
      'check_in_time' => MoveOperationType::IN,
      'check_out_time' => MoveOperationType::OUT,
      'departure_time' => MoveOperationType::IN,
    ];

    $movements = $this->movements()->orderBy('id')->get();

    // If no movements exist, generate them
    if ($movements->isEmpty()) {
      $this->generateMovements();
    }

    // Find the current movement based on the current location ID
    $currentMovement = $movements->firstWhere('current_node_id', $this->current_location_id);
    if (!$currentMovement) {
      return [false, "This package does not have a valid package movement."];
    }

    if ($currentMovement->arrival_time == null) {
      if ($operation == MoveOperationType::OUT) {
        return [false, "Move operations do not match."];
      }
      $previousMovement = $this->movements()->where("next_movement", $currentMovement->id)->first();
      if (!$previousMovement) {
        return [false, "No previous movement found, cannot undo action."];
      }
      $previousMovement->departure_time = null;
      $previousMovement->save();
      $this->current_location_id = $previousMovement->current_node_id;
      $this->save();
    } else {
      if ($currentMovement->check_in_time == null) {
        if ($operation == MoveOperationType::IN) {
          return [false, "Move operations do not match."];
        }
        $currentMovement->arrival_time = null;
      } else {
        if ($currentMovement->check_out_time == null) {
          if ($operation == MoveOperationType::OUT) {
            return [false, "Move operations do not match."];
          }
          $currentMovement->check_in_time = null;
        } else {
          if ($currentMovement->departure_time == null) {
            if ($operation == MoveOperationType::IN) {
              return [false, "Move operations do not match."];
            }
            $currentMovement->check_out_time = null;
          }
        }
      }
    }
    $currentMovement->save();
    return [true, "Action succesfully undone."];

  }

  /**
   * Set the current movement of the package.
   *
   * @param  string  $location  The ID of the location to set as current.
   */
  private function setCurrentMovement(string $location): void {
    $this->current_location_id = $location;
    $this->save();
  }


  /**
   * Commit the movements to the database.
   *
   * This method creates PackageMovement records for each node in the path.
   *
   * @param  Node[]  $path  The array of Node objects representing the path.
   */
  private function commitMovements(array $path): void {
    DB::transaction(function () use ($path) {
      // Check if the package already has movements and remove them
      if ($this->movements()->exists()) {
        $this->movements()->delete();
      }

      $previousMovement = null;
      $previousNode = null;

      foreach ($path as $i => $node) {
        // Prepare movement data
        $movementData = [
          'package_id' => $this->id,
          'handled_by_courier_id' => null,
          'vehicle_id' => null,
          'departure_time' => null,
          'arrival_time' => $i === 0 ? now() : null,
          'check_in_time' => $i === 0 ? now() : null,
          'check_out_time' => null,
          'current_node_id' => $node->getID(),
          'next_movement' => null,
          'router_edge_id' => null,
        ];

        // Create the movement
        $movement = $this->movements()->create($movementData);

        // Link to the previous movement if exists
        if ($previousMovement) {
          // Find the router edge between previous and current node
          $routerEdge = RouterEdges::where(function ($query) use ($previousNode, $node) {
            $query->where('origin_node', $previousNode->getID())
              ->where('destination_node', $node->getID())
              ->orWhere(function ($query) use ($previousNode, $node) {
                $query->where('origin_node', $node->getID())
                  ->where('destination_node', $previousNode->getID())
                  ->where('isUniDirectional', 0);
              });
          })->first();

          // Update the previous movement with the next movement ID and router edge ID
          $previousMovement->update([
            'next_movement' => $movement->id,
            'router_edge_id' => $routerEdge ? $routerEdge->id : null,
          ]);
        }

        // Set previous movement and node for the next iteration
        $previousMovement = $movement;
        $previousNode = $node;
      }
    });
  }


  /**
   * Get the movements from the database as an array of Node objects.
   *
   * @return Node[]|null Array of Node objects representing chronological package movements.
   * @throws InvalidRouterArgumentException If the node ID is empty
   * @throws InvalidCoordinateException If the node ID is empty
   */
  private function getMovementsFromDb(): ?array {
    // Load all movements ordered by ID
    $movements = $this->movements()->orderBy('id')->get();
    $nodes = [];

    // Convert each movement to a Node object
    foreach ($movements as $movement) {
      $node = Node::fromId($movement->current_node_id);
      if ($node) {
        $nodes[] = $this->initializeNode($node, $movement);
      }
    }

    return $nodes;
  }

  /**
   * Initialize a Node object with movement timestamps.
   *
   * @param  Node  $node  The Node object to initialize.
   * @param  PackageMovement  $movement  The movement containing timestamps.
   * @return Node The initialized Node object.
   */
  private function initializeNode(Node $node, PackageMovement $movement): Node {
    // It's usually better when changing the fields of a class that you do it in that class.
    // It would be best to instantly call the method (like done below) on the node instead of calling this (basically wrapper) function.
    return $node->initializeTimes($movement);
  }
}
