<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Package;
use App\Models\Location;
use App\Models\User;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition() {
    $weightclass = WeightClass::inRandomOrder()->first();

    return [
      "reference" => "REF".$this->faker->unique()->randomNumber(8),
      "user_id" => User::inRandomOrder()->first()->id,
      "origin_location_id" => '1',
      "destination_location_id" => (string) Location::factory()->create()->id,
      "addresses_id" => 1,
      "status" => $this->faker->randomElement(["Pending", "Delivered", "Cancelled"]),
      "weight_id" => $weightclass->id,
      "weight" => round(mt_rand($weightclass->weight_min * 1000, $weightclass->weight_max * 1000) / 1000, 3),
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
