<?php

namespace App\Services\Router\Types;

/**
 * Enum NodeType represents the type of a node in the RouterGraph.
 *
 * @package App\Services\Router\Types
 */
enum NodeType: string {
  case PICKUP_POINT = 'PICKUP_POINT';
  case DISTRIBUTION_CENTER = 'DISTRIBUTION_CENTER';
  case AIRPORT = 'AIRPORT';
  case ADDRESS = 'ADDRESS';
}