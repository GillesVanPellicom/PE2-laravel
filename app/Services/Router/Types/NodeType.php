<?php

namespace App\Services\Router\Types;

enum NodeType: string {
  case DISTRIBUTION_CENTER = 'distribution center';
  case PICKUP_POINT = 'pickup point';
  case AIRPORT = 'airport';
}