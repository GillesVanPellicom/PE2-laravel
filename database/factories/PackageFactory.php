<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "reference" => "REF".$this->faker->unique()->randomNumber(8),
            "user_id" => 1,
            "origin_location_id" => 1,
            "current_location_id" => 1,
            "destination_location_id" => 1,
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
