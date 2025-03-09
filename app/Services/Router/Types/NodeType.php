<?php

namespace App\Services\Router\Types;

enum NodeType: string {
  case DISTRIBUTION_CENTER = 'DISTRIBUTION_CENTER';
  case PICKUP_POINT = 'PICKUP_POINT';
  case AIRPORT = 'AIRPORT';
}