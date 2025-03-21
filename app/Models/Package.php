<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\User;

class Package extends Model {
  use HasFactory;

  protected $primaryKey = 'id'; // Custom primary key
  protected $fillable = [
    'reference',
    'user_id',
    'origin_location_id',
    'destination_location_id',
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
      return $path;

    } else {
      // Cache hit
      return $this->getMovementsFromDb();
    }
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
        $movement = $this->movements()->create([
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
        ]);

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
        $nodes[] = $node;
      }
    }

    return $nodes;
  }
}
