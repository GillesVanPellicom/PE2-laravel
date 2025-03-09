<?php

namespace Tests\Unit\Services\Router;

use PHPUnit\Framework\TestCase;
use App\Services\Router\GeoMath;

class GeoMathTest extends TestCase
{
  public function testHaversine()
  {
    $distance = GeoMath::haversine(40.7128, -74.0060, 51.5074, -0.1278); // New York to London
    $this->assertEqualsWithDelta(5570, $distance, 1);
  }

  public function testHaversineRandom()
  {
    $lat1 = mt_rand(-90, 90) + mt_rand() / mt_getrandmax();
    $lon1 = mt_rand(-180, 180) + mt_rand() / mt_getrandmax();
    $lat2 = mt_rand(-90, 90) + mt_rand() / mt_getrandmax();
    $lon2 = mt_rand(-180, 180) + mt_rand() / mt_getrandmax();
    $distance = GeoMath::haversine($lat1, $lon1, $lat2, $lon2);
    $this->assertIsFloat($distance);
  }

  public function testSphericalCosinesDistance()
  {
    $distance = GeoMath::sphericalCosinesDistance(40.7128, -74.0060, 51.5074, -0.1278); // New York to London
    $this->assertEqualsWithDelta(5570, $distance, 1);
  }

  public function testSphericalCosinesDistanceRandom()
  {
    $lat1 = mt_rand(-90, 90) + mt_rand() / mt_getrandmax();
    $lon1 = mt_rand(-180, 180) + mt_rand() / mt_getrandmax();
    $lat2 = mt_rand(-90, 90) + mt_rand() / mt_getrandmax();
    $lon2 = mt_rand(-180, 180) + mt_rand() / mt_getrandmax();
    $distance = GeoMath::sphericalCosinesDistance($lat1, $lon1, $lat2, $lon2);
    $this->assertIsFloat($distance);
  }
}