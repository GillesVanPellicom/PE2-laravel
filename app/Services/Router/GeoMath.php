<?php

namespace App\Services\Router;

/**
 * A class that provides methods for handling geospatial data.
 */
class GeoMath {

  /**
   * Calculate the distance between two points on Earth using the Haversine formula.
   *
   * Compared to the spherical law of cosines algorithm:
   * - Haversine formula is slower but more accurate
   *
   * The formula used is:
   * a = sin²(Δφ / 2) + cos(φ1) * cos(φ2) * sin²(Δλ / 2)
   * c = 2 * atan2(√a, √(1-a))
   * d = r * c
   *
   * Where:
   * - φ1, φ2 are the latitudes in radians
   * - λ₁, λ₂ are the longitudes in radians
   * - Δφ is the difference in latitudes in radians
   * - Δλ is the difference in longitudes in radians
   * - r is the Earth's radius (6371 km)
   * - a is the square of half the chord length between the points
   * - c is the angular distance in radians
   * - d is the distance between the two points in km
   *
   * @param  float  $phi1  Latitude of the first point in radians
   * @param  float  $lambda1  Longitude of the first point in radians
   * @param  float  $phi2  Latitude of the second point in radians
   * @param  float  $lambda2  Longitude of the second point in radians
   * @return float           Distance between the two points in kilometers
   */
  public static function haversine(float $phi1, float $lambda1, float $phi2, float $lambda2): float {
    // Earth's radius in kilometers
    $r = 6371;

    // Calculate the differences in latitudes and longitudes:
    // Δφ = φ₂ - φ₁, Δλ = λ₂ - λ₁
    $deltaPhi = $phi2 - $phi1;
    $deltaLambda = $lambda2 - $lambda1;

    // Haversine formula:
    // a = sin²(Δφ / 2) + cos(φ1) * cos(φ2) * sin²(Δλ / 2)
    $a = pow(sin($deltaPhi / 2), 2) + cos($phi1) * cos($phi2) * pow(sin($deltaLambda / 2), 2);

    // c = 2 * atan2(√a, √(1-a))
    $c = 2 * asin(sqrt($a));

    // d = r * c
    return $r * $c;
  }

  /**
   * Calculates the distance between two points on Earth using the spherical law of cosines.
   *
   * Compared to the haversine algorithm:
   * - Spherical law of cosines is faster but less accurate
   *
   * The formula used is:
   * d = r * acos(sin φ₁ sin φ₂ + cos φ₁ cos φ₂ cos Δλ)
   *
   * Where:
   * - φ₁, φ₂ are the latitudes in radians
   * - λ₁, λ₂ are the longitudes in radians
   * - Δλ is the difference in longitudes in radians
   * - r is the Earth's radius (6371 km)
   * - d is the distance between the two points in kilometers
   *
   * @param  float  $phi1  Latitude of the first point in radians
   * @param  float  $lambda1  Longitude of the first point in radians
   * @param  float  $phi2  Latitude of the second point in radians
   * @param  float  $lambda2  Longitude of the second point in radians
   * @return float         Distance in kilometers
   */
  public static function sphericalCosinesDistance(float $phi1, float $lambda1, float $phi2, float $lambda2): float {
    // Earth's radius in kilometers
    $r = 6371;

    // Calculate the difference in longitudes: Δλ = λ₂ - λ₁
    $deltaLambda = $lambda2 - $lambda1;

    // Compute the central angle: θ = acos(sin φ₁ sin φ₂ + cos φ₁ cos φ₂ cos Δλ)
    $theta = acos(
      sin($phi1) * sin($phi2) +
      cos($phi1) * cos($phi2) * cos($deltaLambda)
    );

    // Calculate the distance: d = r * θ
    return $r * $theta;
  }
}