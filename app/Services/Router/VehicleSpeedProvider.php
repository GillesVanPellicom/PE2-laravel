<?php

namespace App\Services\Router;

use App\Services\Router\Types\VehicleType;

/**
 * Class VehicleSpeedProvider
 *
 * Provides speed information for different vehicle types.
 *
 * @package App\Services\Router
 */
class VehicleSpeedProvider {
  /**
   * Speeds in km/h for different vehicle types.
   *
   * @var array<string, float>
   */
  private array $vehicleSpeeds = [
    'Van' => 60,      // 60 km/h
    'Truck' => 50,    // 50 km/h
    'Airplane' => 800, // 800 km/h
    'Train' => 120,   // 120 km/h
    'Ship' => 30,     // 30 km/h
  ];

  /**
   * Get the speed for a specific vehicle type.
   *
   * @param  VehicleType  $vehicleType  The vehicle type
   * @return float The speed in km/h
   */
  public function getSpeed(VehicleType $vehicleType): float {
    return $this->vehicleSpeeds[$vehicleType->value] ?? $this->vehicleSpeeds[VehicleType::TRUCK->value];
  }

  /**
   * Calculate the travel time for a given distance and vehicle type.
   *
   * @param  float  $distance  The distance in kilometers
   * @param  VehicleType  $vehicleType  The vehicle type
   * @return float The travel time in hours
   */
  public function calculateTravelTime(float $distance, VehicleType $vehicleType): float {
    $speed = $this->getSpeed($vehicleType);
    return $distance / $speed;
  }
}