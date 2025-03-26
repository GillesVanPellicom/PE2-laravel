<?php

namespace App\Models;

use App\Services\Router\Router;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\MoveOperationType;
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

  ### Relationships

  public function user() {
    return $this->belongsTo(User::class, 'user_id');
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

  public function currentLocation() {
    return $this->belongsTo(Location::class, 'current_location_id');
  }

  public function movements() {
    return $this->hasMany(PackageMovement::class, 'package_id');
  }

  ### Public Methods


  /**
   * Get the next location for the package.
   *
   * @return Node|null The next location node or null if not found.
   * @throws Exception If movements are uninitialized or current movement not found.
   */
  public function getNextMovement(): ?Node {
    $movements = $this->movements()->orderBy('id')->get();
    if ($movements->isEmpty()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

    $currentNode = $this->getCurrentMovement();
    if (!$currentNode) {
      throw new Exception('Current location not found.');
    }

    $currentMovement = $movements->firstWhere('current_node_id', $currentNode->getID());
    if (!$currentMovement) {
      throw new Exception('Current movement not found.');
    }

    if (is_null($currentMovement->next_movement)) {
      return null;
    }

    $nextMovement = $movements->firstWhere('id', $currentMovement->next_movement);
    if (!$nextMovement) {
      return null;
    }

    $node = $this->getNodeFromId($nextMovement->current_node_id);
    if (!$node) {
      throw new Exception('No next movement found.');
    }

    return $this->initializeNode($node, $nextMovement);
  }


  /**
   * Move the package forward one step.
   *
   * @param  MoveOperationType  $operation
   * @return array [success, message]
   * @throws Exception If no movements exist.
   */
  public function move(MoveOperationType $operation): array {
    $movements = $this->movements()->orderBy('id')->get();
    if ($movements->isEmpty()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

    $currentMovement = $movements->firstWhere('current_node_id', $this->current_location_id);
    if (!$currentMovement) {
      return [false, "This package does not have a valid package movement."];
    }

    if ($operation === MoveOperationType::DELIVER) {
      return $this->deliverPackage($currentMovement);
    }

    return $this->performMovementOperation($currentMovement, $operation);
  }


  /**
   * Simulate a move without operation type.
   *
   * @throws Exception If no movements exist or current movement not found.
   */
  public function fakeMove() {
    $movements = $this->movements()->orderBy('id')->get();
    if ($movements->isEmpty()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

    $currentMovement = $movements->firstWhere('current_node_id', $this->current_location_id);
    if (!$currentMovement) {
      throw new Exception("No current movement found.");
    }

    $timestamps = ['arrival_time', 'check_in_time', 'check_out_time', 'departure_time'];

    foreach ($timestamps as $timestamp) {
      if (is_null($currentMovement->$timestamp)) {
        $currentMovement->$timestamp = now();
        $currentMovement->save();

        if ($timestamp === 'arrival_time' && is_null($currentMovement->next_movement) && $this->isAddressLocation()) {
          $currentMovement->check_in_time = now();
          $currentMovement->check_out_time = now();
          $currentMovement->departure_time = now();
          $currentMovement->save();
        }

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

    if (!is_null($currentMovement->next_movement)) {
      throw new Exception('Package did not move to next movement.');
    }
  }

  /**
   * Get the current location of the package as a Node.
   *
   * @return Node|null The current location node or null if not found.
   * @throws Exception If movements are uninitialized.
   */
  public function getCurrentMovement(): ?Node {
    if (!$this->movements()->exists()) {
      throw new Exception("No movements found for this package. Generate movements first using Package::getMovements().");
    }

    $node = $this->getNodeFromId($this->current_location_id);
    if (!$node) {
      return null;
    }

    $movement = $this->movements()->where('current_node_id', $this->current_location_id)->first();
    if (!$movement) {
      return null;
    }

    return $this->initializeNode($node, $movement);
  }

  /**
   * Get the movements of the package as an array of Node objects.
   *
   * @return Node[]|null Array of Node objects representing chronological package movements.
   * @throws InvalidCoordinateException
   * @throws InvalidRouterArgumentException
   * @throws NoPathFoundException
   * @throws NodeNotFoundException
   * @throws RouterException
   */
  public function getMovements(): ?array {
    if (!$this->movements()->exists()) {
      $this->generateMovements();
    }
    return $this->getMovementsFromDb();
  }

  /**
   * Generate movements for the package.
   *
   * @throws InvalidCoordinateException
   * @throws InvalidRouterArgumentException
   * @throws NoPathFoundException
   * @throws NodeNotFoundException
   * @throws RouterException
   */
  public function generateMovements(): void {
    if ($this->movements()->exists()) {
      return;
    }

    $router = App::make(Router::class);
    $path = $router->getPath(
      $this->getAttribute('originLocation'),
      $this->getAttribute('destinationLocation')
    );
    $this->commitMovements($path);
    $this->setCurrentMovement($path[0]->getID());
  }

  ### Private Helper Methods

  private function deliverPackage(PackageMovement $currentMovement): array {
    if (!is_null($currentMovement->next_movement)) {
      return [false, "This package has not yet reached its final destination."];
    }

    $location = Location::find($this->current_location_id);
    if (!$location || $location->location_type !== NodeType::ADDRESS) {
      return [
        false, "This package is not fit for delivery => ".($location ? $location->location_type->value : 'Unknown')
      ];
    }

    if (is_null($currentMovement->arrival_time)) {
      $now = now();
      $currentMovement->arrival_time = $now;
      $currentMovement->check_in_time = $now;
      $currentMovement->check_out_time = $now;
      $currentMovement->departure_time = $now;
      $currentMovement->save();
      return [true, "Delivery Successful"];
    }

    return [false, "This package has already been delivered"];
  }

  private function performMovementOperation(PackageMovement $currentMovement, MoveOperationType $operation): array {
    $timestamps = [
      'arrival_time' => MoveOperationType::OUT,
      'check_in_time' => MoveOperationType::IN,
      'check_out_time' => MoveOperationType::OUT,
      'departure_time' => MoveOperationType::IN,
    ];

    foreach ($timestamps as $timestamp => $expectedOperation) {
      if (is_null($currentMovement->$timestamp)) {
        if ($operation === $expectedOperation) {
          $currentMovement->$timestamp = now();
          $currentMovement->save();

          if ($timestamp === 'departure_time' && !is_null($currentMovement->next_movement)) {
            $nextMovement = $this->movements()->find($currentMovement->next_movement);
            if ($nextMovement) {
              $this->current_location_id = $nextMovement->current_node_id;
              $this->save();
            }
          }

          return [true, "Package successfully scanned ".$operation->value];
        }
        return [
          false, "This package was not previously scanned ".($operation === MoveOperationType::OUT ? "in" : "out")."."
        ];
      }
    }

    if (is_null($currentMovement->next_movement)) {
      return [false, "Package has reached its final destination."];
    }

    throw new Exception('All timestamps are already set for the current movement.');
  }

  private function isAddressLocation(): bool {
    $location = Location::find($this->current_location_id);
    return $location && $location->location_type === NodeType::ADDRESS;
  }

  private function setCurrentMovement(string $location): void {
    $this->current_location_id = $location;
    $this->save();
  }

  private function commitMovements(array $path): void {
    DB::transaction(function () use ($path) {
      $previousMovement = null;
      $previousNode = null;

      foreach ($path as $i => $node) {
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

          $previousMovement->update([
            'next_movement' => $movement->id,
            'router_edge_id' => $routerEdge ? $routerEdge->id : null,
          ]);
        }

        $previousMovement = $movement;
        $previousNode = $node;
      }
    });
  }

  /**
   * @throws InvalidRouterArgumentException
   * @throws InvalidCoordinateException
   */
  private function getMovementsFromDb(): ?array {
    $movements = $this->movements()->orderBy('id')->get();
    $nodes = [];
    foreach ($movements as $movement) {
      $node = $this->getNodeFromId($movement->current_node_id);
      if ($node) {
        $nodes[] = $this->initializeNode($node, $movement);
      }
    }
    return $nodes;
  }

  /**
   * @throws InvalidRouterArgumentException
   * @throws InvalidCoordinateException
   */
  private function getNodeFromId($id): ?Node {
    $routerNode = RouterNodes::find($id);
    if ($routerNode) {
      return new Node(
        $routerNode->id,
        $routerNode->description,
        $routerNode->location_type,
        $routerNode->latDeg,
        $routerNode->lonDeg,
        $routerNode->isEntry,
        $routerNode->isExit
      );
    }

    $location = Location::find($id);
    if ($location) {
      return new Node(
        $location->id,
        $location->description,
        $location->location_type,
        $location->latitude,
        $location->longitude,
        false,
        false
      );
    }

    return null;
  }

  private function initializeNode(Node $node, PackageMovement $movement): Node {
    $node->setArrivedAt($movement->arrival_time ? new Carbon($movement->arrival_time) : null);
    $node->setCheckedInAt($movement->check_in_time ? new Carbon($movement->check_in_time) : null);
    $node->setCheckedOutAt($movement->check_out_time ? new Carbon($movement->check_out_time) : null);
    $node->setDepartedAt($movement->departure_time ? new Carbon($movement->departure_time) : null);
    return $node;
  }
}