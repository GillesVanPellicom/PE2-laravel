<?php

namespace App\Services\Router;

/**
 * A class that provides methods for handeling geospatial data.
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
   * - φ1, φ2 are the latitudes in radians,
   * - Δφ is the difference in latitudes in radians,
   * - Δλ is the difference in longitudes in radians,
   * - r is the Earth's radius (6371 km),
   * - a is the square of half the chord length between the points,
   * - c is the angular distance in radians,
   * - d is the distance between the two points.
   *
   * @param  float  $lat1  Latitude of the first point in degrees
   * @param  float  $lon1  Longitude of the first point in degrees
   * @param  float  $lat2  Latitude of the second point in degrees
   * @param  float  $lon2  Longitude of the second point in degrees
   * @return float         Distance between the two points in kilometers
   */
  public static function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float {
    // Earth's radius in kilometers
    $r = 6371;

    // Convert deg to rad
    $phi1 = deg2rad($lat1);
    $phi2 = deg2rad($lat2);
    $deltaPhi = deg2rad($lat2 - $lat1);
    $deltaLambda = deg2rad($lon2 - $lon1);

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
   *  Compared to the haversine algorithm:
   *  - Spherical law of cosines is faster but less accurate
   *
   * The formula used is:
   * d = r * acos(sin φ₁ sin φ₂ + cos φ₁ cos φ₂ cos Δλ)
   *
   * Where:
   * - φ₁, φ₂ are the latitudes in radians,
   * - Δλ is the difference in longitudes in radians,
   * - r is the Earth's radius (6371 km).
   * - d is the distance between the two points.
   *
   * @param  float  $lat1  Latitude of the first point in degrees
   * @param  float  $lon1  Longitude of the first point in degrees
   * @param  float  $lat2  Latitude of the second point in degrees
   * @param  float  $lon2  Longitude of the second point in degrees
   * @return float         Distance in kilometers
   */
  public static function sphericalCosinesDistance(float $lat1, float $lon1, float $lat2, float $lon2): float {
    // Earth's radius in kilometers
    $r = 6371;

    // Convert latitudes from degrees to radians: φ₁ = deg2rad(lat1), φ₂ = deg2rad(lat2)
    $phi1 = deg2rad($lat1);
    $phi2 = deg2rad($lat2);

    // Convert longitudes from degrees to radians: λ₁ = deg2rad(lon1), λ₂ = deg2rad(lon2)
    $lambda1 = deg2rad($lon1);
    $lambda2 = deg2rad($lon2);

    // Calculate the difference in longitudes: Δλ = λ₂ - λ₁
    $DeltaLambda = $lambda2 - $lambda1;

    // Compute the central angle: θ = acos(sin φ₁ sin φ₂ + cos φ₁ cos φ₂ cos Δλ)
    $theta = acos(
      sin($phi1) * sin($phi2) +
      cos($phi1) * cos($phi2) * cos($DeltaLambda)
    );

    // Calculate the distance: d = r * θ
    return $r * $theta;
  }
}

