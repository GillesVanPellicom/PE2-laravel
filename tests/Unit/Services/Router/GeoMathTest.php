<?php

namespace Tests\Unit\Services\Router;

use PHPUnit\Framework\TestCase;
use App\Services\Router\GeoMath;

class GeoMathTest extends TestCase {
  public function testHaversine() {
    $distance = GeoMath::haversine(deg2rad(40.7128), deg2rad(-74.0060), deg2rad(51.5074),
      deg2rad(-0.1278)); // New York to London
    $this->assertEqualsWithDelta(5570, $distance, 1);
  }

  public function testHaversineRandom() {
    $lat1 = deg2rad(mt_rand(-90, 90) + mt_rand() / mt_getrandmax());
    $lon1 = deg2rad(mt_rand(-180, 180) + mt_rand() / mt_getrandmax());
    $lat2 = deg2rad(mt_rand(-90, 90) + mt_rand() / mt_getrandmax());
    $lon2 = deg2rad(mt_rand(-180, 180) + mt_rand() / mt_getrandmax());
    $distance = GeoMath::haversine($lat1, $lon1, $lat2, $lon2);
    $this->assertIsFloat($distance);
  }

  public function testSphericalCosinesDistance() {
    $distance = GeoMath::sphericalCosinesDistance(deg2rad(40.7128), deg2rad(-74.0060), deg2rad(51.5074),
      deg2rad(-0.1278)); // New York to London
    $this->assertEqualsWithDelta(5570, $distance, 1);
  }

  public function testSphericalCosinesDistanceRandom() {
    $lat1 = deg2rad(mt_rand(-90, 90) + mt_rand() / mt_getrandmax());
    $lon1 = deg2rad(mt_rand(-180, 180) + mt_rand() / mt_getrandmax());
    $lat2 = deg2rad(mt_rand(-90, 90) + mt_rand() / mt_getrandmax());
    $lon2 = deg2rad(mt_rand(-180, 180) + mt_rand() / mt_getrandmax());
    $distance = GeoMath::sphericalCosinesDistance($lat1, $lon1, $lat2, $lon2);
    $this->assertIsFloat($distance);
  }

  public function testGetCoordinatesValidAddress() {
    $address = "1600 Amphitheatre Parkway, Mountain View, CA";
    $expectedLat = 37.422; // Expected latitude
    $expectedLon = -122.084; // Expected longitude
    $epsilon = 0.01; // Acceptable range for comparison

    $coordinates = GeoMath::getCoordinates($address);

    $this->assertIsArray($coordinates);
    $this->assertArrayHasKey('latDeg', $coordinates);
    $this->assertArrayHasKey('longDeg', $coordinates);

    $this->assertEqualsWithDelta($expectedLat, $coordinates['latDeg'], $epsilon);
    $this->assertEqualsWithDelta($expectedLon, $coordinates['longDeg'], $epsilon);
  }
}