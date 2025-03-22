<?php

namespace App\Models;

use App\Services\Router\Router;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Package extends Model {
  use HasFactory;

  protected $primaryKey = 'id'; // Custom primary key
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
    'weight_id',
    'delivery_method_id',
    'dimension',
    'weight_price',
    'delivery_price'
  ];

  protected $attributes = [
    'status' => 'pending',
    'destination_location_id' => null,
    'addresses_id' => null
  ];

  public function user() {
    return $this->belongsTo(User::class, "user_id");
  }

  public function weightClass() {
    return $this->belongsTo(WeightClass::class, 'weight_id');
  }

  public function deliveryMethod() {
    return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
  }

  public function destinationLocation() {
    return $this->belongsTo(Location::class, 'destination_location_id');
  }

  public function address() {
    return $this->belongsTo(Address::class, 'addresses_id');
  }

  public function originLocation() {
    return $this->belongsTo(Location::class, 'origin_location_id');
  }

  public function movements() {
    return $this->hasMany(PackageMovement::class, 'package_id');
  }


  /**
   * Get the next location for the package.
   *
   * @return Node|null
   * @throws Exception
   */
  /**
   * Get the next location for the package.
   *
   * @return Node|null
   * @throws Exception
   */
  public function getNextLocation(): ?Node {
    // Get the current location node
    $currentNode = $this->getCurrentLocation();
    if (!$currentNode) {
      throw new Exception('Current location not found.');
    }

    // Get the movements from the database
    $movements = $this->movements()->orderBy('id')->get();
    if ($movements->isEmpty()) {
      throw new Exception('No movements found for this package.');
    }

    // Find the current movement
    $currentMovement = $movements->firstWhere('current_node_id', $currentNode->getID());
    if (!$currentMovement) {
      throw new Exception('Current movement not found.');
    }

    // Get the next movement
    $nextMovement = $this->movements()->find($currentMovement->next_movement);
    if (!$nextMovement) {
      throw new Exception('No next movement found.');
    }

    // Find the next router node
    $routerNode = RouterNodes::find($nextMovement->current_node_id);
    if (!$routerNode) {
      throw new Exception('Next router node not found.');
    }

    // Convert RouterNodes to Node
    $node = new Node(
      $routerNode->id,
      $routerNode->description,
      $routerNode->location_type,
      $routerNode->latDeg,
      $routerNode->lonDeg,
      $routerNode->isEntry,
      $routerNode->isExit
    );

    return $this->initializeNode($node, $nextMovement);
  }


  /**
   * Move the package forward one step.
   * Location A:
   * set arrival scan time & move package to this location
   * set checkin scan time
   * set checkout scan time
   * set departure scan time & move package away from this location
   *
   * Location B:
   * etc...
   *
   * @note For the first location, arrival and checkin time are automatically set as the time of route creation.
   *
   * @return void
   * @throws Exception
   */
  public function move(): void {
    DB::transaction(function () {
      $currentMovement = $this->movements()->whereNull('departure_time')->first();
      if (!$currentMovement) {
        throw new Exception('Current movement not found.');
      }

      // Set the appropriate scan time based on the current state
      if (is_null($currentMovement->arrival_time)) {
        // Set the arrival scan time
        $currentMovement->arrival_time = now();
      } elseif (is_null($currentMovement->check_in_time)) {
        // Set the check-in scan time
        $currentMovement->check_in_time = now();
      } elseif (is_null($currentMovement->check_out_time)) {
        // Set the check-out scan time
        $currentMovement->check_out_time = now();
      } elseif (is_null($currentMovement->departure_time)) {
        // Set the departure scan time
        $currentMovement->departure_time = now();

        // Attempt to get the next location
        try {
          $nextLocation = $this->getNextLocation();
          if ($nextLocation) {
            // Update the package's current location if next location exists
            $this->setCurrentLocation($nextLocation->getID());
          }
        } catch (Exception $e) {
          // If no next location is found, just set the departure time
        }
      } else {
        // All timestamps are set, refuse to work
        throw new Exception('All timestamps are already set for the current movement.');
      }

      $currentMovement->save();
    });
  }


  private function initializeNode(Node $node, PackageMovement $movement): Node {
    $node->setArrivedAt($movement->arrival_time ? new Carbon($movement->arrival_time) : null);
    $node->setCheckedInAt($movement->check_in_time ? new Carbon($movement->check_in_time) : null);
    $node->setCheckedOutAt($movement->check_out_time ? new Carbon($movement->check_out_time) : null);
    $node->setDepartedAt($movement->departure_time ? new Carbon($movement->departure_time) : null);

    return $node;
  }


  /**
   * Get the current location of the package as a Node.
   *
   * @return Node|null
   */
  public function getCurrentLocation(): ?Node {
    try {
      $location = Location::find($this->current_location_id);
      if ($location) {
        $movement = $this->movements()
          ->where('current_node_id', $location->id)
          ->orderBy('id')
          ->first();

        $node = new Node(
          $location->id,
          $location->description,
          $location->location_type,
          $location->latitude,
          $location->longitude,
          false, // Assuming locations are not entry nodes
          false  // Assuming locations are not exit nodes
        );

        return $this->initializeNode($node, $movement);
      }

      $routerNode = RouterNodes::find($this->current_location_id);
      if ($routerNode) {
        $movement = $this->movements()
          ->where('current_node_id', $routerNode->id)
          ->orderBy('id')
          ->first();

        $node = new Node(
          $routerNode->id,
          $routerNode->description,
          $routerNode->location_type,
          $routerNode->latDeg,
          $routerNode->lonDeg,
          $routerNode->isEntry,
          $routerNode->isExit
        );

        return $this->initializeNode($node, $movement);
      }
    } catch (Exception $e) {
      return null;
    }
    return null;
  }


  private function setCurrentLocation(string $location): void {
    $this->current_location_id = $location;
    $this->save();
  }


  /**
   * @return Node[]|null Array of Node objects representing chronological package movements
   * @throws InvalidCoordinateException If any input coordinates are blatantly wrong
   * @throws InvalidRouterArgumentException General bad argument exception
   * @throws NoPathFoundException No possible path found. There may not exist a route
   * @throws NodeNotFoundException Given node ID might not exists
   * @throws RouterException General router error
   */
  public function getMovements(): ?array {

    // Check cache
    if (!$this->movements()->exists()) {
      // Cache miss
      // New package movement

      /** @var Router $router */
      $router = App::make(Router::class);

      $path = $router->getPath(
        $this->getAttribute('originLocation'),
        $this->getAttribute('destinationLocation'));
      $this->commitMovements($path);
      $this->setCurrentLocation($path[0]->getID());

    }
      return $this->getMovementsFromDb();
  }


  /**
   * @param  Node[]  $path  Array of Node objects to commit as movements
   * @return void
   */
  private function commitMovements(array $path): void {
    DB::transaction(function () use ($path) {
      $previousMovement = null;
      $previousNode = null;

      foreach ($path as $i => $node) {
        $movementData = [
          'package_id' => $this->getAttribute('id'),
          'handled_by_courier_id' => null,
          'vehicle_id' => null,
          'departure_time' => null,
          'arrival_time' => null,
          'check_in_time' => null,
          'check_out_time' => null,
          'current_node_id' => $node->getID(),
          'next_movement' => null,
          'router_edge_id' => null,
        ];

        // Set arrival and check-in time for the first movement
        if ($i === 0) {
          $movementData['arrival_time'] = now();
          $movementData['check_in_time'] = now();
        }

        $movement = $this->movements()->create($movementData);

        if ($previousMovement) {
          $routerEdge = RouterEdges::where(function ($query) use ($previousNode, $node) {
            $query->where('origin_node', $previousNode->getID())
              ->where('destination_node', $node->getID())
              ->orWhere(function ($query) use ($previousNode, $node) {
                $query->where('origin_node', $node->getID())
                  ->where('destination_node', $previousNode->getID())
                  ->where('isUniDirectional', 0);
              });
          })->first();

          $routerEdgeId = $routerEdge ? $routerEdge->id : null;

          $previousMovement->update([
            'next_movement' => $movement->getAttribute('id'),
            'router_edge_id' => $routerEdgeId,
          ]);
        }

        $previousMovement = $movement;
        $previousNode = $node;
      }
    });
  }


  /**
   * @return Node[]|null
   * @note Does not check existence before query. Function is expected to only be called after internal checks.
   * @throws InvalidCoordinateException
   * @throws InvalidRouterArgumentException
   */
  private function getMovementsFromDb(): ?array {
    $movements = $this->movements()->orderBy('id')->get();
    $nodes = [];
    foreach ($movements as $movement) {
      $routerNode = RouterNodes::find($movement->current_node_id);
      if ($routerNode) {
        $node = new Node(
          $routerNode->id,
          $routerNode->description,
          $routerNode->location_type,
          $routerNode->latDeg,
          $routerNode->lonDeg,
          $routerNode->isEntry,
          $routerNode->isExit
        );
      } else {
        $location = Location::find($movement->current_node_id);
        if ($location) {
          $node = new Node(
            $location->id,
            $location->description,
            $location->location_type,
            $location->latitude,
            $location->longitude,
            false, // Assuming locations are not entry nodes
            false  // Assuming locations are not exit nodes
          );
        } else {
          continue; // Skip if neither RouterNode nor Location is found
        }
      }
      $nodes[] = $this->initializeNode($node, $movement);
    }
    return $nodes;
  }
}