<?php

namespace App\Services\Router;

use App\Services\Router\Types\Exceptions\FailedCoordinatesFetchException;

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
   * - a = sin²(Δφ / 2) + cos(φ1) * cos(φ2) * sin²(Δλ / 2)
   * - c = 2 * atan2(√a, √(1-a))
   * - d = r * c
   *
   * Where:
   * - φ1, φ2 are the latitudes in radians
   * - λ₁, λ₂ are the longitudes in radians
   * - Δφ is the difference in latitudes in radians
   * - Δλ is the difference in longitudes in radians
   * - r is the Earth's radius (6371 km)
   * - a is the square of half the chord length between the points
   * - c is the angular distance in radians
   * - d is the distance between the two points in kilometers
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
   * - d = r * acos(sin φ₁ sin φ₂ + cos φ₁ cos φ₂ cos Δλ)
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

  /**
   * @param  string  $address
   * @return array|null
   * @throws FailedCoordinatesFetchException
   */
  public static function getCoordinates(string $address): ?array {
    $url = "https://nominatim.openstreetmap.org/search?".http_build_query([
        'q' => $address,
        'format' => 'json',
        'limit' => 1
      ]);

    $opts = [
      "http" => [
        "header" => "User-Agent: YourApp/1.0 (your@email.com)"
      ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    // Debugging output
    if ($response === false) {
      throw new FailedCoordinatesFetchException($address);
    }

    $data = json_decode($response, true);

    // Debugging output
    if (empty($data)) {
      throw new FailedCoordinatesFetchException($address);
    }
    return ['latDeg' => $data[0]['lat'], 'longDeg' => $data[0]['lon']];
  }
  /**
   * Calculates the bearing (direction) from one point to another on Earth.
   *
   * The formula used is:
   * - θ = atan2(sin Δλ ⋅ cos φ2, cos φ1 ⋅ sin φ2 − sin φ1 ⋅ cos φ2 ⋅ cos Δλ)
   *
   * Where:
   * - φ1, φ2 are the latitudes in radians
   * - λ1, λ2 are the longitudes in radians
   * - Δλ is the difference in longitudes in radians
   * - θ is the bearing in radians (0 = North, π/2 = East, π = South, 3π/2 = West)
   *
   * @param float $phi1 Latitude of the first point in radians
   * @param float $lambda1 Longitude of the first point in radians
   * @param float $phi2 Latitude of the second point in radians
   * @param float $lambda2 Longitude of the second point in radians
   * @return float Bearing in degrees (0-360, where 0 = North, 90 = East, 180 = South, 270 = West)
   */
  public static function calculateBearing(float $phi1, float $lambda1, float $phi2, float $lambda2): float {
    // Calculate the difference in longitudes: Δλ = λ₂ - λ₁
    $deltaLambda = $lambda2 - $lambda1;

    // Calculate the bearing using the formula:
    // θ = atan2(sin Δλ ⋅ cos φ2, cos φ1 ⋅ sin φ2 − sin φ1 ⋅ cos φ2 ⋅ cos Δλ)
    $y = sin($deltaLambda) * cos($phi2);
    $x = cos($phi1) * sin($phi2) - sin($phi1) * cos($phi2) * cos($deltaLambda);
    $theta = atan2($y, $x);

    // Convert from radians to degrees and normalize to 0-360
    return fmod(rad2deg($theta) + 360, 360);
  }

/**
   * Calculates the angle between three points (previous, current, next).
   * This is useful for determining if there's a turn and how sharp it is.
   *
   * The formula used is:
   * - θ = |θ₂ - θ₁|, normalized to [0; 180]° range
   *
   * Where:
   * - θ₁ is the bearing from the previous point to the current point
   * - θ₂ is the bearing from the current point to the next point
   *
   * θ₁ and θ₂ are calculated internally, refer to GeoMath::calculateBearing for details.
   *
   * @param float $phi1 Latitude of the previous point in radians (φ₁)
   * @param float $lambda1 Longitude of the previous point in radians (λ₁)
   * @param float $phi2 Latitude of the current point in radians (φ₂)
   * @param float $lambda2 Longitude of the current point in radians (λ₂)
   * @param float $phi3 Latitude of the next point in radians (φ₃)
   * @param float $lambda3 Longitude of the next point in radians (λ₃)
   * @return float Angle in degrees (0-180, where 0 = straight line, 180 = U-turn) (θ)
   */
  public static function calculateTurnAngle(float $phi1, float $lambda1, float $phi2, float $lambda2, float $phi3, float $lambda3): float {
    // Calculate bearings for the two segments
    $bearing1 = self::calculateBearing($phi1, $lambda1, $phi2, $lambda2);
    $bearing2 = self::calculateBearing($phi2, $lambda2, $phi3, $lambda3);

    // Calculate the absolute difference between the bearings
    $angle = abs($bearing2 - $bearing1);

    // Normalize to 0-180 (we only care about the sharpness of the turn, not the direction)
    if ($angle > 180) {
      $angle = 360 - $angle;
    }

    return $angle;
  }
}
