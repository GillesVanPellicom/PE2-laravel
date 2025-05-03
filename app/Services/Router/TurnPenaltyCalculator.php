<?php

namespace App\Services\Router;

use App\Services\Router\Types\TurnType;
use App\Services\Router\Types\VehicleType;

/**
 * Class TurnPenaltyCalculator
 *
 * Handles the calculation of turn penalties based on vehicle type and turn angle.
 *
 * @package App\Services\Router
 */
class TurnPenaltyCalculator {
  /**
   * Turn penalties in hours for regular vehicles.
   *
   * @var array<string, float>
   */
  private array $regularTurnPenalties = [
    'slight' => 0.02,    // 1.2 minutes for turns < 30 degrees
    'moderate' => 0.05,  // 3 minutes for turns between 30-60 degrees
    'sharp' => 0.1,      // 6 minutes for turns between 60-120 degrees
    'u-turn' => 0.08,    // 5 minutes for turns > 120 degrees
  ];

  /**
   * Turn penalties in hours for airplane operations.
   *
   * @var array<string, float>
   */
  private array $airplaneTurnPenalties = [
    'slight' => 0.17,    // 10 minutes for turns < 30 degrees
    'moderate' => 0.25,  // 15 minutes for turns between 30-60 degrees
    'sharp' => 0.33,     // 20 minutes for turns between 60-120 degrees
    'u-turn' => 0.33,    // 20 minutes for turns > 120 degrees
  ];

  /**
   * Calculate the turn penalty based on the angle, vehicle type, and distance.
   *
   * @param  float  $angle  The angle in degrees
   * @param  VehicleType  $vehicleType  The type of vehicle
   * @param  float  $distance  The distance in kilometers
   * @return float The turn penalty in hours
   */
  public function calculateTurnPenalty(float $angle, VehicleType $vehicleType, float $distance = 1.0): float {
    $turnType = TurnType::fromAngle($angle);

    if ($vehicleType === VehicleType::AIRPLANE) {
      $penalty = $this->airplaneTurnPenalties[$turnType->value];
    } else {
      $penalty = $this->regularTurnPenalties[$turnType->value];

      // Reduce turn penalty for very short distances (< 0.01 km)
      if ($distance < 0.01 && $vehicleType !== VehicleType::AIRPLANE) {
        $penalty = min($penalty, 0.08); // Max 5 minutes for short/zero-distance legs
      }
    }

    return $penalty;
  }

  /**
   * Get the turn type display name based on the angle.
   *
   * @param  float  $angle  The angle in degrees
   * @return string The turn type display name
   */
  public function getTurnTypeDisplayName(float $angle): string {
    return TurnType::fromAngle($angle)->getDisplayName();
  }
}