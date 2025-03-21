<?php

namespace App\Services\Router\Types;

/**
 * Enum LocationType represents the type of a location in the DB.
 *
 * @package App\Services\Router\Types
 */
enum LocationType: string {
  case ADDRESS = 'ADDRESS';
  case PICKUP_POINT = 'PICKUP_POINT';
  case DROPOFF_POINT = 'DROPOFF_POINT';
}