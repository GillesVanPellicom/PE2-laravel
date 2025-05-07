<?php

namespace App\Services\Router\Types;

/**
 * Enum VehicleType represents the type of vehicle used for transportation.
 *
 * @package App\Services\Router\Types
 */
enum VehicleType: string {
  case VAN = 'Van';
  case TRUCK = 'Truck';
  case AIRPLANE = 'Airplane';
  case TRAIN = 'Train';
  case SHIP = 'Ship';
}