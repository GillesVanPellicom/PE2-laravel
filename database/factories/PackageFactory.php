<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Package;
use App\Models\RouterNodes;
use App\Models\Location;

class PackageFactory extends Factory {
  public function definition(): array {
    // Fetch RouterNodes based on the constraints
    $pickupPoints = RouterNodes::where('location_type', 'PICKUP_POINT')->pluck('id')->toArray();
    $dropOffPoints = RouterNodes::where('location_type', 'DROPOFF_POINT')->pluck('id')->toArray();
    $hybridPoints = RouterNodes::where('location_type', 'PICKUP_AND_DROPOFF_POINT')->pluck('id')->toArray();

    $validRouterNodes = array_merge($pickupPoints, $dropOffPoints, $hybridPoints);

    // Fetch Location IDs with 'location_type' => 'ADDRESS'
    $locationIds = Location::where('location_type', 'ADDRESS')->pluck('id')->toArray();

    // Ensure all locations are unique
    $allLocations = array_unique(array_merge($validRouterNodes, $locationIds));

    // Generate unique origin and destination IDs
    $originLocationId = $this->faker->randomElement($allLocations);
    $destinationLocationId = $this->faker->randomElement(array_diff($allLocations, [$originLocationId]));

    return [
      "reference" => "REF".$this->faker->unique()->randomNumber(8),
      "user_id" => 1,
      "origin_location_id" => $originLocationId,
      "destination_location_id" => $destinationLocationId,
      "addresses_id" => 1,
      "status" => $this->faker->randomElement(["Pending", "Delivered", "Cancelled"]),
      "weight_id" => 1,
      "delivery_method_id" => 1,
      "dimension" => $this->faker->randomElement(["30x20x15", "40x30x20", "50x40x30"]),
      "name" => $this->faker->firstName(),
      "lastName" => $this->faker->lastName(),
      "receiverEmail" => $this->faker->unique()->safeEmail(),
      "receiver_phone_number" => $this->faker->e164PhoneNumber(),
      "created_at" => $this->faker->dateTime(),
      "updated_at" => $this->faker->dateTime(),
    ];
  }
}