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
use App\Models\Address;
use App\Models\User;

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

    public function currentLocation()
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function movements()
    {
        return $this->hasMany(PackageMovement::class, 'package_id');
    }


  /**
   * Get the next location for the package.
   *
   * This method retrieves the next location node for the package based on its current movement.
   * It throws an exception if the current location or next movement is not found.
   *
   * @return Node|null The next location node or null if not found.
   * @throws Exception If the current location or next movement is not found.
   */
  public function getNextMovement(): ?Node {
    // No movements found
    if (!$this->movements()->exists()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

    // Get the current location node
    $currentNode = $this->getCurrentMovement();
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
   *
   * This method updates the package's movement by setting the appropriate scan times (arrival, check-in, check-out, departure).
   * If the final node is not and ADDRESS, you can keep running move() to set the final timestamps as you would, without moving.
   * If the final node is an ADDRESS, it automatically fills all timestamps in that movement with now() upon arrival.
   *
   * E.g.:
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
   * @throws Exception If the current movement is not found or all timestamps are already set.
   */
  public function move(): void {
    if (!$this->movements()->exists()) {
      throw new Exception('No movements found for this package. Generate movements first using Package::getMovements().');
    }

    DB::transaction(function () {
      $currentMovement = $this->movements()->whereNull('departure_time')->first();
      if (!$currentMovement) {
        throw new Exception('Current movement not found.');
      }

      // If the final node is an ADDRESS, set all timestamps to now()
      if ($this->getCurrentMovement()->getType() == NodeType::ADDRESS) {
        $currentMovement->arrival_time = now();
        $currentMovement->check_in_time = now();
        $currentMovement->check_out_time = now();
        $currentMovement->departure_time = now();
      } else {
        // Set the appropriate scan time based on the current state
        if (is_null($currentMovement->arrival_time)) {
          $currentMovement->arrival_time = now();
        } elseif (is_null($currentMovement->check_in_time)) {
          $currentMovement->check_in_time = now();
        } elseif (is_null($currentMovement->check_out_time)) {
          $currentMovement->check_out_time = now();
        } elseif (is_null($currentMovement->departure_time)) {
          $currentMovement->departure_time = now();

          // Attempt to get the next location
          try {
            $nextLocation = $this->getNextMovement();
            if ($nextLocation) {
              $this->setCurrentMovement($nextLocation->getID());
            }
          } catch (Exception $e) {
            // If no next location is found, just set the departure time
          }
        } else {
          throw new Exception('All timestamps are already set for the current movement.');
        }
      }

      $currentMovement->save();
    });
  }


  /**
   * Get the current location of the package as a Node.
   *
   * This method retrieves the current location of the package and converts it to a Node object.
   * It returns null if the current location is not found.
   *
   * @return Node|null The current location node or null if not found.
   * @throws Exception If movements are uninitialized
   */
  public function getCurrentMovement(): ?Node {
    if (!$this->movements()->exists()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

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
          false,
          false
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

    // Check cache
    if (!$this->movements()->exists()) {
      // Cache miss
      $this->generateMovements();
    }
    return $this->getMovementsFromDb();
  }


  /**
   * Generate movements for the package. This method is called internally by getMovements() if movements do not exist yet.
   *
   * @return void
   * @throws InvalidCoordinateException If any input coordinates are blatantly wrong.
   * @throws InvalidRouterArgumentException General bad argument exception.
   * @throws NoPathFoundException No possible path found. There may not exist a route.
   * @throws NodeNotFoundException Given node ID might not exist.
   * @throws RouterException General router error.
   * @throws Exception If movements already exist for the package.
   */
  public function generateMovements(): void {
    if ($this->movements()->exists()) {
      throw new Exception("Movements already exist for this package. Use Package::getMovements() to retrieve them.");
    }

    /** @var Router $router */
    $router = App::make(Router::class);

    $path = $router->getPath(
      $this->getAttribute('originLocation'),
      $this->getAttribute('destinationLocation'));
    $this->commitMovements($path);
    $this->setCurrentMovement($path[0]->getID());
  }


  // INTERNAL FUNCTIONS, DO NOT CALL DIRECTLY, NO EXCEPTION HANDLING


  /**
   * @param  string  $location  either Location or RouterNode ID.
   * @return void
   */
  private function setCurrentMovement(string $location): void {
    $this->current_location_id = $location;
    $this->save();
  }


  /**
   * @param  Node[]  $path  Array of Node objects to commit as movements.
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
   * @return Node[]|null Array of Node objects representing chronological package movements.
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
            false,
            false
          );
        } else {
          continue; // Skip if neither RouterNode nor Location is found
        }
      }
      $nodes[] = $this->initializeNode($node, $movement);
    }
    return $nodes;
  }

  private function initializeNode(Node $node, PackageMovement $movement): Node {
    $node->setArrivedAt($movement->arrival_time ? new Carbon($movement->arrival_time) : null);
    $node->setCheckedInAt($movement->check_in_time ? new Carbon($movement->check_in_time) : null);
    $node->setCheckedOutAt($movement->check_out_time ? new Carbon($movement->check_out_time) : null);
    $node->setDepartedAt($movement->departure_time ? new Carbon($movement->departure_time) : null);

    return $node;
  }
}